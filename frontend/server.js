const express = require('express');
const path = require('path');
const cors = require('cors');

const app = express();
const port = 4000;

// Enable CORS
app.use(cors());

// Serve static files from both frontend and backend public directories
app.use(express.static(path.join(__dirname, 'public')));
app.use(express.static(path.join(__dirname, '..', 'backend', 'src', 'public')));

// Serve admin dashboard directly
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, '..', 'backend', 'src', 'public', 'admin', 'dashboard_admin.html'));
});

// Handle 404 - Send to admin dashboard
app.use((req, res) => {
  res.sendFile(path.join(__dirname, '..', 'backend', 'src', 'public', 'admin', 'dashboard_admin.html'));
});

app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
  console.log(`Admin dashboard available at http://localhost:${port}`);
}); 