# PaintPro - Core PHP Painting & Wallpaper Website

Upgraded Core PHP + MySQL system with CRM, lead/order notifications, advanced settings, and improved admin UX.

## Major Upgrades

- Modern responsive admin sidebar with icons, hints, and notification bell.
- Fixed lead submission flow with robust validation and database save.
- Customer management (list + profile with order history and spending).
- Order checkout now redirects to a dedicated `thank-you.php` success page.
- Advanced email templates (trigger type + enabled/disabled + HTML body preview).
- Frontend WhatsApp floating button configurable from settings.

## New/Updated Modules

- `admin/customers.php`
- `admin/customer-view.php`
- `admin/mark-notifications-read.php`
- `thank-you.php`
- `admin/email-templates.php` (advanced)
- `admin/settings.php` (WhatsApp + email sender)

## Run Locally

1. Import `database.sql` into MySQL.
2. Update DB credentials in `includes/db.php`.
3. Start server:
   ```bash
   php -S localhost:8000
   ```
4. Open site: `http://localhost:8000`
5. Admin login: `http://localhost:8000/admin`
   - Email: `admin@paintpro.com`
   - Password: `Admin@123`
