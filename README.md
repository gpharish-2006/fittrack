# FitTrack Pro — BMI and Fitness Progress Tracker

FitTrack Pro is a modular, feature-separated PHP web application designed to help users calculate their Body Mass Index (BMI), log physical metrics, and analyze their fitness progress over time. 

Instead of cluttering a single interface, this application splits features into two dedicated views: a dynamic **BMI Calculator Workspace** and an analytics-driven **Live Progress and Trend Tracker**.

---

## Features

### 1. BMI Workspace (`index.php`)

* **Instant BMI Calculation:** Input name, weight (kg), and height (cm) to calculate BMI instantly.
* **Unified Workspace:** The form dynamically switches between "Add Log" and "Edit Log" modes on the same page, preventing unnecessary page redirects.
* **Database Log Table:** View, edit, and delete logged metrics.
* **Color-Coded Classification Legend:** A side-legend explaining BMI categories (< 18.5 is Underweight, 18.5–24.9 is Normal, etc.) matching the status badges in the log history.

### 2. Live Trend Tracker (`tracker.php`)

* **Key Performance Indicators (KPIs):** High-level counters for Total Active Users, Cumulative Log Entries, and Goal Distributions.
* **Overall Journey Analysis:** Automatically groups historical data chronologically per user to calculate starting vs. current weight and display net progress (e.g., *7.5 kg lost* or *+2.1 kg gained*).
* **Step-by-Step Timelines:** Visualizes chronological trend directions between successive logs, showing step-down or step-up weight indicators.

---

## Folder Structure

```text
fittrack/
│
├── db.php              # Secure MySQLi Database Connection
├── schema.sql          # Database Blueprint (DDL)
├── header.php          # Global Layout: Bootstrap 5 Navigation Header
├── footer.php          # Global Layout: Script files and footer branding
├── process.php         # Logic Controller: Processes Form Requests (C.U.D.)
│
├── index.php           # Feature Page 1: BMI Workspace & Log History
└── tracker.php         # Feature Page 2: Analytical Trend Tracker & Journey Logs
```

# FitTrack Setup Guide

## Technology Stack
* **Back-end**: PHP 8.x (using prepared statements for safe database queries)
* **Database**: MySQL
* **Front-end UI**: Bootstrap 5 (Responsive Layout and Utility Classes)

---

## Installation and Setup
Follow these steps to run the project locally using an environment like XAMPP, WAMP, or MAMP:

### 1. Place Project Files
Copy the `fittrack` project directory inside your local server's root directory (e.g., `C:/xampp/htdocs/fittrack/`).

### 2. Import the Database
1. Open your browser and navigate to phpMyAdmin (`http://localhost/phpmyadmin/`).
2. Click on the **SQL** tab at the top.
3. Copy, paste, and run the following query:

```sql
CREATE DATABASE IF NOT EXISTS fitness_db;
USE fitness_db;

CREATE TABLE IF NOT EXISTS bmi_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    weight_kg DECIMAL(5,2) NOT NULL,
    height_cm DECIMAL(5,2) NOT NULL,
    fitness_goal ENUM('Weight Loss', 'Maintenance', 'Muscle Gain') NOT NULL,
    log_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. Check Database Connection Settings
Open `db.php` and verify the credentials match your local MySQL configuration:

```php
$host = "localhost";
$user = "root";
$password = ""; // Change if you have a password set
$dbname = "fitness_db";
```

### 4. Run the Application
Open your web browser and navigate to:
```text
http://localhost/fittrack/index.php
```

---

## Best Practices Implemented
* **Prepared SQL Statements (`mysqli_prepare`)**: Prevents SQL injection vulnerabilities.
* **Post-Redirect-Get (PRG) Pattern**: Prevents accidental double form submissions on page refresh.
* **Modular Design**: Layouts, database connections, and operational processes are separated to ensure highly maintainable and readable code.
