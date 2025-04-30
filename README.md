# Laptop Service Job Management System

A comprehensive web-based solution for managing computer and laptop service center jobs, built with Laravel and MySQL.

## Features

- **Authentication and Roles**: Admin and Technician roles with appropriate permissions
- **Customer Management**: Add, edit, view customer details and job history
- **Job Management**: Complete job lifecycle from intake to delivery
- **Technician Assignment**: Assign jobs to technicians and track progress
- **Job Notes**: Internal communication system for each job
- **Printable Job Notes**: Generate professional PDFs for customers
- **Reporting**: Generate service reports with filtering options
- **Dashboard**: Overview of service center operations with key metrics

## System Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js & NPM

## Installation

1. Clone the repository
```
git clone https://github.com/yourusername/laptop-service-job.git
cd laptop-service-job
```

2. Install PHP dependencies
```
composer install
```

3. Install and compile frontend assets
```
npm install
npm run build
```

4. Configure your environment
```
cp .env.example .env
php artisan key:generate
```

5. Configure your database connection in the `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laptop_service_job
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations and seed the database
```
php artisan migrate
php artisan db:seed
```

7. Start the development server
```
php artisan serve
```

8. Access the application at `http://localhost:8000`

## Default Credentials

After seeding the database, you can log in with these default users:

- **Admin**
  - Email: admin@example.com
  - Password: password

- **Technician**
  - Email: tech@example.com
  - Password: password

## Usage Guide

### Dashboard
The dashboard provides an overview of your service center's operations, including:
- Job status counts
- Recent jobs
- Quick links to create new jobs and customers

### Customers
- Add new customers
- View customer details and job history
- Edit customer information

### Jobs
- Create new service jobs
- Assign jobs to technicians
- Update job status and add technical notes
- Track repair progress and parts used
- Generate printable job notes for customers

### Reports
- Generate service reports for any date range
- Filter by status, technician, or other criteria
- Export reports as PDF

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support or inquiries, please open an issue on this repository.
