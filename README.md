# Hey there! Welcome to the Shreeyam Veda Project 👋

This folder contains the frontend website for Shreeyam Veda, as well as a lightweight Node.js backend designed to save form submissions straight into a MySQL database. 

Since you'll be handling the server and database setup, we've made this as easy as possible to get running on your end. Right now, it's configured to run perfectly on a **local MySQL setup** (like XAMPP, MAMP, or a local MySQL server) so you can test it out right away!

---

### What You'll Need
- **Node.js** installed on your machine.
- A **MySQL Server** running locally (or remotely, if you prefer).

---

### 🚀 Getting Started in 4 Easy Steps

**1. Install the packages**  
Open your terminal in this folder and run:
```bash
npm install
```
*(This grabs all the necessary Node.js libraries we used).*

**2. Add your database credentials**  
You'll see a file called `.env` in this folder. Open it up and pop in your local MySQL details. It looks like this:
```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_local_password_here
DB_NAME=shreeyam_veda
PORT=3000
```
*(If you're using something like XAMPP locally, your password might just be blank!)*

**3. Set up the database**  
You don't need to manually create the database or tables. We wrote a quick script that does it for you. Just run:
```bash
node setup-db.js
```
*(This will automatically create the `shreeyam_veda` database and a `submissions` table inside it).*

**4. Start the server!**  
Finally, fire up the backend by running:
```bash
node server.js
```
The server will now be listening on `http://localhost:3000`. You can open `index.html` in your browser, fill out the form, and watch the leads drop straight into your MySQL database!

---

### 🧠 How it works under the hood
Just for your reference, here is how the pieces connect:
- **Frontend (`index.html`)**: When the user hits submit, it sends a quick `fetch()` POST request to `/api/leads`.
- **Backend (`server.js`)**: An Express server receives that data and securely inserts it into the MySQL database. 
- **Database (`setup-db.js`)**: The table we created has these columns: `id`, `name`, `phone`, `email`, and `timestamp`.

If you have any questions or need to tweak the fields, everything is kept super simple and readable in `server.js`. Happy coding!
