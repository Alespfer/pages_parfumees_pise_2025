<!-- Language Navigation -->
<div align="right">
  <b><a href="./README.md">English</a></b> | <a href="./README_fr.md">Français</a> | <a href="./README_es.md">Español</a>
</div>

# Les Pages Parfumées - A PHP E-commerce Website

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
![Language](https://img.shields.io/badge/Language-PHP-8892BF)
![Database](https://img.shields.io/badge/Database-MySQL-4479A1)
![Tech](https://img.shields.io/badge/Tech-Docker-2496ED)

"Les Pages Parfumées" (The Scented Pages) is a fully functional e-commerce website built from scratch as a university project. It simulates a fictional online store selling second-hand books, artisanal candles, and curated gift sets. The entire application is developed using **vanilla PHP**, following a modular structure, and offers two setup options: a traditional local server environment (WampServer, MAMP) or a containerized setup with **Docker**.

![Homepage Screenshot](img/homepage.png)

## Table of Contents

- [About The Project](#about-the-project)
- [Key Features](#key-features)
- [Technical Stack](#technical-stack)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [License](#license)
- [Contact](#contact)

## About The Project

This project was designed to demonstrate proficiency in backend web development using core PHP, without relying on a framework like Laravel or Symfony. The goal was to build a complete, secure, and functional e-commerce platform encompassing both customer-facing and administrative functionalities.

## Key Features

The application is divided into two main parts: the public storefront and the admin dashboard.

### 🛍️ Customer-Facing Features
*   **Product Catalogue:** Browse products by category (books, candles, gift sets) with advanced filtering (genre, price, scent, etc.) and sorting options.
*   **Detailed Product Pages:** View product details, images, descriptions, and customer reviews.
*   **Customer Reviews:** Logged-in users can rate and review products.
*   **Shopping Cart:** Add, update, and remove items from the cart.
*   **Secure Checkout Process:** A simulated but complete checkout flow, including address selection and payment information validation.
*   **Full User Authentication:** Secure registration, login, logout, and password reset functionality.
*   **User Account Dashboard:** Users can manage their personal information, shipping addresses, view order history, and handle product returns.

### ⚙️ Administrative Backend
*   **Secure Admin Login:** A separate authentication system for administrators.
*   **Product Management (CRUD):** Full Create, Read, Update, and Delete capabilities for all product types (books, candles, gift sets) through dedicated forms.
*   **Auditing:** A logging system (`audit_logs` table) tracks all significant changes made in the backend.

## Technical Stack

*   **Backend:** **PHP 8.2+** (procedural and functional approach, no frameworks)
*   **Database:** **MySQL**
*   **Frontend:** HTML5, CSS3, vanilla JavaScript
*   **Development Environments:** WampServer / MAMP / XAMPP, or **Docker**.

## Getting Started

You can run this project using a traditional local server stack or Docker.

### Option 1: Using a Local Server (WampServer, MAMP, XAMPP)

This is the recommended method if you are familiar with local PHP development environments.

1.  **Prerequisites:**
    *   A local server environment like [WampServer](https://www.wampserver.com/), MAMP, or XAMPP installed and running.
    *   Access to phpMyAdmin or another MySQL client.

2.  **Clone the Repository:**
    ```bash
    git clone https://github.com/Alespfer/alespfer-pages_parfumees_pise_2025.git
    ```

3.  **Place Project Files:**
    Move the cloned project folder into the `www` directory of your WampServer installation (or `htdocs` for XAMPP/MAMP).

4.  **Database Setup:**
    *   Start your local server and open **phpMyAdmin**.
    *   Create a new database and name it `ecommerce`.
    *   Select the newly created `ecommerce` database.
    *   Go to the "Import" tab.
    *   Click "Choose File" and select the `docs/database.sql` file from this project.
    *   Click "Execute" at the bottom of the page to run the script. This will create all the tables and populate them with sample data.

5.  **Configuration:**
    *   Open the file `parametrage/param.php`.
    *   Ensure the database credentials match your local setup. The default for WampServer is usually correct (`DB_USER` = 'root', `DB_PASSWORD` = '').

6.  **Access the Application:**
    Open your browser and navigate to `http://localhost/alespfer-pages_parfumees_pise_2025/`.

### Option 2: Using Docker

This method uses the provided `Dockerfile` to create a self-contained environment for the PHP application. **Note:** This Docker setup runs the PHP server only; you still need a separate MySQL database running on your local machine.

1.  **Prerequisites:**
    *   [Docker](https://www.docker.com/get-started) installed and running.
    *   A MySQL server running on your local machine (not in a container).

2.  **Clone and Setup Database:**
    *   Clone the repository as described in Option 1.
    *   Follow **Step 4 (Database Setup)** from Option 1 to create and populate your `ecommerce` database on your local MySQL server.

3.  **Configuration for Docker:**
    *   Open the `parametrage/param.php` file.
    *   You must change `DB_HOST` from `'localhost'` to `'host.docker.internal'`. This special DNS name allows the Docker container to connect to services running on your host machine.
    ```php
    // In parametrage/param.php
    define('DB_HOST', 'host.docker.internal'); // For Docker setup
    ```

4.  **Build and Run the Docker Container:**
    In your terminal, at the root of the project directory, run:
    ```bash
    # Build the Docker image
    docker build -t pages-parfumees .

    # Run the container
    docker run -p 8000:10000 pages-parfumees
    ```

5.  **Access the Application:**
    Open your browser and navigate to `http://localhost:8000`.

## Project Structure

*   `*.php`: Controller/View files for each main page.
*   `/parametrage/`: Contains the global configuration file.
*   `/fonction/`: Contains all business logic and database functions.
*   `/partials/`: Reusable UI components like the header and footer.
*   `/styles/`: CSS stylesheets.
*   `/docs/`: Contains the `database.sql` dump.
*   `Dockerfile`: Defines the container for the application.

## License

This project is distributed under the MIT License. See the `LICENSE` file for more information.

## Contact

Alberto Esperon - [LinkedIn](https://www.linkedin.com/in/alberto-espfer) - [GitHub Profile](https://github.com/Alespfer)
