# Bốc Lì Xì (Lucky Money) — WordPress Gamification Plugin

> **Custom WordPress Gamification & Dynamic Coupon Campaign Plugin**  
> *A promotional campaign plugin for WordPress & WooCommerce featuring shortcode/template rendering, AJAX prize draws, participation rate limiting, and WooCommerce coupon creation.*

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-Optional-96588A?style=flat-square&logo=woocommerce&logoColor=white)](https://woocommerce.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![GSAP](https://img.shields.io/badge/GSAP-Animations-88CE02?style=flat-square&logo=greensock&logoColor=black)](https://greensock.com/gsap/)

---

## 📌 Plugin Motivation & Business Use Case

In Vietnamese e-commerce and retail marketing, seasonal **Lunar New Year ("Bốc Lì Xì")** campaigns drive engagement. This plugin was built as an **independent custom WordPress plugin** to run gamified promotion campaigns.

It captures visitor details (name, email, phone), enforces a per-visitor draw limit over a sliding time window, selects a prize using admin-defined probability weights, and issues a single-use WooCommerce discount coupon when the winning prize is a coupon type.

---

## ⚙️ Core Technical Features

1. **Campaign Page Rendering**
   - A custom page template `public/template/boclixi.index.php` registered through the `theme_page_templates` and `template_include` filters.
   - A shortcode `[boclixi_page id="N"]` renders the same experience inside existing pages.
   - Front-end animations powered by **GSAP** and **ScrollTrigger**.
2. **Weighted Prize Selection**
   - Each program row stores `prize_percent` (weight) and `prize_quantity` (stock). `lm_find_winning_prize()` sums the weights, draws a random float, and walks the cumulative weights to pick a prize, skipping prizes with no stock.
   - Prize ids sent to the browser are signed: `base64(prize_id:hmac_md5(prize_id, AUTH_KEY))`, decoded with `hash_equals`, so a client cannot request a specific prize.
3. **Participation Rate Limiting**
   - Before the draw, `lm_lucky_money_user_validation_for_lucky_money()` counts previous results for the same page where the visitor's email (and, when configured, phone) match, and rejects if the count is at or above the configured limit or the most recent draw is inside the sliding window (minutes / hours / days).
4. **WooCommerce Coupon Prizes**
   - On a coupon prize, a `WC_Coupon` is created with `usage_limit = 1` and `individual_use = true`, as a percentage or fixed-cart amount, with a code from `wp_generate_password(6)`.
5. **Custom Database Schema & Admin Reporting**
   - Three tables created with `dbDelta()`: `lucky_money_program` (prize weights, stock, colors), `lucky_money_prize` (prize catalogue), `lucky_money_result` (visitor rows, `received` flag, `received_at`, status).
   - Admin pages under the **Bốc Lì Xì** menu: **Chương trình** (programmes and prizes), **Thống kê** (results with search, pagination, and a Pending → Received workflow), **Tuỳ chọn** (rate-limit settings), **Cài đặt**, and **Email** templates.
6. **Winner Email**
   - A `wp_mail()` template with configurable header image, body, and footer image.

---

## 📐 Draw Flow

```mermaid
sequenceDiagram
    autonumber
    actor Visitor as Front-End Visitor
    participant WP as WordPress Plugin (admin-ajax.php)
    participant DB as Custom MySQL Tables
    participant WC as WooCommerce Core

    Visitor ->> WP: POST { action: "lm_ajax_program_prize_win", page_id, name, email, phone }
    WP ->> WP: Validate email and phone format
    WP ->> DB: Count previous results for page + email/phone; check sliding window
    alt Limit reached
        WP -->> Visitor: JSON error "Bạn đã hết lượt tham gia"
    else Limit valid
        WP -->> Visitor: JSON success with the visitor data
        Visitor ->> WP: POST { action: "lm_ajax_program_prize_win_2", page_id, prize_id (signed) }
        WP ->> WP: Decode prize id, weighted draw, decrement stock
        alt Prize type == coupon
            WP ->> WC: Create single-use WC_Coupon
        end
        WP ->> DB: Insert result row (status pending)
        WP -->> Visitor: JSON success with prize and coupon code
    end
```

---

## 🚀 Quick Start & Installation

### Prerequisites
* WordPress 5.8+ (PHP 7.4 or 8.x).
* WooCommerce is optional; it is only required if you issue coupon prizes.

### Installation Steps

1. Clone or download the repository into your WordPress plugins folder:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/huyhhuy1410/lucky-money.git lucky-money
   ```
2. Activate **Bốc Lì Xì (Lucky Money)** via **WordPress Admin $\rightarrow$ Plugins**.
3. Create a new campaign page in WordPress and select the **"Bốc Lì Xì"** page template, or insert the shortcode:
   ```text
   [boclixi_page id="1"]
   ```
4. Configure campaign rules, prizes, and draw limits under **Bốc Lì Xì $\rightarrow$ Chương trình** and **Bốc Lì Xì $\rightarrow$ Tuỳ chọn**.

---

## 📂 Codebase Architecture

```text
lucky-money/
├── lucky_money.php           # Plugin bootstrap, constants & hook registrations
├── admin/                    # Admin menus, settings, prize CRUD & result reporting
├── includes/                 # Core classes (Program, Prize, Result, Email)
├── public/
│   ├── css/                  # Front-end styles & toasts
│   ├── js/                   # GSAP animation handlers & AJAX scripts
│   └── template/             # Custom WordPress page template (boclixi.index.php)
└── uninstall.php             # Database cleanup on plugin deletion
```

---

## ⚠️ Known Limitations

These are open items, not hidden behaviour. They are listed so a reviewer does not have to read the code to find them.

* **No CSRF protection on the AJAX endpoints.** The plugin creates a nonce (`wp_create_nonce('lucky-money-ajax-security')`) and passes it to the front-end script, but no handler calls `check_ajax_referer()` or `wp_verify_nonce()`. A logged-in administrator's browser could be induced to submit a draw on their behalf. To fix, call `check_ajax_referer('lucky-money-ajax-security', 'ajaxNonce')` at the top of each `wp_ajax_` draw handler.
* **The rate limit is not atomic.** It is a `SELECT COUNT(*)` followed by a comparison, with no lock or unique constraint around the insert. Two concurrent requests from the same visitor can both pass the check. A row-level lock, a unique `(page_id, email, slot)` key, or a Redis `SET NX` marker would be needed to make it strict.
* **Rate limiting is by email and phone only.** The `email | phone | both` setting is honoured, but there is no IP-based limiting, even though the plugin can read the client IP.
* **Coupons are not email-restricted at draw time.** The `set_email_restrictions()` call is commented out in the draw handler. A coupon created on a win is usable by anyone who obtains the code. The email restriction is only applied when an administrator marks the result as **Received** in the **Thống kê** page.
* **No automated tests.** The plugin ships no PHPUnit or integration test suite.
* **The draw is a two-step AJAX flow**, not one atomic endpoint: the first request validates the visitor and the second performs the draw and inserts the result. A visitor who stops after the first step is not counted as having drawn.

---

## 🤝 Contributing

Contributions, bug reports, and feature proposals are welcome! Feel free to open an issue or submit a Pull Request.

---

## 📄 License & Provenance Notice

This plugin is an **independent open-source project** created by Vo Quang Huy for technical demonstration and e-commerce gamification. It contains no confidential employer secrets or proprietary client data.
