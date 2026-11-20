LIBRARY MANAGEMENT SYSTEM - SETUP (XAMPP for macOS)

1. Copy the complete LibraryManagementSystem folder into:
   /Applications/XAMPP/xamppfiles/htdocs/

2. Start Apache and MySQL in XAMPP Manager.

3. Make sure the phpMyAdmin database exists:
   library_management

4. The project expects these tables:
   admin, books, students, issue_records

   If you already created the four tables using the earlier SQL instructions,
   do not create them again.

5. Open in Chrome:
   http://localhost/LibraryManagementSystem/

DATABASE SETTINGS:
   Host: localhost
   Username: root
   Password: (blank by default in a standard local XAMPP setup)
   Database: library_management

FEATURES:
- Dashboard with book, student, available and issued counts
- Books: Create, Read, Update, Delete and Search
- Students: Create, Read, Update, Delete and Search
- Issue books to registered students
- Return issued books and update available quantity automatically

NOTES:
- This is a beginner-level local academic project, not production-hardened software.
- Keep the folder name exactly LibraryManagementSystem.
- If your MySQL root account has a password, update config/db.php.
