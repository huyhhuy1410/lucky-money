# Lucky Money for WordPress

A WordPress campaign/gamification plugin for running "lucky money" prize campaigns. It provides a frontend gift-opening experience, admin prize management, result tracking, reporting, email notification, and optional WooCommerce coupon integration.

## What It Does

- Adds a Lucky Money campaign template to WordPress pages.
- Provides a shortcode-based campaign renderer.
- Lets admins manage prizes, campaign settings, frontend submissions, and result statistics.
- Tracks participant data including name, phone, email, prize, page/campaign, and received status.
- Supports prize types such as no prize, custom prize, WooCommerce coupon, and WooCommerce product.
- Randomly selects prizes based on prize configuration and winning rules.
- Prevents repeat participation based on configurable conditions such as email/phone and time limit.
- Sends winner email notifications.
- Supports reporting, pagination, filtering, and export-oriented data formatting.
- Updates prize received status when a related WooCommerce order status changes.

## Shortcodes and Templates

- `[boclixi_page]` renders a Lucky Money campaign by page/program ID.
- Adds a page template named `boclixi.index.php`.
- Frontend UI includes intro, guide, form, game, result, and maintenance popup partials.

## Main Features

- Admin menu for campaign management.
- Prize CRUD and prize option helpers.
- Result storage and reporting.
- Campaign-level validation and participation limits.
- Frontend AJAX flow for prize draw and result updates.
- WooCommerce coupon generation and email restriction support when WooCommerce is active.
- Chart/report data helpers for campaign statistics.
- Frontend animation stack with GSAP, ScrollTrigger, SmoothScroll, and modular JavaScript.

## Technical Notes

- Main plugin file: `lucky_money.php`
- Core classes:
  - `Lucky_MoneyProgram`
  - `Lucky_MoneyPrize`
  - `Lucky_MoneyResult`
  - `Lucky_MoneyEmail`
- Important AJAX actions:
  - `lm_ajax_program_prize_win`
  - `lm_ajax_program_prize_result_updation`
  - `lm_ajax_result_pagination_ajax`
  - `lm_ajax_result_report_ajax`
  - `lm_ajax_program_result_received`
- Uses custom database tables for programs, prizes, results, and email data.
- Uses WordPress filters/actions to keep prize drawing, validation, import, and email sending extensible.

## Installation

1. Upload the plugin folder to `wp-content/plugins/lucky-money`.
2. Activate the plugin in WordPress Admin.
3. Create or edit a page and select the Lucky Money page template, or use `[boclixi_page]`.
4. Configure prizes and campaign settings from the Lucky Money admin menu.
5. If WooCommerce coupon prizes are used, make sure WooCommerce is active.

## Public Repository Note

The plugin includes many visual assets, fonts, icon libraries, and audio files used by the campaign UI. Before using this repository in a public/commercial distribution, verify the license status of bundled fonts and media assets.
