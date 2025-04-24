const express = require('express');
const router = express.Router();

router.post('/login', (req, res) => {
    res.json({ token: 'sample-token' });
});

router.post('/logout', (req, res) => {
    res.json({ message: 'Logged out successfully' });
});

module.exports = router; 