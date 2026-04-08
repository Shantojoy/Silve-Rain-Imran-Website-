# PaintPro Business Management System (Core PHP)

Professional Website + CRM + Invoice + Quotation + Blog CMS for a Painting & Wallpaper company.

## Stack
- Core PHP (no framework)
- MySQL
- Bootstrap 5
- Vanilla JavaScript
- TinyMCE Free CDN (rich text editor)

## Includes
- Leads + Customers + Orders
- Invoices + Payments + Quotations (convert quotation to invoice)
- Blog CMS (categories + posts + SEO fields)
- Gallery with rich description + location
- Product SEO slug + multi-image gallery
- Settings: logo, favicon, WhatsApp, invoice settings, email sender
- Notification bell + WhatsApp floating button

## Run Locally
1. Import `database.sql`
2. Configure database in `includes/db.php`
3. Run server:
   ```bash
   php -S localhost:8000
   ```
4. Open:
   - Website: `http://localhost:8000`
   - Admin: `http://localhost:8000/admin`

Default Admin:
- Email: `admin@paintpro.com`
- Password: `Admin@123`
