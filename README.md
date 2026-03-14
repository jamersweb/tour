# Acute Tourism Rebuild

This repository is the new foundation for rebuilding Acute Tourism as a premium Dubai experiences site on Laravel, Vue, and Inertia.

## Stack

- Laravel 12
- Vue 3
- Inertia.js
- Vite
- Tailwind CSS v4

## Build Direction

- Replace the current generic marketplace-style experience with a curated premium brand.
- Focus the public site on desert, yacht, city, water, family, and private experiences.
- Lead with premium presentation, clear logistics, and concierge-first conversion before full checkout.

## Initial Public Routes

- `/`
- `/experiences`
- `/experiences/{slug}`
- `/collections/{slug}`
- `/about`
- `/corporate-events`
- `/contact`
- `/journal`
- `/faq`

## Current CMS Features

- Filament admin at `/admin`
- Site settings for brand, homepage copy, contact data, and footer content
- Organization/legal/social schema settings with shared JSON-LD output
- Redirect manager for legacy-to-new URL migration
- Collections and experiences with editor-managed SEO fields
- Imported legacy `/tour` catalog data with images and descriptions
- Package content type with public listing/detail pages and UFC Abu Dhabi seed data
- Filament package management at `/admin/packages`
- Hero images for collections and experiences
- Gallery images for experience detail pages
- Journal articles with list and detail pages
- FAQ entries grouped by category
- Lead inquiries captured into the admin panel with experience context, notes, and follow-up fields
- Admin dashboard widgets for inquiry pipeline, recent leads, and follow-up queue
- Payment transactions in admin at `/admin/payment-transactions`
- User auth with account dashboard, order history, inquiry history, profile editing, and feedback
- Customer password reset flow and in-account password change
- Account order receipt page with traveler details and printable summary
- PDF invoice download for customers and admins
- Admin payment actions for reconcile, refund, resend confirmation, and invoice export
- Package seed data normalized from `database/data/current-packages.json`

## Payment Flow

- Gateway target: `Network Payment Gateway`
- Integration mode: hosted checkout flow using N-Genius-style outlet order creation
- Public checkout routes:
  - `/checkout/experiences/{slug}`
  - `/checkout/packages/{slug}`
- Gateway callback route:
  - `/payments/network/callback`
- Result page:
  - `/checkout/result/{transaction}`

## Payment Env

- `NETWORK_PAYMENT_ENABLED`
- `NETWORK_PAYMENT_BASE_URL`
- `NETWORK_PAYMENT_OUTLET_ID`
- `NETWORK_PAYMENT_API_KEY`
- `NETWORK_PAYMENT_API_SECRET`
- `NETWORK_PAYMENT_CURRENCY`
- `NETWORK_PAYMENT_ACTION`
- `WHATSAPP_NOTIFICATIONS_ENABLED`
- `WHATSAPP_API_URL`
- `WHATSAPP_API_TOKEN`
- `WHATSAPP_FROM`

## Payment Gateway

- The production checkout will use `Network Payment Gateway`.
- Multi-guest checkout now captures traveler name, email, and phone per guest.
- After a successful payment callback, booking confirmations are sent by email to the buyer and each traveler.
- WhatsApp confirmations can also be sent when the WhatsApp provider env vars are configured.
- Payment transactions now support reconciliation and refund audit fields.
- The current live package surface exposed one reachable package slug during import verification: `ufc-fight-night-returns-to-abu-dhabi`.
