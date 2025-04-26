const express = require('express');
const path = require('path');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const helmet = require('helmet');
const compression = require('compression');
const winston = require('winston');
const chalk = require('chalk');
const figlet = require('figlet');
const mysql = require('mysql2');
const rateLimit = require('express-rate-limit');

// Cấu hình logger
const logger = winston.createLogger({
    level: 'info',
    format: winston.format.combine(
        winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }),
        winston.format.printf(({ timestamp, level, message, ...metadata }) => {
            let status = '';
            let color = 'white';
            
            switch(level) {
                case 'error':
                    status = '❌ LỖI';
                    color = 'red';
                    break;
                case 'warn':
                    status = '⚠️ CẢNH BÁO';
                    color = 'yellow';
                    break;
                case 'info':
                    status = 'ℹ️ THÔNG TIN';
                    color = 'cyan';
                    break;
                case 'debug':
                    status = '🔍 DEBUG';
                    color = 'gray';
                    break;
                default:
                    status = '📝 LOG';
                    color = 'white';
            }

            const metadataStr = Object.keys(metadata).length ? JSON.stringify(metadata) : '';
            return chalk[color](
                `┌───────────────────────────────────────────────────────────────┐\n` +
                `│ ${status.padEnd(15)} │ ${timestamp} │\n` +
                `├───────────────────────────────────────────────────────────────┤\n` +
                `│ ${message}\n` +
                (metadataStr ? `│ ${metadataStr}\n` : '') +
                `└───────────────────────────────────────────────────────────────┘\n`
            );
        })
    ),
    transports: [
        new winston.transports.File({ 
            filename: 'logs/error.log', 
            level: 'error',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            )
        }),
        new winston.transports.File({ 
            filename: 'logs/combined.log',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            )
        }),
        new winston.transports.Console()
    ]
});

const app = express();
const port = 3000;

// Middleware
app.use(helmet({
    contentSecurityPolicy: {
        directives: {
            defaultSrc: ["'self'"],
            scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'", "https://code.jquery.com", "https://cdn.jsdelivr.net", "https://cdnjs.cloudflare.com"],
            styleSrc: ["'self'", "'unsafe-inline'", "https://fonts.googleapis.com", "https://cdn.jsdelivr.net", "https://cdnjs.cloudflare.com"],
            imgSrc: ["'self'", "data:", "https://unpkg.com"],
            fontSrc: ["'self'", "https://fonts.gstatic.com", "https://cdnjs.cloudflare.com"],
            connectSrc: ["'self'", "http://localhost:*", "http://127.0.0.1:*", "https://cdn.jsdelivr.net", "ws://localhost:8080", "ws://127.0.0.1:8080"],
            workerSrc: ["'self'", "blob:"],
            frameSrc: ["'self'"],
            objectSrc: ["'none'"]
        }
    }
}));
app.use(compression());
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Cấu hình rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 phút
    max: 100 // giới hạn 100 request mỗi IP trong 15 phút
});

// Áp dụng rate limiting cho tất cả các request
app.use(limiter);

// Logging HTTP requests
app.use(morgan('combined', {
    stream: {
        write: (message) => {
            const [method, url, status, responseTime] = message.split(' ');
            logger.info('HTTP Request', {
                method,
                url,
                status: parseInt(status),
                responseTime: `${responseTime}ms`
            });
        }
    }
}));

// Configure Content Security Policy with more permissive settings
app.use((req, res, next) => {
    res.setHeader(
        'Content-Security-Policy',
        "default-src 'self' http://localhost:* http://127.0.0.1:*; " +
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " +
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " +
        "img-src 'self' data: https://unpkg.com; " +
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " +
        "connect-src 'self' http://localhost:* http://127.0.0.1:* https://cdn.jsdelivr.net ws://localhost:8080 ws://127.0.0.1:8080; " +
        "worker-src 'self' blob:; " +
        "frame-src 'self'; " +
        "object-src 'none';"
    );
    next();
});

// Serve static files from the public directory
app.use(express.static(path.join(__dirname, 'public')));

