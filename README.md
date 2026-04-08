# PaintPro Business Management System (Core PHP)

Professional Website + CRM + Invoice + Quotation + Blog CMS for Painting & Wallpaper business.

## Stack
- Core PHP (no framework)
- MySQL
- Bootstrap 5
- Vanilla JavaScript
- CKEditor 5 (free build, no API key)

## Implemented Modules
- Leads (status + notes), Customers, Orders
- Invoices, Payments, Quotations (with convert to invoice)
- Product catalog, Services, Gallery (location + rich description)
- Blog CMS (categories + blogs + SEO fields)
- Settings split support (general + invoice + quotation)
- Notifications + WhatsApp floating button

## Admin Module Structure
List/Add/Edit pages are available for major modules (products, services, gallery, blogs, customers, invoices, quotations) via dedicated files under `/admin`.

## Run Locally
1. Import `database.sql`
2. Configure database in `includes/db.php`
3. Run:
   ```bash
   php -S localhost:8000
   ```
4. Open:
   - Website: `http://localhost:8000`
   - Admin: `http://localhost:8000/admin`

Default Admin:
- Email: `admin@paintpro.com`
- Password: `Admin@123`
