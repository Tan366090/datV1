const express = require('express');
const router = express.Router();

router.get('/', (req, res) => {
    res.json([{ id: 1, employeeId: 1, score: 90, period: '2024-Q1' }]);
});

module.exports = router; 