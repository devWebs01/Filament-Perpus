# Getting Started

## Kebutuhan Sistem

### Minimum Requirements
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: 2.x atau lebih tinggi
- **Node.js**: 18.x atau lebih tinggi
- **NPM**: 9.x atau lebih tinggi
- **Database**: SQLite 3.x (default) / MySQL 5.7+ / PostgreSQL 10+

### PHP Extensions
- BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML
- GD (untuk image processing)
- SQLite (jika menggunakan SQLite)

### Recommended
- RAM: 2GB atau lebih
- Disk Space: 500MB atau lebih
- CPU: Dual core atau lebih

---

## Instalasi

### Quick Install (Otomatis)
Clone repository dan jalankan script setup:

```bash
git clone <repository-url>
cd perpus11
composer run setup
```

Script ini akan menginstall dependency, setup env, generate key, migrate database, dan build assets.

### Manual Install
1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd perpus11
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database**
   Edit `.env` sesuai database yang digunakan. Default menggunakan SQLite.
   ```bash
   touch database/database.sqlite
   ```

5. **Jalankan Migration & Seeder**
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```
   *Seeder akan membuat user admin, petugas, siswa, dan data dummy.*

6. **Install Frontend Dependencies & Build**
   ```bash
   npm install
   npm run build
   ```

7. **Link Storage**
   ```bash
   php artisan storage:link
   ```

---

## Konfigurasi

### Pengaturan Aplikasi (.env)
```env
APP_NAME="Perpus11"
APP_ENV=local
APP_URL=http://localhost:8000
TZ=Asia/Jakarta
```

### Konfigurasi Email
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Akun Default
| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@testing.com | password |
| Ketua | ketua@testing.com | password |
| Petugas | petugas1@testing.com | password |
| Siswa | siswa@testing.com | password |

---

## Deployment & Optimasi

### Production Checklist
- [ ] Set `APP_ENV=production` & `APP_DEBUG=false`
- [ ] Setup SSL (HTTPS)
- [ ] Konfigurasi Queue Worker (Supervisor)
- [ ] Setup Scheduler (Cron job)

### Optimasi Filament & Laravel 🚀

Agar aplikasi berjalan ngebut di production, jalankan perintah berikut:

#### 1. Cache Configuration, Events, & Routes
Mengurangi overhead parsing file konfigurasi dan route.
```bash
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

#### 2. Optimasi Autoloader
```bash
composer install --optimize-autoloader --no-dev
```

#### 3. Optimasi Filament Icons & Components
Filament memiliki caching internal untuk icons dan components agar rendering lebih cepat.
```bash
php artisan filament:optimize
```
*Note: Pastikan command ini tersedia di versi Filament yang digunakan.*

#### 4. Cache Icons (Blade Icons)
Jika menggunakan blade-icons:
```bash
php artisan icons:cache
```

#### 5. Optimasi Server (PHP Opcache)
Pastikan `opcache` aktif di `php.ini`. Rekomendasi setting:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

#### 6. Build Assets for Production
Pastikan assets (CSS/JS) di-minify.
```bash
npm run build
```

---

## Troubleshooting Deployment

### Error: "500 Internal Server Error"
- Cek permission folder `storage` dan `bootstrap/cache` (chmod 775).
- Cek logs: `tail -f storage/logs/laravel.log`.

### Error: "Icons not showing"
- Jalankan `php artisan icons:clear` lalu `php artisan icons:cache`.
