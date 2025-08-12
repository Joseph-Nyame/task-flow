TaskFlow - Laravel Task Management System
TaskFlow is a modern task management application built with Laravel that features:

Drag-and-drop task prioritization
Project organization
Clean, responsive interface
Real-time priority updates

Features
Task Management

Create, edit, and delete tasks
Drag-and-drop reordering with automatic priority updates
Task details with timestamps

Project Organization

Group tasks by projects
Filter tasks by project
Project management CRUD operations

User Experience

Intuitive interface
Visual feedback during drag operations
Responsive design for all devices

Requirements

PHP 8.0+
Composer
MySQL 5.7+
Node.js 14+ (for frontend assets)
NPM or Yarn

Installation

Clone the repository

git clone https://github.com/yourusername/task-flow.git
cd task-flow


Install PHP dependencies

composer install


Install JavaScript dependencies

npm install


Create environment file

cp .env.example .env


Generate application key

php artisan key:generate


Configure databaseEdit .env file with your database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskmaster
DB_USERNAME=root
DB_PASSWORD=


Run migrations

php artisan migrate


Compile assets

npm run dev


Start development server

php artisan serve
