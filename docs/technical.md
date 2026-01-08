# Technical Documentation

## Tech Stack

### Core Technologies
- **Backend**: Laravel 12.x, PHP 8.2+, SQLite.
- **Frontend**: Filament 4.x, Livewire 3.x, Tailwind CSS 4.x, DaisyUI 5.x, Alpine.js.

### Key Packages
- `filament/filament`: Admin Panel & Form Builder.
- `bezhansalleh/filament-shield`: Role & Permission Management.
- `alperenersoy/filament-export`: Excel/CSV Data Export.
- `jeffersongoncalves/filament-qrcode-field`: QR Code Input/Display.
- `milon/barcode`: 1D Barcode Generator.
- `jantinnerezo/livewire-alert`: SweetAlert2 wrapper for Livewire.
- `wildside/userstamps`: Record creator/updater tracking.

---

## Database Schema (ERD)

### Ringkasan Relasi
- `Users` memiliki relasi `hasOne` ke `UserDetails`.
- `Books` dikategorikan oleh `Categories` (`belongsTo`).
- `Transactions` menghubungkan `Users` (peminjam) dan `Books` (objek).
- `Transactions` memiliki `Status` (Menunggu, Dipinjam, Kembali, dll).

### Struktur Tabel Utama

#### `users`
Menyimpan data otentikasi.
- `id`, `name`, `email`, `password`, `role`.

#### `user_details`
Menyimpan profil lengkap anggota perpustakaan.
- `user_id` (FK), `nis`, `class`, `phone_number`, `barcode`.

#### `books`
Katalog buku perpustakaan.
- `id`, `title`, `isbn`, `stock`, `category_id` (FK), `barcode`.

#### `transactions`
Mencatat sirkulasi peminjaman.
- `code` (Unique), `user_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status_id`.
- `penalty_total`: Total denda jika ada.

```mermaid
erDiagram
    users ||--o{ user_details : has
    users ||--o{ transactions : makes
    users ||--o{ bookmarks : creates
    users ||--o{ model_has_roles : has
    users ||--o{ model_has_permissions : has

    roles ||--o{ model_has_roles : assigned_to
    permissions ||--o{ model_has_permissions : assigned_to
    role_has_permissions }o--|| roles : belongs_to
    role_has_permissions }o--|| permissions : belongs_to

    categories ||--o{ books : categorizes
    books ||--o{ transactions : borrowed_in
    books ||--o{ bookmarks : saved_in
    statuses ||--o{ transactions : defines

    users {
        int id PK
        string name
        string email UK
        datetime email_verified_at
        string password
        string role
        string avatar_url
    }

    user_details {
        int id PK
        int user_id FK
        string nik
        string nis
        string nisn
        string class
        string address
        string phone_number
        string barcode
    }

    categories {
        int id PK
        string name
        string slug
    }

    books {
        int id PK
        string title
        string isbn
        string author
        int year_published
        string publisher
        int book_count
        string barcode
        int category_id FK
    }

    transactions {
        int id PK
        string code UK
        date borrow_date
        date due_date
        date return_date
        int book_id FK
        int user_id FK
        int status_id FK
        decimal penalty_total
    }
```

---

## Class Diagram

Diagram kelas level aplikasi yang menunjukkan relasi antar Model Eloquent.

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +detail() HasOne
        +transactions() HasMany
        +bookmarks() HasMany
        +roles() BelongsToMany
    }

    class UserDetail {
        +int id
        +string nis
        +string phone_number
        +user() BelongsTo
    }

    class Book {
        +int id
        +string title
        +string isbn
        +category() BelongsTo
        +transactions() HasMany
        +bookmarks() HasMany
    }

    class Category {
        +int id
        +string name
        +books() HasMany
    }

    class Transaction {
        +int id
        +string code
        +date borrow_date
        +status() BelongsTo
        +user() BelongsTo
        +book() BelongsTo
    }

    class Status {
        +int id
        +string name
        +transactions() HasMany
    }

    User "1" --> "1" UserDetail : HasOne
    User "1" --> "*" Transaction : HasMany
    User "1" --> "*" Book : Bookmarks
    Category "1" --> "*" Book : HasMany
    Book "1" --> "*" Transaction : HasMany
    Transaction "*" --> "1" Status : BelongsTo
```

---

## Project Structure

```
perpus11/
├── app/
│   ├── Filament/           # Admin Panel Resources, Pages, Widgets
│   │   ├── Resources/      # CRUD Modules (Books, Users, etc)
│   │   └── Widgets/        # Dashboard stats & charts
│   ├── Models/             # Eloquent Models
│   ├── Services/           # Business Logic (Transaction, Barcode)
│   └── Console/Commands/+  # Custom Artisan Commands
├── database/
│   ├── migrations/         # Schema definitions
│   └── seeders/            # Dummy data generators
├── resources/views/        # Blade templates (PDFs, Custom layouts)
└── routes/console.php      # Scheduler definitions
```

---

## Testing

Proyek ini menggunakan **PHPUnit** untuk automated testing.

### Menjalankan Test
```bash
# Jalankan seluruh test suite
php artisan test

# Jalankan specific file
php artisan test tests/Feature/TransactionTest.php
```

### Coverage
- **Unit Tests**: Menguji logic level service dan model helper.
- **Feature Tests**: Menguji flow user, http request, dan permission (misal: Siswa tidak bisa akses halaman admin).

---

## Kontribusi

Kami menerima kontribusi kode melalui Pull Request.
1. Fork repository ini.
2. Buat branch fitur (`git checkout -b feature/Keren`).
3. Commit perubahan (`git commit -m 'Menambah fitur keren'`).
4. Push ke branch (`git push origin feature/Keren`).
5. Buat Pull Request di GitHub.
