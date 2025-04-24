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

// Serve static files with proper MIME types
app.use(express.static(path.join(__dirname, 'backend/src/public'), {
    setHeaders: (res, path) => {
        if (path.endsWith('.css')) {
            res.setHeader('Content-Type', 'text/css');
        } else if (path.endsWith('.js')) {
            res.setHeader('Content-Type', 'application/javascript');
        } else if (path.endsWith('.png')) {
            res.setHeader('Content-Type', 'image/png');
        }
    }
}));

// Route mặc định - tự động mở dashboard_admin_V1.html
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'backend/src/public/admin/dashboard_admin_V1.html'));
});

// Hiển thị banner
console.log(
    chalk.blue('┌───────────────────────────────────────────────────────────────┐\n') +
    chalk.blue('│ ') + chalk.bold('QLNS V2 - Hệ thống quản lý nhân sự') + chalk.blue(' │\n') +
    chalk.blue('├───────────────────────────────────────────────────────────────┤\n') +
    chalk.blue('│ ') + chalk.green('Server đang chạy tại: ') + chalk.yellow('http://localhost:3000') + chalk.blue(' │\n') +
    chalk.blue('└───────────────────────────────────────────────────────────────┘')
);

const PORT = process.env.PORT || 3000;

// Khởi động server
const server = app.listen(PORT, () => {
    logger.info(`Server đang chạy tại port ${PORT}`);
});

// Xử lý tắt server
process.on('SIGTERM', () => {
    logger.info('Server đang tắt...');
    server.close(() => {
        process.exit(0);
    });
});

process.on('uncaughtException', (err) => {
    logger.error('Lỗi không xử lý được', {
        error: err.message,
        stack: err.stack
    });
    process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
    logger.error('Promise bị từ chối', { reason });
}); 