# Lovejoy Antiques – Secure Web Application
A university assignment project built for a fictional antique valuation company, Lovejoy. The application demonstrates secure web development practices in PHP and MySQL.

## Features
- User Registration – Email, password, name, and contact number with email verification
- Secure Login – Session management, brute-force protection (lockout after 3 failed attempts), and two-factor authentication via email code
- Password Management – Strength recommendations, server-side enforcement, and secure password recovery
- Evaluation Requests – Authenticated users can submit object descriptions, preferred contact method, and photo uploads
- Admin Dashboard – Restricted evaluation list view for administrator accounts only

## Security Highlights
Passwords hashed, CSRF token protection on all forms, Prepared statements throughout to prevent SQL injection, Inputs sanitised to prevent XSS, File uploads validated by type and size, stored with unique filenames, Role-based access control for admin pages, Generic messaging on password reset to prevent user enumeration

## Tech Stack
- Backend: PHP
- Database: MySQL
- Frontend: HTML, CSS, JavaScript

All user data is fictional and for demonstration purposes only.
