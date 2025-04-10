Simple CRUD Application with PHP and MySQL
A basic CRUD (Create, Read, Update, Delete) application built with PHP and MySQL for managing users, entries, and audit logs.
Features

User authentication with session management
Dashboard with statistics and data visualization
Complete CRUD operations for entries (account transactions)
User management system
Comprehensive audit logging system
Responsive design using Bootstrap

Requirements

PHP 7.0 or higher
MySQL 5.6 or higher
Web server (Apache, Nginx, etc.)

Installation

Clone or download this repository to your web server directory
Create a database named simple_crud in MySQL
Import the database structure using the SQL code below:

sql-- Create Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Entries table
CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(100) NOT NULL,
    narration TEXT,
    currency VARCHAR(10) NOT NULL,
    credit DECIMAL(10, 2) DEFAULT 0.00,
    debit DECIMAL(10, 2) DEFAULT 0.00,
    user_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create Audit table
CREATE TABLE audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    old_value TEXT,
    new_value TEXT,
    user_id INT,
    entry_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name) VALUES ('admin', '0192023a7bbd73250516f069df18b500', 'Administrator');

Configure the database connection in config/Database.php:

private $host = 'localhost';  // Your database host
private $username = 'root';   // Your database username
private $password = '';       // Your database password
private $database = 'simple_crud';

Access the application by navigating to the project's URL in your web browser

Project Structure
simple_crud/
├── config/
│   └── Database.php
├── models/
│   ├── User.php
│   ├── Entry.php
│   └── Audit.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── css/
│   └── style.css
├── js/
│   └── script.js
├── entries/
│   ├── list.php
│   ├── add.php
│   ├── edit.php
│   └── delete.php
├── users/
│   ├── list.php
│   ├── add.php
│   ├── edit.php
│   └── delete.php
├── audit/
│   └── list.php
├── index.php
├── dashboard.php
├── logout.php
└── register.php

Usage

Login with the default credentials:

Username: admin
Password: admin123


Navigate the application:

Dashboard: View statistics and charts
Entries: Manage financial entries
Users: Manage user accounts
Audit Logs: View system changes


Add new entries to populate the dashboard charts and statistics

Security Notes

The application uses MD5 for password hashing for simplicity. For production environments, use PHP's password_hash() function.
The application uses basic session-based authentication. Additional security measures would be needed for production.

Customization

Modify the CSS in css/style.css to change the application's appearance
Add additional fields to the database tables as needed
Extend functionality by adding new features to the existing structure

Credits
This application was created as a demonstration of basic CRUD functionality using PHP and MySQL with object-oriented programming principles.
