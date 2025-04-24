const express = require('express');
const router = express.Router();
const leaveController = require('../controllers/leaveController');
const authMiddleware = require('../middleware/authMiddleware');
const upload = require('../middleware/uploadMiddleware');

// Get remaining leave days
router.get('/remaining-days', authMiddleware, leaveController.getRemainingDays);

// Register new leave request
router.post('/register', authMiddleware, upload.single('attachment'), leaveController.registerLeave);

// Get leave list
router.get('/list', authMiddleware, leaveController.getLeaveList);

// Get leave details
router.get('/:id', authMiddleware, leaveController.getLeaveDetails);

// Approve leave request
router.put('/:id/approve', authMiddleware, leaveController.approveLeave);

// Reject leave request
router.put('/:id/reject', authMiddleware, leaveController.rejectLeave);

// Cancel leave request
router.put('/:id/cancel', authMiddleware, leaveController.cancelLeave);

module.exports = router; 