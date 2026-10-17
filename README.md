# LD Embroidery – WordPress Theme

Lightweight, WooCommerce-ready theme for **LD Embroidery** (embroidery digitizing).
**Phase 1:** homepage + reusable header/footer. **Phase 2:** Order (artwork upload), Services, Portfolio and Contact pages.

```
style.css / functions.php          theme header + loader
header.php / footer.php            reusable header & footer (all pages)
front-page.php                     homepage → template-parts/home/*.php
  template-parts/home/hero.php       hero banner
  template-parts/home/trust.php      trust badges (hidden by default)
  template-parts/home/services.php   4 service cards
  template-parts/home/products.php   newest WooCommerce products (auto-hidden when none)
  template-parts/home/offer.php      launch offer + file formats (hidden by default)
inc/customizer.php                 all editable homepage content + defaults
inc/woocommerce.php                cart count, account link, cart fragments, payment methods
inc/template-tags.php              CTA link resolution, logo, fallback menu, hero image
inc/icons.php                      inline SVG icons
inc/admin.php                      "theme setup" checklist on Dashboard
page-order.php / page-services.php / page-portfolio.php / page-contact.php   inner pages (chosen by page slug)
inc/content-types.php              Portfolio + Services post types (wp-admin menus)
inc/requests.php                   Order & Contact form handling, private artwork storage, "Requests" inbox
assets/css/pages.css, assets/js/pages.js   inner-page styles/scripts (loaded only on those pages)
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

## Inner pages (phase 2)

**Create the pages:** Dashboard → *LD Embroidery theme setup* → **Create missing pages** (creates `services`, `portfolio`, `order`, `contact`; the template is picked from the slug). Then add them to **Appearance → Menus → Primary Menu**. Page headings come from each page's title.

| Page | How to manage it |
|---|---|
| **Order** (`/order/`) | Real form: drag & drop or choose a PNG/JPG/SVG/PDF (max 10 MB; type is verified on the server, not just by extension). Categories and embroidery types: *Customize → LD Page: Order / Upload*. Links like `/order/?category=Hoodie` preselect the dropdown. |
| **Services** (`/services/`) | *wp-admin → Services*: title, short description (excerpt), and in the side box choose Group (Apparel / Software), icon, price label and link. Starter cards are shown until you publish your first Service. |
| **Portfolio** (`/portfolio/`) | *wp-admin → Portfolio*: set the **Featured image**, a **Portfolio Category** (these become the filter buttons) and the side-box subtitle / price / badge. Hover → Quick View. |
| **Contact** (`/contact/`) | *Customize → LD Page: Contact*: phone, WhatsApp, email, address, hours, social links. **Any empty item is hidden** — nothing is shown until you enter it. |

### Where submissions go
- Every order/message is saved under **wp-admin → Requests** and emailed to *Customize → LD Page: Order → "Send new orders & messages to"* (default: the WordPress admin email).
- Uploaded artwork is **not** in the public Media Library. It is stored in `wp-content/uploads/ldemb-private/` under a random name (blocked from direct web access by `.htaccess` on Apache/LiteSpeed) and can only be downloaded from the request screen by a logged-in editor/admin. Deleting a request deletes its file.
- Spam protection: nonce, hidden honeypot field and a limit of 5 submissions per 10 minutes per visitor.
- **Check email delivery on the live site** (submit a test order). If mail doesn't arrive, install an SMTP plugin; requests are still saved in *Requests*.
- On **nginx** hosting the `.htaccess` rule has no effect — add `location ~ ^/wp-content/uploads/ldemb-private/ { deny all; }`.
- The 10 MB limit also needs `upload_max_filesize` and `post_max_size` of at least 10 MB in your PHP settings (Hostinger → PHP Configuration).

## Homepage products & importing the catalog

The homepage shows the newest published WooCommerce products between *Services* and the offer banner (*Customize → LD: Home Products*: heading, count, optional category slug, button texts, or switch it off). It needs WooCommerce, products with a price, and hides itself otherwise.

**Import (WooCommerce → Products → Import):** upload the prepared CSV files one at a time (≈250 products each; images are fetched from the URLs in the file, so each file can take a few minutes — if it times out, import again; do not tick "Update existing products" for the first run).
- The old export only had a *Sale price*; WooCommerce then imports products **without any price**. Prepared files use *Regular price* instead.
- Products with no price or no download file are imported as **drafts**, so customers can't buy an empty download.
- Downloadable files must be reachable by this site. If the shop moves to a new server, copy `wp-content/uploads/woocommerce_uploads/` first.
- Don't commit the CSV files to this repository (they contain paid download links).
