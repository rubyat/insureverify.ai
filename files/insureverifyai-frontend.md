# InsureVerifyAI Frontend (Laravel + Inertia Vue + Tailwind)

This document describes the **visual design and layout** of the InsureVerifyAI marketing website, extracted from screenshots and the provided `index.html`.  
Use this as **context for Windfurf AI** when scaffolding Vue pages.

---

## 🌐 Global Layout

- **Header / Navbar**
  - Black background, sticky at top.
  - Left: InsureVerifyAI logo.
  - Center: Navigation links → `Features`, `Pricing`, `Contact`.
  - Right: `Login` (link), `Sign Up` (button, blue background).
  - Mobile: Hamburger menu.

- **Footer**
  - Black background, white text.
  - 4 columns:
    - Logo + mission statement.
    - Quick Links: About, Pricing, API Docs.
    - Legal: Privacy Policy, Terms of Service.
    - Contact Info: Email + business hours.
  - Social icons: Facebook, LinkedIn, X (Twitter), Instagram.

---

## 🏠 Home Page (index)

### Hero Section
- Background: Tech image with lock icon (cybersecurity style).
- Headline: **"Fast, Reliable License & Insurance Verification API for Car Rentals"**
- Subtext: Verify renters’ licenses + insurance in seconds.
- CTA: `Request API Access` (blue button).

### Why Choose Section
- Title: **Why Choose InsureVerifyAI?**
- Tagline: Smarter Verifications. Safer Rentals.
- Highlight card: **$3,000,000 Fraud-Related Theft Coverage**
- 4 feature blocks (icons + text):
  1. **Identity & License Verification (AI + Biometrics)** – uses CLEAR-like system, $1M liability coverage.
  2. **Real-Time Insurance Verification** – auto checks insurance declarations instantly.
  3. **Recorded Insurance Confirmation (Add-on)** – records proof of coverage.
  4. **Scalable Growth** – API grows with rental business.

### CTA Banner
- Black background, white text.
- Headline: **"Ready to Approve More Renters in Seconds?"**
- CTA: `Get Started` button.

### Pricing Section
- Title: **Pricing – Simple, transparent**
- 5 pricing cards:
  - **Silver ($99/mo, 8 verifications)** → No min, real-time checks, support.
  - **Bronze ($199/mo, 20 verifications)** → Priority API, audit trail, manager.
  - **Gold ($349/mo, 40 verifications)** → Identity, license, insurance, fraud prevention, analytics.
  - **Platinum ($499/mo, 75 verifications)** → Adds AI voice confirmation, call recording, fraud prevention, customizable flow.
  - **Enterprise (Custom Pricing)** → High volume, custom SLAs, reporting.

### Partners Section
- Title: **We Work With the Best Partners**
- Logos grid (RentFYV, Panda Exotic Car Rentals, Premier Auto Rental, Studinovski, FYV Exotic Car Rental, Gather).
- CTA: `Read More`.

---

## ℹ️ About Us Page
- Intro: InsureVerifyAI is a verification platform for car rental & mobility companies.
- 12+ years rental industry experience.
- Partner with **Gather** + **CLEAR**.
- **Coverage**: up to **$300,000** in case of identity verification failure.
- **What We Do**: Verify license, check insurance, automate approvals, integrate with systems.
- **Why It Matters**: Saves cost, ensures compliance, reduces fraud, avoids storing sensitive data too long.

---

## 📞 Contact Page
- Form with fields:
  - Name
  - Email
  - Message
- CTA: `Send Message`.

---

## ⚙️ API Documentation Page
- Quick Start Example:
  ```http
  POST https://api.insureverify.ai/v1/verify
  Headers:
  Authorization: Bearer YOUR_API_KEY
  Content-Type: application/json
  Body:
  {
    "license_number": "D123456789",
    "insurance_policy": "XYZ123456789"
  }
  ```
- Endpoints:
  1. `/v1/verifylicense` → license status, validity, expiry.
  2. `/v1/verifyinsurance` → insurance provider, status, coverage date.
- Authentication: Bearer Token.
- Rate Limits: Free = 100 reqs/month, Pro = 10k/month, Enterprise = custom.
- Support email: support@insureverify.ai.

---

## 🛡️ Features Page
- Grid layout:
  1. **Fast Verification** → instant checks.
  2. **Developer Friendly** → APIs, sandbox, testing.
  3. **Secure & Encrypted** → full encryption in transit/rest.
  4. **Scalable Infrastructure** → grows with usage.
- CTA: `Request API Access`.

---

## 💰 Pricing Page (Standalone)
- Duplicate of home page pricing section.
- Same 5 pricing tiers (Silver, Bronze, Gold, Platinum, Enterprise).

---

## 🔐 Privacy Policy Page
- Effective Date: 01-08-2025.
- Applies to:
  - Website
  - API services
  - Support & integrations
- **Information Collected**
  - Customer/Account info: Business name, contacts, API keys, billing, interactions.
  - End-user data (verification subjects): Name, DOB, license info, insurance details.
- Compliance: Only lawful use, not collected directly from individuals.

---

## 📝 Signup Page
- Title: **Sign Up for API Access**
- Fields:
  - Choose a Plan (dropdown)
  - Full Name
  - Business Email
  - Password + Confirm
  - Payment Info: Credit card, Expiration, CVV
- Checkbox: Accept terms & conditions.
- CTA: `Create Account`.
- Option: Sign up with Google.

---

## 🎨 Styling
- **Colors**:
  - Primary: Blue (`#0086ed`)
  - Backgrounds: White / Black
  - Text: Black / Gray / White
- **Typography**:
  - Bold headings, clean sans-serif.
  - CTA buttons rounded with hover states.
- **Icons**:
  - Lucide icons for features.
- **Components**:
  - Pricing cards, CTA banners, partner logos grid.

---
