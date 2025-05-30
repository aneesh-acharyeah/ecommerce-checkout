# 🛍️ eCommerce Checkout Flow Simulation

A PHP + MySQL project that simulates an eCommerce checkout experience. It includes product listing, variant selection, checkout form, transaction simulation, and email confirmation using Mailtrap. The UI is styled with Tailwind CSS.

---

## 📦 Features

- Product listing with image, title, price
- Encrypted product detail links
- Product detail page with:
  - Sub-image gallery
  - Description, variant (color/size) selector
  - Quantity input
  - Related products section
- Checkout form with:
  - Customer info
  - Address and card details
  - Simulated payment result
- Transaction result based on card number:
  - `1` → ✅ Approved
  - `2` → ❌ Declined
  - `3` → ⚠️ Gateway Error
- Confirmation/failure email sent via Mailtrap
- Responsive Tailwind CSS UI
- Product images and sub-images stored in MySQL

---

## 🧰 Technologies Used

- PHP 
- MySQL
- Tailwind CSS
- JavaScript (image gallery logic)
- PHPMailer (SMTP via Mailtrap)

---

## 📧Email via Mailtrap
Emails are sent to the user’s provided email using Mailtrap:
- Approved → Order confirmation email
- Declined/Error → Failure notification


----

## 🔐 Security & Notes
- Product and variant IDs are encrypted in URLs

- Email logic uses PHPMailer and SMTP

- Images are stored as URLs in the database


---


# 🌍 Live Demo
https://ecommerce-checkout.infinityfreeapp.com


--- 

## 🖼️ Screenshots
 ![App Screenshot](https://github.com/aneesh-acharyeah/ecommerce-checkout/blob/main/image1.png)
 ![App Screenshot](https://github.com/aneesh-acharyeah/ecommerce-checkout/blob/main/image2.png)


