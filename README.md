# Weby - Academic Resource Sharing Platform

A simple vanilla PHP platform for university students to share notes, questions, and build academic networks.

## Features

- 📝 **Share Notes** - Upload and download class notes
- ❓ **Share Questions** - Access previous exam questions
- 👤 **User Profiles** - Track your contributions
- ❤️ **Like System** - Like helpful resources
- 🔍 **Search & Filter** - Find resources by semester, year, course
- 🎨 **Modern Dark UI** - Beautiful TailwindCSS design

## Tech Stack

- **Backend:** Vanilla PHP
- **Database:** MySQL (PDO)
- **Frontend:** TailwindCSS CDN
- **Icons:** Material Icons Rounded

## Installation

### 1. Database Setup

1. Open phpMyAdmin
2. Import `database.sql`
3. Database `weby_db` will be created automatically

### 2. Configuration

The `config.php` is already configured for XAMPP:
```php
$host = 'localhost';
$dbname = 'weby_db';
$user = 'root';
$pass = '';
```

### 3. Start Server

```bash
cd c:\xampp\htdocs\Sheild\Spiderman\Weby_Vanilla
php -S localhost:8001
```

### 4. Access Application

Open browser: `http://localhost:8001`

## File Structure

```
Weby_Vanilla/
├── config.php              # Database connection
├── functions.php           # Helper functions
├── database.sql            # Database schema
├── index.php               # Landing page
├── login.php               # Login page
├── register.php            # Registration
├── logout.php              # Logout handler
├── notes.php               # Browse notes
├── questions.php           # Browse questions
├── upload-note.php         # Upload note
├── upload-question.php     # Upload question
├── profile.php             # User profile
├── view.php                # View resource
├── css/
│   └── style.css          # Custom styles
└── uploads/               # Uploaded files
    ├── notes/
    └── questions/
```

## Database Tables

- **users** - User accounts
- **notes** - Shared notes
- **questions** - Shared questions
- **likes** - Like tracking

## Usage

1. **Register** - Create an account
2. **Login** - Sign in with your credentials
3. **Browse** - View notes and questions
4. **Upload** - Share your resources
5. **Like** - Like helpful content
6. **Profile** - View your contributions

## Security Features

- Password hashing with `password_hash()`
- PDO prepared statements (SQL injection prevention)
- Session-based authentication
- File upload validation (PDF only)

## Simple Database Approach (Like EMS Pro)

- ✅ One `config.php` file for connection
- ✅ One `database.sql` file to import
- ✅ Direct PDO queries (no ORM complexity)
- ✅ Simple helper functions
- ✅ No migrations or seeders needed

## Credits

Built with simplicity in mind, following the EMS Pro database pattern.
