# PHP_Laravel12_Filament_Admin_Starter

## Overview

This project is a **modern Laravel 12 admin starter kit** built using **Laravel Breeze** for authentication and **Filament** for a powerful admin panel.

It is designed to be:

* Clean and beginner-friendly
* Fully compatible with Laravel 12
* Production-ready
* A modern replacement for old / archived base templates

This repository can be reused as a base for:

* Admin panels
* CMS systems
* E-commerce backends
* CRM / ERP projects

---

## Features

* Laravel 12 (latest stable)
* Authentication system (Login / Register / Forgot Password)
* Blade-based UI using Laravel Breeze
* Filament Admin Panel
* Admin dashboard
* User management ready
* Role & permission ready (Spatie compatible)
* Clean folder structure
* Future-proof architecture

---


## Installation Guide

Below are the **exact steps** to install this project from scratch.

---

## Laravel 12 + Breeze + Filament

STEP 1: Create a New Laravel 12 Project

```
composer create-project laravel/laravel php_laravel12_project
```

Check Laravel version:

```
php artisan --version
```

Expected:

```
Laravel Framework 12.x
```

---

STEP 2: Environment Setup

Configure database in `.env`

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_admin
DB_USERNAME=root
DB_PASSWORD=
```

---

STEP 3: Install Laravel Breeze (Authentication)

3.1 Install Breeze package

```
composer require laravel/breeze --dev
```

3.2 Install Breeze UI

```
php artisan breeze:install
```

Choose exactly:

* Blade
* No (dark mode optional)
* PHPUnit (IMPORTANT – not Pest)

3.3 Install frontend assets

```
npm install
npm run dev
```

3.4 Run migrations

```
php artisan migrate
```

3.5 Run server

```
php artisan serve
```

Open:

```
http://127.0.0.1:8000
```

Login / Register should work

---

STEP 4: Enable PHP intl Extension (REQUIRED for Filament)

Open:

```
C:\xampp\php\php.ini
```

Find:

```
;extension=intl
```

Change to:

```
extension=intl
```

Restart Apache.

Verify:

```
php -m | findstr intl
```

Output:

```
intl
```

---

STEP 5: Install Filament Admin Panel (Laravel 12 Compatible)

5.1 Install Filament

```
composer require filament/filament:^4.5
```

5.2 Run Filament installer

```
php artisan filament:install
```

5.3 Allow user to access admin panel

Open:

```
app/Models/User.php
```

```php
<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Filament Admin Access Control
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // allow all users for now
    }
}
```

---

STEP 6: Create Admin User

Register a user:

```
http://127.0.0.1:8000/register
```

Then open admin:

```
http://127.0.0.1:8000/admin
```

Sign In:-

<img width="1077" height="838" alt="Screenshot 2026-01-19 142242" src="https://github.com/user-attachments/assets/5ab9ed63-4aa1-410a-8ea3-3ff035d0b5ae" />


Dashboard:-

<img width="1919" height="944" alt="Screenshot 2026-01-19 142300" src="https://github.com/user-attachments/assets/299bd9d0-310f-41d8-94de-6c2745789884" />


---

## Result

* Laravel 12 installed
* Authentication working
* Filament admin panel working
* Clean and modern base project

---

## Future Enhancements

* Role-based admin access
* Permissions management
* CRUD modules (Users, Products, Orders)
* Admin theme customization

---


