# Mini Admin Panel (Laravel 12)

## Project Overview
This project is a Mini Admin Panel built using Laravel 12.  
It includes features like Product CRUD operations, authentication system, role-based access control, cart functionality, and API responses.

---

## Features Implemented
- User Authentication (Login, Register, Profile)
- Role-based Middleware (Admin & User)
- Product Management (CRUD)
- Product Image Upload
- Cart System (Add, Remove, Clear)
- API for Products
- Custom Response Macro
- Signed URL Implementation
- File Download Feature
- Custom Service Provider
- Custom Facade (Product, Greeting)
- View Composer & Global Data Sharing

---
aravel Concepts Covered
Request Lifecycle

Request → Service Provider → Middleware → Controller → Response

Service Providers initialize services

Middleware checks authentication and roles

Controller handles logic

Response is returned

Service Container

Laravel automatically resolves dependencies.

Example:

public function __construct(PaymentService $ps)

Object created automatically

Binding defined in AppServiceProvider

Singleton used for same instance

Dependency Injection

Constructor Injection used in controllers

Services injected automatically

Facades

Example:

ProductFacade::store($data);

Calls ProductService internally

Uses Service Container

Middleware

Custom middleware CheckRole:

Checks authentication

Allows only admin access

Routing

Named routes

Route model binding

Middleware groups

Signed URLs

Request Handling

$request->input()

$request->query()

Form Request Validation

File Upload

Responses

View

JSON

Redirect

Download

Response Macro

Views (Blade)

Layouts

Components

View Composer

Global data sharing

Screenshots

(Add screenshots here)

Homepage

Product List

Add Product

Cart Page

API Response

What I Learned

Request lifecycle

Service Container & DI

bind vs singleton

Facades

Middleware

Validation & file upload

API handling

Blade system
