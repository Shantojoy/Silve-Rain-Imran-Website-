# PaintPro - Core PHP Painting & Wallpaper Website

Upgraded Core PHP + MySQL business website with lead management, order (COD) flow, categories, settings, and a modern responsive admin panel.

## Key Modules

- Frontend pages: home, services, gallery filter, shop search, product details slider, quote form, contact, checkout.
- Admin panel: dashboard, categories, services, products (main + gallery images), gallery, leads, orders, testimonials, email templates, settings.
- Security: PDO prepared statements, session auth, route protection, validated uploads (jpg/png + max 5MB).

## New Database Entities

- `settings`
- `categories`
- `product_images`
- `orders`
- `order_items`
- `email_templates`

Use `database.sql` for full schema.

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