// Route for all paths - directly serve dashboard_admin_V1.html
app.get('*', (req, res) => {
    const filePath = path.join(__dirname, 'public/admin/dashboard_admin_V1.html');
    console.log('Serving dashboard_admin_V1.html for path:', req.path);
    res.sendFile(filePath);
});

// Cấu hình kết nối MySQL
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'qlnhansu'
});

// Kết nối đến MySQL
db.connect((err) => {
    if (err) {
        logger.error('Lỗi kết nối MySQL:', { error: err });
        console.error(chalk.red('Error connecting to MySQL:', err));
        return;
    }
    logger.info('Đã kết nối thành công đến MySQL');
    console.log(chalk.green('Connected to MySQL database'));
});

// API Endpoints
app.get('/api/employees', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT e.*, d.name as department_name, p.name as position_name, u.email, u.full_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching employees:', error);
        res.status(500).json({ error: 'Error fetching employees' });
    }
});

app.get('/api/departments', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT d.*, COUNT(e.id) as employee_count
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id
            GROUP BY d.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching departments:', error);
        res.status(500).json({ error: 'Error fetching departments' });
    }
});

app.get('/api/positions', async (req, res) => {
    try {
        const [rows] = await db.promise().query('SELECT * FROM positions');
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching positions:', error);
        res.status(500).json({ error: 'Error fetching positions' });
    }
});

app.get('/api/performances', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT p.*, e.employee_code, u.full_name
            FROM performances p
            LEFT JOIN employees e ON p.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching performances:', error);
        res.status(500).json({ error: 'Error fetching performances' });
    }
});

app.get('/api/payroll', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT p.*, e.employee_code, u.full_name
            FROM payroll p
            LEFT JOIN employees e ON p.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching payroll:', error);
        res.status(500).json({ error: 'Error fetching payroll' });
    }
});

app.get('/api/leaves', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT l.*, e.employee_code, u.full_name
            FROM leaves l
            LEFT JOIN employees e ON l.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching leaves:', error);
        res.status(500).json({ error: 'Error fetching leaves' });
    }
});

app.get('/api/trainings', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT t.*, e.employee_code, u.full_name
            FROM trainings t
            LEFT JOIN employees e ON t.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching trainings:', error);
        res.status(500).json({ error: 'Error fetching trainings' });
    }
});

app.get('/api/tasks', async (req, res) => {
    try {
        const [rows] = await db.promise().query(`
            SELECT t.*, e.employee_code, u.full_name
            FROM tasks t
            LEFT JOIN employees e ON t.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
        `);
        res.json(rows);
    } catch (error) {
        logger.error('Error fetching tasks:', error);
        res.status(500).json({ error: 'Error fetching tasks' });
    }
});

// Hiển thị banner
console.log(
    chalk.blue('┌───────────────────────────────────────────────────────────────┐\n') +
    chalk.blue('│ ') + chalk.bold('QLNS V2 - Hệ thống quản lý nhân sự') + chalk.blue(' │\n') +
    chalk.blue('├───────────────────────────────────────────────────────────────┤\n') +
    chalk.blue('│ ') + chalk.green('Server đang chạy tại: ') + chalk.yellow('http://localhost:3000') + chalk.blue(' │\n') +
    chalk.blue('└───────────────────────────────────────────────────────────────┘')
);

// Start the server
app.listen(port, '0.0.0.0', () => {
    logger.info(`Server đang chạy tại port ${port}`);
    console.log(chalk.green(`Server running on port ${port}`));
    console.log('Server started successfully');
    console.log('Static files are served from:', path.join(__dirname, 'public'));
    console.log('Current working directory:', process.cwd());
});

// Xử lý tắt server
process.on('SIGTERM', () => {
    logger.info('Server đang tắt...');
    app.close(() => {
        process.exit(0);
    });
});

process.on('uncaughtException', (err) => {
    logger.error('Lỗi không xử lý được', {
        error: err.message,
        stack: err.stack
    });
    console.error(chalk.red('Uncaught Exception:', err));
    process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
    logger.error('Promise bị từ chối', { reason });
    console.error(chalk.red('Unhandled Rejection:', reason));
}); 