# 🛠 Mini Admin Panel (Laravel 12)

## 📌 Project Overview

This project is a **Mini Admin Panel** built using **Laravel 12**.
It demonstrates core Laravel concepts such as routing, middleware, service container, facades, and API development.

The application allows admins to manage products and users with proper authentication and role-based access.

---

## 🚀 Features Implemented

### 🔐 Authentication

* User Registration, Login, Logout
* Password reset functionality (Laravel Breeze)

### 👤 Role-Based Access Control

* Admin and User roles
* Custom middleware (`CheckRole`)
* Admin-only routes protection

### 📦 Product Management

* Create, Read, Update, Delete (CRUD)
* Product image upload
* Route Model Binding used

### 🛒 Cart System

* Add to cart
* Remove from cart
* Clear cart

### ⚙️ Service Layer

* `ProductService` for business logic
* Clean separation of concerns

### 🧩 Custom Configuration

* `config/company.php`
* Environment-based values using `.env`

### 🪞 Custom Facade

* Created custom facade for Product operations
* Connected with Service Container

### 🔌 Service Provider

* Custom bindings in `AppServiceProvider`
* View composer and response macro

### 🌐 API Development

* API endpoint: `/api/products`
* Returns JSON response
* Rate limiting applied (`throttle:60,1`)

### 🔒 Security

* CSRF protection in forms
* Signed routes implemented

### ⚡ Deployment Optimization

* Used optimization commands:

  * `php artisan config:cache`
  * `php artisan route:cache`
  * `php artisan view:cache`
  * `php artisan optimize`

---

## 🧠 Concepts Learned

### 🔄 Request Lifecycle

Request → Middleware → Route → Controller → Response

### 📦 Service Container & Dependency Injection

* Used for binding services
* Constructor injection in controllers

### 🪄 Facades

* Static-like interface for services
* Connected to Service Container internally

### ⚙️ Config vs env()

* `env()` → used in config files
* `config()` → used throughout application

---

## 🛠 Setup Instructions

```bash
git clone 
cd laravel12-app

composer install
cp .env.example .env

# Configure database in .env

php artisan key:generate
php artisan migrate

npm install
npm run dev

php artisan serve
```

---

## 📂 Project Structure (Important Folders)

* `app/` → Core application logic
* `routes/` → Web & API routes
* `config/` → Configuration files
* `resources/` → Blade templates
* `public/` → Entry point (`index.php`)
* `storage/` → Logs & uploads

---

## 📸 Screenshots

(Add screenshots here)

* Dashboard
* Product List
* Create Product
* API Response (Postman)

---


## 🎥 Demo Video (Optional)

(Add video link here)

---

## 📌 Conclusion

This project helped in understanding:

* Laravel architecture
* Clean code practices
* Real-world admin panel structure
* API development with rate limiting

---

## 👨‍💻 Author

**Digvijaysinh Sarvaiya**
