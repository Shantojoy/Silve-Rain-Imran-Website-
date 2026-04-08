# Painting & Wallpaper Company Website (Lead-Based)

Core PHP + MySQL + Bootstrap 5 web application for a painting and wallpaper company with admin panel and lead management.

## Folder Structure

```text
/project-root
  /admin
    index.php
    logout.php
    dashboard.php
    services.php
    products.php
    gallery.php
    leads.php
    testimonials.php
  /assets
    /css/style.css
    /js/main.js
    /images
  /uploads
    /gallery
    /products
    /leads
  /includes
    db.php
    auth.php
    functions.php
    header.php
    footer.php
  database.sql
  index.php
  services.php
  gallery.php
  shop.php
  product-details.php
  contact.php
  quote.php
```

## Setup Instructions (Local)

1. Create database and tables:
   - Open phpMyAdmin or MySQL CLI
   - Import `database.sql`
2. Update DB credentials in `includes/db.php`.
3. Start local PHP server from project root:
   ```bash
   php -S localhost:8000
   ```
4. Open website: `http://localhost:8000`
5. Open admin panel: `http://localhost:8000/admin`
   - Default admin:
     - Email: `admin@paintpro.com`
     - Password: `Admin@123`

## Security & Features Included

- Prepared statements with PDO
- Session-based auth for admin
- Password verification using `password_verify()`
- Secure image upload validation (mime + size + random filename)
- Flash messages and redirect helpers
- CRUD for services, products, gallery, testimonials
- Leads table as core business model (no cart/checkout/payment)
- Product search + category filters
- Responsive Bootstrap 5 UI
