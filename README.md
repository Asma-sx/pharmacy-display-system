# Pharmacy Display System

A role-based web app for managing a pharmacy's medicine list. Admins can add, edit and delete medicines; pharmacists can browse and view details.

Built for the IT331 (Fundamentals of N-Tier Architectures) course at Imam Mohammad Ibn Saud Islamic University.

## Tech
PHP (OOP, PDO) · MySQL · Stored Procedures · JavaScript · HTML · CSS

## Architecture
The app is split into three layers:

| Layer | Folder | Role |
|---|---|---|
| Presentation | `presentation/` | Pages, client-side validation, calls the API with `fetch()` |
| Business | `business/` | Controllers (JSON API), services, server-side validation, sessions |
| Data Access | `data/` | PDO connection; every CRUD operation runs through a stored procedure |

## Features
- Login and registration with hashed passwords (`password_hash` / `password_verify`)
- Server-side sessions and role checks: pharmacists can read, only admins can write
- Full CRUD on medicines via stored procedures (`sp_get_medicines`, `sp_add_medicine`, ...)
- Server-side validation (required fields, non-negative price and quantity, future expiry date)

## Demo account
Pharmacist: `demo` / `demo1234`

## Run locally (XAMPP)
1. Copy the folder into `htdocs/`.
2. In phpMyAdmin create a database named `pharmacy_db` and import `data/migrations/database.sql`.
3. Check the settings in `data/config.php`.
4. Open `http://localhost/PharmacyDisplaySystem/`.
