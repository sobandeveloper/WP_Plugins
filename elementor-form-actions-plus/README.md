# Elementor Form Actions Plus

**Elementor Form Actions Plus** is a custom WordPress plugin to save Elementor Pro form entries into a database table and manage them via the WordPress admin and frontend.

---

## 🔧 Features

- ✅ Save Elementor form entries to a custom DB table
- ✅ Assign label names (e.g. "Bookings", "Quotes", etc.)
- ✅ View and manage entries in the WordPress Admin dashboard
- ✅ Elementor widget to display entries in a frontend table
- ✅ AJAX search and pagination
- ✅ Shortcode support for rendering tables outside Elementor

---

## 🧩 How It Works

1. In the Elementor form settings:
   - Enable the action: `Save to DB`
   - Set: `Enable DB Save: Yes`
   - Set a `Label Name` (used to group entries)

2. Submitted entries are stored in a custom DB table: `wp_efap_entries`

3. Use the WP admin area or Elementor widget to display/filter/export them.

---

## 📐 Shortcode (Optional)

Use this anywhere to display the entries:

```php
[efap_table label="bookings"]
