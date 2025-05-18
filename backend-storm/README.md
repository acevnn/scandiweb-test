# Scandiweb Test Project – Backend (PHP + MySQL)

This project imports structured product data from a JSON file into a MySQL database using pure PHP and PDO.

---

## Project Structure

backend-storm/
│
├── data/
│ └── data.json # Static JSON file with product data
│
├── scripts/
│ └── schema.sql # SQL script to create all database tables
│
├── src/
│ ├── Config/
│ │ └── Database.php # PDO connection class
│ └── Services/
│ └── DataImporter.php # Main logic for importing JSON to DB
│
├── bootstrap.php # Entrypoint to run the importer
├── composer.json # Autoload configuration
└── vendor/ # Composer dependencies (if needed)


---

## How to Run

1. **Import Schema**
    - Open `scripts/schema.sql` in MySQL Workbench and execute it.
    - This will create all required tables (safe to rerun due to `IF NOT EXISTS` and `DROP TABLE` ordering).

2. **Configure PHP**
    - Ensure `pdo_mysql` is enabled in your `php.ini`:
      ```ini
      extension=pdo_mysql
      ```

3. **Run Import Script**
    - From the project root:
      ```bash
      php bootstrap.php
      ```

4. **Result**
    - All categories, products, prices, attributes, and images will be imported into the MySQL database.

---

## Features

- Pure PHP with OOP
- Uses `INSERT IGNORE` to avoid duplicates
- Handles all foreign key relations properly
- Safe SQL schema with rollback-compatible drop order
- Modular code for scalability

---

## Requirements

- PHP 8+
- MySQL 8+
- Composer (optional but project is ready for it)

