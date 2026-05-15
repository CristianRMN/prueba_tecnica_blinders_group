# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PrestaShop 1.7.8.11 module ("Product Badges") that lets administrators create styled badges and assign them to products. Technical test for Blinders Group. Author: Cristian Regueiro Martínez.

## Development Environment

```bash
# Start PrestaShop + MySQL via Docker
docker-compose up -d

# PrestaShop accessible at http://localhost:8085 (configurable via APP_PORT in .env)
# MySQL on port 3307
```

The module directory `./productbadges` is volume-mounted into the container at `/var/www/html/modules/productbadges`. Changes to module files are reflected immediately.

No build step, test framework, or linter is configured. Testing is manual through the PrestaShop admin panel.

## Architecture

This is a standard PrestaShop 1.7 module following the ObjectModel + AdminController + Hook pattern:

- **`productbadges/productbadges.php`** — Module entry point. Handles install/uninstall (DB tables, hooks, admin tab). Registers two hooks:
  - `displayAdminProductsMainStepRightColumnBottom` — renders badge assignment UI on the product edit page
  - `actionProductSave` — intended to persist product-badge associations (currently empty)

- **`productbadges/classes/ProductBadges.php`** — `Badge` ObjectModel mapped to `ps_badge` table. Fields: name, background_color, text_color, position (left/right), active.

- **`productbadges/controllers/admin/AdminProductBadgesController.php`** — CRUD admin controller for badge management (list + form views).

- **`productbadges/sql/Install.php` / `Uninstall.php`** — Raw SQL for creating/dropping `ps_badge` and `ps_product_badge` (junction table linking products to badges).

- **`productbadges/views/templates/admin/product_badges.tpl`** — Smarty template for the badge assignment widget on the product edit page. UI is in Spanish.

## Known Issues

- Template (`product_badges.tpl`) references `$badge.color_bg` and `$badge.color_text` but the ObjectModel defines `background_color` and `text_color` — variable name mismatch.
- `hookActionProductSave()` is empty — product-badge association saving is not yet implemented.
- No client-side JS files exist yet for the badge add/remove UI interactions in the template.
