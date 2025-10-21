# MonVolEnLigne Project Summary

This document provides a comprehensive overview of the MonVolEnLigne project, a flight booking platform. It is intended to be a guide for developers and contributors.

## 1. Project Overview

MonVolEnLigne is a complete flight reservation platform built with PHP. It connects clients, travel agencies, and airlines through a centralized system, with a dedicated administration panel for platform management.

The platform supports four main user roles:

- **CLIENT**: Searches for flights and manages their bookings.
- **AGENCY**: Manages flight bookings on behalf of clients.
- **AIRLINE**: Manages their fleet, flights, and fares.
- **ADMIN**: Oversees the entire platform, including user management, approvals, and content.

The project is approximately 97% complete, with all core modules being fully functional.

## 2. Technical Stack

- **Backend**: PHP 8.x (procedural and functional style)
- **Database**: MySQL / MariaDB (schema is in `db.sql`)
- **Database Access**: PDO for secure database interactions.
- **Frontend**: HTML5, CSS3 (using modern OKLCH colors), and Vanilla JavaScript.
- **Emailing**: `PHPMailer` library (located in `src/PHPMailer-master`) for transactional emails.

## 3. Project Structure

The application follows a custom structure that separates presentation, business logic, and configuration.

- **/app/**: Contains the frontend views and dashboards for authenticated users. Each user role has its own subdirectory (`/app/admin`, `/app/agency`, etc.).
- **/src/**: The core of the application's backend logic.
  - `controllers/`: Handles form submissions, user actions, and business logic.
  - `functions/`: Contains data access functions (e.g., `client_data.php`, `admin_data.php`) that query the database, and helper functions for validation, authentication, and email.
- **/public/**: Contains public-facing pages like the landing page, login/registration forms, and contact page.
- **/config/**: Holds the database connection settings (`db.php`).
- **/uploads/**: Intended for storing generated files like PDF tickets and user avatars.
- **db.sql**: The definitive database schema. **All table and column names are in French.**

## 4. Database

The database schema is defined in `db.sql`. It is the source of truth for all data structures.

- **Naming Convention**: All tables, columns, and ENUM values are in French (e.g., `utilisateurs`, `vols`, `reservations`, `statut_actuel`).
- **Relationships**: The schema is relational with foreign keys enforcing data integrity.
- **Auditing**: Tables include tracking fields like `cree_par`, `modifie_par`, and `date_creation`.

## 5. Core Workflows & Features

### Authentication

- Managed via PHP sessions.
- Helper functions in `src/functions/auth_helpers.php`.
- A "first connection" workflow forces users to change their temporary password.
- Role-based access control is implemented by checking session variables.

### User Registration

- **Clients** can register directly.
- **Agencies** and **Airlines** submit partnership requests via forms (`demandes_agences`, `demandes_compagnies`).
- An **Admin** must approve these requests to create the corresponding user and entity (`agences` or `compagnies_aeriennes`).
- Email notifications are sent upon approval/rejection.

### Dashboards by Role

- **ADMIN**: Full oversight of users, flights, bookings, and partnership requests. Features AJAX-powered modals for viewing details without page reloads.
- **AIRLINE**: Manages fleet (`avions`), creates flights (`vols`), sets fares (`tarifs`), and views bookings on their flights.
- **AGENCY**: Searches for flights, books for clients, manages client requests, and handles cancellations.
- **CLIENT**: Searches for flights, manages personal bookings, and can request assistance from an agency.

### Email Notifications

- Handled by the `sendEmail.php` function using the PHPMailer library.
- Emails are sent for:
  - Registration confirmation.
  - Account activation (with temporary password).
  - Partnership request approvals/rejections.
  - Booking confirmations.

## 6. Coding Conventions

- **File Naming**: `kebab-case.php` (e.g., `mes-reservations.php`).
- **PHP Functions**: `snake_case()` (e.g., `get_client_stats()`).
- **PHP Variables**: `$snake_case` (e.g., `$user_id`).
- **CSS Classes**: `kebab-case` (e.g., `.vol-card`).
- **Database**: All names are in French and `snake_case`.

## 7. Current Status & Next Steps

### Project Status: 🟢 Near Completion

All four modules are functionally complete and ready for user testing.

### ❗️ Blocked Task: PDF Ticket Generation

- **Priority**: CRITICAL
- **Description**: The final key feature to be implemented is the generation of PDF tickets upon confirmed booking.
- **Blocker**: The project does not currently use `Composer` for dependency management. A PHP PDF generation library (like `TCPDF` or `FPDF`) needs to be installed via Composer.
- **Required Steps**:
  1. Initialize Composer in the project (`composer init`).
  2. Install a PDF library (`composer require tecnickcom/tcpdf`).
  3. Implement the PDF generation logic in `src/functions/`.
  4. Integrate the generation into the booking confirmation controllers.
  5. Create a secure download controller for the generated tickets.

### Future Improvements (Backlog)

- Implement HTML templates for more professional-looking emails.
- Add advanced statistical charts to dashboards (e.g., using Chart.js).
- Develop an audit trail for sensitive actions.
- Allow clients to cancel their own bookings (with conditions).
