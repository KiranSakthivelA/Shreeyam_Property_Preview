require('dotenv').config();
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
// Serve static files from the current directory (for index.html)
app.use(express.static(path.join(__dirname)));

// MySQL Connection Pool
const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Test connection
pool.getConnection()
    .then(connection => {
        console.log('Connected to the MySQL database successfully!');
        connection.release();
    })
    .catch(err => {
        console.warn('Warning: Could not connect to MySQL database.');
        console.warn('Error details:', err.message);
        console.warn('Make sure to update your .env file with actual credentials once you secure hosting.');
    });

// API endpoint to handle form submissions
app.post('/api/leads', async (req, res) => {
    const { name, phone, email } = req.body;

    if (!name || !phone) {
        return res.status(400).json({ error: 'Name and phone are required.' });
    }

    let insertId = null;

    try {
        const sql = `INSERT INTO submissions (name, phone, email) VALUES (?, ?, ?)`;
        const [result] = await pool.query(sql, [name, phone, email]);
        insertId = result.insertId;
    } catch (err) {
        console.error('Error inserting data:', err.message);
        return res.status(500).json({ error: 'Failed to save submission. Check database connection.' });
    }

    // Attempt to push to Syncr CRM
    try {
        const syncrUrl = process.env.SYNCR_API_URL;
        if (syncrUrl && syncrUrl !== 'https://api.syncr.com/webhook/placeholder') {
            const payload = {
                name: name,
                phone: phone,
                email: email
            };

            const headers = {
                'Content-Type': 'application/json'
            };
            
            if (process.env.SYNCR_API_KEY && process.env.SYNCR_API_KEY !== 'your_api_key_here') {
                // The exact authorization header format depends on Syncr CRM's requirement
                headers['Authorization'] = `Bearer ${process.env.SYNCR_API_KEY}`;
            }

            const crmResponse = await fetch(syncrUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            });

            if (!crmResponse.ok) {
                console.error(`Syncr CRM responded with status: ${crmResponse.status}`);
            } else {
                console.log('Successfully pushed lead to Syncr CRM.');
            }
        }
    } catch (crmErr) {
        console.error('Error pushing data to Syncr CRM:', crmErr.message);
        // We don't return an error here because the local database insertion succeeded
    }

    res.status(201).json({ message: 'Submission saved successfully!', id: insertId });
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
