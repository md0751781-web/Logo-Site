# LD Embroidery – WordPress Theme

Lightweight, WooCommerce-ready theme for **LD Embroidery** (embroidery digitizing).
**Phase 1:** homepage only, plus a reusable header and footer for later pages.

```
style.css / functions.php          theme header + loader
header.php / footer.php            reusable header & footer (all pages)
front-page.php                     homepage → template-parts/home/*.php
  template-parts/home/hero.php       hero banner
  template-parts/home/trust.php      trust badges (hidden by default)
  template-parts/home/services.php   4 service cards
  template-parts/home/offer.php      launch offer + file formats (hidden by default)
inc/customizer.php                 all editable homepage content + defaults
inc/woocommerce.php                cart count, account link, cart fragments, payment methods
inc/template-tags.php              CTA link resolution, logo, fallback menu, hero image
inc/icons.php                      inline SVG icons
inc/admin.php                      "theme setup" checklist on Dashboard
woocommerce.php                    wrapper so shop/product pages use the theme header/footer
assets/css/main.css, assets/js/main.js
assets/images/hero-placeholder.svg placeholder illustration – replace with the real photo
```

## Deploy (Hostinger → GitHub)

1. Branch: `main`.
2. **Deploys to root directory:** `public_html/wp-content/themes/ld-embroidery`
   (keep it inside `themes/`, otherwise the files land in `public_html` and can break WordPress).
3. Deploy, then **WordPress Admin → Appearance → Themes → LD Embroidery → Activate**.
4. Install & activate **WooCommerce** (cart icon, account icon and payment methods only appear when it is active).

## Setup checklist

After activation the Dashboard shows a **"LD Embroidery theme setup"** notice listing anything still missing.

| What | Where |
|---|---|
| **Logo** | Appearance → Customize → Site Identity → Logo (PNG/SVG/WebP, ~320×96, transparent background). Without it a text + icon fallback is shown. |
| **Hero photo** | Customize → **LD: Hero Banner → Hero image**. Landscape, at least 1400 px wide, WebP recommended. Set the image's *Alt text* in the Media Library. |
| **Menu** | Appearance → Menus → create *Home, Services, Pricing, How It Works, Portfolio, Contact* → location **Primary Menu**. The current page is shown in red. |
| **Get Quote link** | Customize → **LD: Header → Quote page URL**. If empty, a page with slug `get-quote`, `quote` or `contact` is used automatically. |
| **Upload Your Design link** | Customize → **LD: Hero Banner → Upload page URL** (or a page with slug `upload-your-design` / `upload`). |
| **$5 button** | Customize → LD: Hero Banner → *WooCommerce product ID* (links to that product page; nothing is added to the cart automatically) or a URL. |
| **Footer** | Customize → **LD: Footer**: website, email, support info (each hidden while empty). Payment methods = gateways enabled in WooCommerce → Settings → Payments. |

### Claims that need the owner's approval (OFF by default)

- **LD: Trust Badges** → "Show trust badges" — satisfaction guarantee, delivery time, customer count, "trusted by…" line.
- **LD: Launch Offer** → "Show offer banner" — $5 launch offer / limited-time wording.
- **LD: Launch Offer** → "Show supported file formats" — list only formats you really deliver.

The hero ("Just $5", "12–24 hours") and the header "24h Turnaround" badge are visible by default — edit or clear them in the Customizer if they are not accurate.

## Testing checklist

- Widths: 1920, 1440, 1280, 1024, 768, 430, 375, 320 — no horizontal scroll.
- Below 1200 px the menu collapses into the hamburger (Esc / outside click closes it).
- Add a product to the cart → the red count on the cart icon updates (WooCommerce cart fragments).
- Account icon → WooCommerce *My Account* page (hidden if that page isn't configured).
- All buttons lead to real pages; browser console has no errors.
- `prefers-reduced-motion` disables the entrance animations.
