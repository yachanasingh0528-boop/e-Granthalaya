# e-Granthalaya
### Smart Library Management System

**Bringing Knowledge Closer to Everyone.**

e-Granthalaya is a web-based Library Management System developed to simplify and organize daily library operations. It helps administrators manage books, student records, book issuing, and returns through a user-friendly dashboard.

## Features

- **Admin Login:** Secure administrator authentication.
- **Dashboard:** View total books, available copies, registered students, and currently issued books.
- **Book Management:** Add, view, edit, delete, and search books.
- **Student Management:** Register, view, edit, delete, and search student records.
- **Issue Books:** Issue books to registered students.
- **Return Books:** Record returned books and automatically update availability.
- **Search Functionality:** Quickly find books and student records.

## Technologies Used

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- XAMPP

## Project Structure

```text
e-Granthalaya/
│
├── assets/
│   ├── style.css
│   └── script.js
│
├── config/
│   └── db.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── index.php
├── books.php
├── students.php
├── issue_book.php
├── login.php
└── logout.php
```

## Installation and Setup

1. Install XAMPP on macOS.
2. Copy the project folder into:

   `/Applications/XAMPP/xamppfiles/htdocs/`

3. Start Apache and MySQL using XAMPP Manager.
4. Open phpMyAdmin:

   `http://localhost/phpmyadmin`

5. Create a database named `library_management`.
6. Create or import the required tables:
   - admin
   - books
   - students
   - issue_records

   If these tables already exist, do not create them again.

7. Configure the database connection in `config/db.php` according to your local MySQL settings.
8. Open the project in your browser:

   `http://localhost/LibraryManagementSystem/`

9. Log in using your administrator account.

## Database Configuration

- Host: localhost
- Username: root
- Password: Depends on local MySQL configuration
- Database: library_management

## Admin Account Setup

The project includes an administrator login system. The initial administrator account is created using the setup process provided with the project.

For security, remove the setup script after creating the administrator account.

## Future Enhancements

- Book issue and return history reports.
- Fine calculation for late returns.
- Student login portal.
- Email notifications.
- Online book reservation.
- Improved mobile responsiveness.

## Project Information

**Project Name:** e-Granthalaya  
**Project Type:** Library Management System  
**Technology:** PHP, MySQL, HTML, CSS and JavaScript  
**Development Environment:** XAMPP  
**Platform:** Web Application

---

*Bringing Knowledge Closer to Everyone.*