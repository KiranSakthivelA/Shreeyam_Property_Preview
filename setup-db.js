require('dotenv').config();
const mysql = require('mysql2/promise');

async function setupDatabase() {
    try {
        // Connect without a specific database first, in case the database doesn't exist
        console.log('Connecting to MySQL Server...');
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST,
            user: process.env.DB_USER,
            password: process.env.DB_PASSWORD
        });

        const dbName = process.env.DB_NAME;
        
        console.log(`Ensuring database '${dbName}' exists...`);
        await connection.query(`CREATE DATABASE IF NOT EXISTS \`${dbName}\``);

        console.log(`Using database '${dbName}'...`);
        await connection.query(`USE \`${dbName}\``);

        console.log('Creating `submissions` table if it does not exist...');
        const createTableQuery = `
            CREATE TABLE IF NOT EXISTS submissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                email VARCHAR(255),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        `;
        await connection.query(createTableQuery);

        console.log('Setup completed successfully!');
        await connection.end();
    } catch (err) {
        console.error('Error during setup:', err.message);
    }
}

setupDatabase();
