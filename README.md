# Baboucha Project

## Description

This project is a web application for managing articles, categories, subcategories, and user accounts. It includes features such as user registration, login, password reset, and article management.

## Project Structure

## Database

The database schema is defined in the `ecom.sql` file located in the `auth/assets/DataBase/` directory. It includes tables for articles, categories, subcategories, users, favorites, and password resets.

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/Hatemfer/Baboucha.git
   ```
2. Navigate to the project directory:
   ```sh
   cd Baboucha
   ```
3. Install dependencies using Composer:
   ```sh
   composer install
   ```
4. Import the database schema:
   ```sh
   mysql -u yourusername -p yourpassword ecom < auth/assets/DataBase/ecom.sql
   ```

## Usage

1. Start your local server (e.g., Apache, Nginx).
2. Open your browser and navigate to the project URL (e.g., `http://localhost/Baboucha/auth/dashboard.php`).

## Dependencies

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - A full-featured email creation and transfer class for PHP.

## License

This project is licensed under the MIT License.

## Authors

- Hatem Ferjeni - [Hatemfer](https://github.com/Hatemfer)

## Technologies Used

- **Language**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Libraries**: PHPMailer

## Major Features

- User registration and login
- Password reset functionality
- Article management (add, edit, delete)
- Category and subcategory management
- User profile management
- Favorite articles functionality
