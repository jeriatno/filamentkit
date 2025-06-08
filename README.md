# FilamentKit

**FilamentKit** is a Laravel Starter Kit built on top of **Filament v3**, designed to help you quickly scaffold a modern admin panel. It comes pre-integrated with essential plugins like authentication, access control, settings management, and Excel export/import support.

---

## 🚀 Features

- ✅ **Filament v3**: Beautiful and responsive admin panel using Tailwind & Alpine
- 🔐 **Filament Breezy**: Custom authentication with login, registration, and 2FA
- 🛡️ **Filament Shield**: Role-based access control
- 📁 **Media Library Plugin**: File and image management using Spatie
- ⚙️ **Settings Plugin**: Configurable system settings via UI
- 📊 **Laravel Excel**: Export and import data to/from Excel
- 🧩 Custom helpers: `general.php` and `access.php` for utility functions

---

## 📦 Installation

1. **Clone this repository**

```bash
git clone https://github.com/jeriatno/filamentkit.git
cd filamentkit
```

2. **Install dependencies**

```bash
composer install
```

3. **Copy .env and configure**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

5. **Start the development server**

```bash
php artisan serve
```

## 👤 Default Admin Credentials

```txt
Email: admin@example.com
Password: password
```

You can change these credentials in the seeder or through the admin panel after logging in.

## 🧰 Helper Structure

```txt
app/
└── Helpers/
    ├── access.php      # Role/user access helpers
    └── general.php     # Common utilities (formatting, etc.)
```

## 🧪 Testing & Dev Tools

- PHPUnit: Unit testing framework
- Laravel Pint: Code formatting tool
- Laravel Sail: Docker-based local dev environment (optional)

## 📄 License
MIT © Jeriatno

## 📬 Contact
Interested in contributing or collaborating? Feel free to reach out at jeriat01@gmail.com

---

Let me know if you'd like a **badge section** (for GitHub stars, build passing, etc.), or a