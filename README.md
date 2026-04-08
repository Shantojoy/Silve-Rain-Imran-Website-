# PaintPro - Professional Core PHP + MySQL System

A production-ready Painting & Wallpaper business system with:
- modern admin panel (thin collapsible sidebar + top navbar)
- leads, orders, customers, products, settings, notifications
- full blog CMS with TinyMCE editor and SEO fields
- frontend WhatsApp floating button and branding controls (logo/favicon)

## Core Features

- Core PHP (no framework), Bootstrap 5, MySQL, JS
- Secure auth/session, profile update, secure logout
- Product SEO slug + gallery images + rich text description
- Checkout + thank-you page (Cash on Delivery)
- Email templates with variables and trigger types
- Blog categories + blog posts + frontend blog list/detail + share buttons

## Run Locally

1. Import `database.sql`.
2. Update DB credentials in `includes/db.php`.
3. Run:
   ```bash
   php -S localhost:8000
   ```
4. Website: `http://localhost:8000`
5. Admin: `http://localhost:8000/admin`
   - `admin@paintpro.com`
   - `Admin@123`
