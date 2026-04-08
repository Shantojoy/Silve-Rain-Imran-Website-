# PaintPro - Core PHP Painting & Wallpaper Website

Modernized Core PHP + MySQL system with a minimal admin UI, CRM, notifications, and lead/order workflows.

## Latest UI/UX Upgrades

- Thin collapsible admin sidebar (icon-first, hover-expand labels, active highlight).
- Top navbar with notification bell + profile dropdown (Profile/Logout).
- Admin profile update page (`admin/profile.php`).
- Contextual help text under form fields via reusable helper.
- WhatsApp floating button on frontend from configurable settings.

## Business Modules

- Leads, orders, customers, categories, services, products, gallery, testimonials.
- Email templates with trigger and enable/disable control.
- Order thank-you page after successful checkout.

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
