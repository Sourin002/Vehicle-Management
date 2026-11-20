# AutoCare Service Hub

A complete vehicle service management website built with PHP, MySQL, HTML, and CSS. It includes customer and administrator roles, secure authentication, vehicle and service-request CRUD, status workflow, searchable listings, detail views, profile management, and server-side validation.

## Setup

1. Create/import the database: `mysql -u root -p < schema.sql`.
2. Update the credentials in `config.php` if your MySQL username/password differs.
3. Serve the directory with Apache/XAMPP or run `php -S localhost:8000` from this folder.
4. Open the local URL in your browser.

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Administrator | admin@autocare.test | password |
| Customer | customer@autocare.test | password |

New passwords are stored with PHP `password_hash()` and verified with `password_verify()`.

## Required module coverage

- **Authentication:** register, login, logout, secure password hashing.
- **Role-based access:** customer dashboard and administrator dashboard.
- **Core CRUD:** add/delete vehicles; create/view service requests; admin status and note updates.
- **Listing pages:** searchable customer requests, sortable dates/status; admin request and user tables.
- **Detail view:** separate service request detail page.
- **Status workflow:** Pending → Approved → In Progress → Completed, with Cancelled available.
- **Admin panel:** counts, all requests, status management, user role management.
- **Validation:** PHP checks for email, password, duplicate registration, ownership, valid dates, and allowed statuses.
- **Flash messages:** all important actions provide success/error feedback.
- **Profile management:** edit name, phone, and optional password change.

## Suggested production hardening

Set a strong database password, use HTTPS, configure secure cookie flags, add rate limiting and CSRF token rotation, and move secrets out of `config.php` into environment variables.
