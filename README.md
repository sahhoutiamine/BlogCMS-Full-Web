📚 BlogCMS - Blog Management System
🎯 Project Overview
BlogCMS is a complete blog management system built with PHP, MySQL, and Object-Oriented Programming (OOP). It features a multi-user role system allowing visitors, authors, and administrators to interact with the platform in different ways.

✨ Features
👥 User Roles & Permissions
Visitors (No Login Required)
✅ Browse published articles

✅ Read full article content

✅ Post comments on articles

✅ Filter articles by category

Authors
✅ All visitor permissions

✅ Create new articles

✅ Set article status (Published/Draft)

✅ Delete their own articles

✅ Comment on any article

Administrators
✅ All author permissions

✅ Delete any article or comment

✅ Manage article categories (CRUD)

✅ View system statistics

✅ Monitor user activities

✅ Moderate comments (mark as spam/delete)

🏗️ System Architecture
Technology Stack
Backend: PHP 7.4+ with OOP

Database: MySQL with PDO

Frontend: HTML5, Tailwind CSS, JavaScript

Security: Prepared statements, Password hashing, XSS protection

Patterns: Singleton Pattern, Object-Oriented Design

Database Schema
sql
articles (article_id, title, content, create_date, modify_date, author_username, category_id, article_status)
categories (category_id, category_name)
comments (comment_id, content, create_date, author_username, article_id, type)
users (username, name, last_name, email, pw, create_date, role)
📁 Project Structure
text
blogcms/
├── classes/ # OOP Classes
│ ├── Database.php # Database connection (Singleton)
│ ├── User.php # User authentication & management
│ ├── Article.php # Article CRUD operations
│ ├── Category.php # Category management
│ └── Comment.php # Comment handling
├── index.php # Homepage - lists all articles
├── article.php # Single article view with comments
├── login.php # User authentication
├── logout.php # Session termination
├── admin.php # Admin control panel
├── create_article.php # Article creation form
└── blogcmsdb.sql # Database dump
🎮 OOP Implementation
Key Classes

1. Database Class (Singleton Pattern)
   Ensures single database connection instance

Secure PDO implementation

Automatic error handling

php
$db = Database::getInstance()->getConnection(); 2. User Class
Handles authentication and authorization

Session management

Role-based permissions

php
User::isAdmin(); // Check admin privileges
User::isAuthor(); // Check author privileges
User::isLoggedIn(); // Check login status 3. Article Class
Complete CRUD operations for articles

Category filtering

Author-specific operations

php
$article = new Article();
$article->create($data);     // Create article
$article->getAll(); // Get all articles
$article->delete($id); // Delete article 4. Category Class
Category management

Filtering functionality

5. Comment Class
   Comment handling

Spam detection

Article-specific comments

🔒 Security Features

1. SQL Injection Prevention
   Prepared statements with PDO

Parameter binding

Input validation

2. XSS Protection
   HTML special characters escaping

Output sanitization

Content filtering

3. Session Security
   Secure session management

Role-based access control

Login state validation

4. Data Validation
   Input sanitization

Type checking

Required field validation

🚀 Installation Guide
Prerequisites
PHP 7.4 or higher

MySQL 5.7 or higher

Apache/Nginx web server

Composer (optional)

Step-by-Step Setup
Clone the Repository

bash
git clone https://github.com/sahhoutiamine/BlogCMS-Full-Web.git
cd blogcms
Configure Database

Import blogcmsdb.sql to your MySQL database

Update database credentials in classes/Database.php

Set Up Web Server

Point your web server to the project directory

Ensure proper file permissions

Access the Application

Open browser: http://localhost/BlogCMS-Full-Web/

Use demo accounts for testing

Demo Accounts
text
Admin: admin / admin123
Author: author1 / author123

Database Admin: admin_blog / password
Database Author: jean_dupont / password
📊 Database Design
Relationships
text
users 1───┐
│
articles ─┼─── categories
│
comments ─┘
Key Tables
users: User accounts with role-based permissions

articles: Blog posts with status tracking

categories: Article classification

comments: User feedback system

🔧 Technical Highlights

1. OOP Principles Applied
   Encapsulation: Data hiding in classes

Reusability: Class methods across pages

Maintainability: Easy updates and fixes

Scalability: Simple feature additions

2. Design Patterns
   Singleton Pattern: Database connection

MVC-like Structure: Separation of concerns

Factory Pattern: Object creation

3. Performance Optimization
   Efficient database queries

Lazy loading of resources

Cached database connections

🎯 How It Works
Data Flow
text
User Request → Apache Server → PHP Processor → Database Query
↓ ↓ ↓ ↓
Browser ← HTML Output ← Data Processing ← Result Set
Authentication Flow
text
Login Form → Credentials Check → Session Creation → Role Assignment
↓ ↓ ↓ ↓
Input Database Verify $\_SESSION Set Permission Check
📱 Features in Detail
Article Management
Create, read, update, delete articles

Draft vs Published states

Category assignment

Author tracking

Comment System
Public commenting

Admin moderation

Spam filtering

Threaded discussions

Admin Dashboard
System statistics

User management

Category CRUD

Comment moderation

Frontend Features
Responsive design with Tailwind CSS

Category filtering

Real-time updates

User-friendly interface

🛠️ Development Notes
Code Organization
Classes for each entity type

Helper functions for common tasks

Separation of business logic and presentation

Consistent naming conventions

Error Handling
Try-catch blocks for database operations

User-friendly error messages

Logging for debugging

Graceful degradation

Testing Strategy
Manual testing for all user roles

Database consistency checks

Security vulnerability testing

Cross-browser compatibility

🔄 Future Enhancements
Planned Features
Article editing functionality

User registration system

Search functionality

Image upload support

Pagination for articles

Email notifications

RSS feed generation

API endpoints

Technical Improvements
Unit testing with PHPUnit

Composer dependency management

Caching implementation

Database indexing optimization

Code documentation with PHPDoc

📝 Best Practices Followed
Coding Standards
PSR-12 coding style

Descriptive variable names

Consistent indentation

Commented complex logic

Security Measures
Password hashing with bcrypt

SQL injection prevention

XSS protection

Session hijacking prevention

Performance
Optimized database queries

Efficient file structure

Minimal external dependencies

Cached repeated operations

🤝 Contribution Guidelines
For Developers
Fork the repository

Create a feature branch

Make your changes

Test thoroughly

Submit a pull request

Code Standards
Follow existing OOP structure

Add comments for new methods

Update documentation

Maintain backward compatibility

📚 Learning Outcomes
OOP Concepts Demonstrated
Class and object creation

Inheritance and polymorphism

Encapsulation and abstraction

Static vs instance methods

Web Development Skills
Full-stack PHP application

Database design and management

User authentication systems

Responsive web design

Software Engineering
Project architecture planning

Version control with Git

Documentation writing

Deployment strategies

🆘 Troubleshooting
Common Issues
Database Connection Failed

Check database credentials

Verify MySQL is running

Ensure database exists

Login Not Working

Verify session is started

Check password hashing

Confirm user exists in database

Page Not Loading

Check PHP error logs

Verify file permissions

Confirm web server configuration

Debug Mode
Enable debugging by adding to files:

php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
📄 License
This project is open-source and available under the MIT License.

🙏 Acknowledgments
Tailwind CSS for styling

PHP community for documentation

MySQL for database management

All contributors and testers

Note: This is a learning project demonstrating OOP principles in PHP web development. Suitable for educational purposes and as a foundation for more complex applications.

📞 Support
For issues, questions, or suggestions:

Check the troubleshooting guide

Review the documentation

Open a GitHub issue

Contact the maintainer
