import express from "express";
import cors from "cors";
import helmet from "helmet";
import compression from "compression";
import cookieParser from "cookie-parser";
import bodyParser from "body-parser";
import dotenv from "dotenv";
import { createLogger, format, transports } from "winston";
import { fileURLToPath } from "url";
import { dirname, join } from "path";
import rateLimit from "express-rate-limit";
import hpp from "hpp";
import swaggerUi from "swagger-ui-express";
import YAML from "yamljs";
import path from "path";

// Load environment variables
dotenv.config();

// Initialize express app
const app = express();

// Get current directory
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Body parsing middleware (must be before CSRF)
app.use(bodyParser.json({ limit: "10kb" }));
app.use(bodyParser.urlencoded({ extended: true, limit: "10kb" }));

// Cookie parser with configuration
app.use(
    cookieParser(process.env.COOKIE_SECRET || "your-secret-key", {
        path: "/",
        secure: process.env.NODE_ENV === "production",
        httpOnly: true,
        sameSite: "lax",
    })
);

// CORS configuration (must be before CSRF)
app.use(
    cors({
        origin: true, // Allow all origins in development
        credentials: true,
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        allowedHeaders: [
            "Content-Type",
            "Authorization",
            "CSRF-Token",
            "X-CSRF-Token",
            "X-Requested-With",
        ],
    })
);

// Serve static files
app.use(express.static(path.join(__dirname, "public"), {
    index: false,
    redirect: false
}));

// Configure logger with more detailed format
const logger = createLogger({
    level: "info",
    format: format.combine(
        format.timestamp({
            format: "YYYY-MM-DD HH:mm:ss",
        }),
        format.errors({ stack: true }),
        format.splat(),
        format.json()
    ),
    defaultMeta: { service: "qlnhansu-api" },
    transports: [
        new transports.File({
            filename: join("logs", "error.log"),
            level: "error",
            format: format.combine(format.timestamp(), format.json()),
        }),
        new transports.File({
            filename: join("logs", "combined.log"),
            format: format.combine(format.timestamp(), format.json()),
        }),
    ],
});

// Add console transport for development
if (process.env.NODE_ENV !== "production") {
    logger.add(
        new transports.Console({
            format: format.combine(
                format.colorize(),
                format.timestamp({
                    format: "YYYY-MM-DD HH:mm:ss",
                }),
                format.printf(({ timestamp, level, message, ...meta }) => {
                    return `${timestamp} [${level}]: ${message} ${
                        Object.keys(meta).length
                            ? JSON.stringify(meta, null, 2)
                            : ""
                    }`;
                })
            ),
        })
    );
}

// Error handling middleware
const errorHandler = (err, req, res, next) => {
    logger.error("Error:", {
        error: err.message,
        stack: err.stack,
        path: req.path,
        method: req.method,
        body: req.body,
        query: req.query,
        params: req.params,
    });

    res.status(err.status || 500).json({
        success: false,
        message: err.message || "Internal server error",
        ...(process.env.NODE_ENV === "development" && { stack: err.stack }),
    });
};

// Security middleware
app.use(
    helmet({
        contentSecurityPolicy: {
            directives: {
                defaultSrc: ["'self'", "*"],
                scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
                styleSrc: ["'self'", "'unsafe-inline'"],
                imgSrc: ["'self'", "data:", "https:"],
                connectSrc: ["'self'", "*"],
                fontSrc: ["'self'"],
                objectSrc: ["'none'"],
                mediaSrc: ["'self'"],
                frameSrc: ["'none'"],
            },
        },
        crossOriginEmbedderPolicy: false,
        crossOriginOpenerPolicy: false,
        crossOriginResourcePolicy: { policy: "cross-origin" },
        dnsPrefetchControl: true,
        frameguard: { action: "deny" },
        hidePoweredBy: true,
        hsts: { maxAge: 31536000, includeSubDomains: true, preload: true },
        ieNoOpen: true,
        noSniff: true,
        originAgentCluster: true,
        permittedCrossDomainPolicies: { permittedPolicies: "none" },
        referrerPolicy: { policy: "no-referrer" },
        xssFilter: true,
    })
);
app.use(hpp());

// Compression
app.use(compression());

// Rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 100,
    message: "Too many requests from this IP, please try again later",
});
app.use("/api/", limiter); // Only apply to API routes

// Xóa middleware CSRF nếu không cần thiết

// Swagger documentation
try {
    const swaggerDocument = YAML.load(join(__dirname, "swagger.yaml"));
    app.use("/api-docs", swaggerUi.serve, swaggerUi.setup(swaggerDocument));
    logger.info("Swagger documentation loaded successfully");
} catch (error) {
    logger.error("Failed to load Swagger documentation:", error);
}

// Routes
import authRoutes from "./routes/authRoutes.js";
import userRoutes from "./routes/userRoutes.js";
import hrRoutes from "./routes/hrRoutes.js";
import attendanceRoutes from "./routes/attendanceRoutes.js";
import salaryRoutes from "./routes/salaryRoutes.js";
import documentRoutes from "./routes/documentRoutes.js";
import trainingRoutes from "./routes/trainingRoutes.js";
import performanceRoutes from "./routes/performanceRoutes.js";
import leaveRoutes from "./routes/leaveRoutes.js";
import certificateRoutes from "./routes/certificateRoutes.js";
import bonusRoutes from "./routes/bonusRoutes.js";
import degreeRoutes from "./routes/degreeRoutes.js";
import familyRoutes from "./routes/familyRoutes.js";
import auditLogRoutes from "./routes/auditLogRoutes.js";
import parollRoutes from "./routes/parollRoutes.js";

// API Routes
app.get("/", (req, res) => {
    res.sendFile(path.join(__dirname, "public", "login_new.html"));
});

app.use("/api/auth", authRoutes);
app.use("/api/users", userRoutes);
app.use("/api/hr", hrRoutes);
app.use("/api/attendance", attendanceRoutes);
app.use("/api/salary", salaryRoutes);
app.use("/api/documents", documentRoutes);
app.use("/api/training", trainingRoutes);
app.use("/api/performance", performanceRoutes);
app.use("/api/leaves", leaveRoutes);
app.use("/api/certificates", certificateRoutes);
app.use("/api/bonuses", bonusRoutes);
app.use("/api/degrees", degreeRoutes);
app.use("/api/family", familyRoutes);
app.use("/api/audit-logs", auditLogRoutes);
app.use("/api/payroll", parollRoutes);

app.post("/api/auth/login", async (req, res) => {
    console.log("Login attempt:", req.body); // Log the received data
    // ...existing code...
});

// Error handling
app.use(errorHandler);

// Start server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    logger.info(`Server is running on port ${PORT}`);
    logger.info(`Environment: ${process.env.NODE_ENV || "development"}`);
    logger.info(`API Documentation: http://localhost:${PORT}/api-docs`);
    logger.info(
        `CORS Origin: ${process.env.CORS_ORIGIN || "http://localhost:3000"}`
    );
});
