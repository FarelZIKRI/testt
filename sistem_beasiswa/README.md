# Sistem Pendaftaran Beasiswa

Sistem pendaftaran beasiswa online untuk kampus dengan fitur CRUD (Create, Read, Update, Delete) yang dibuat menggunakan PHP native, HTML/CSS, dan MySQL.

## Analisis Studi Kasus

### Jumlah User, Hak Akses, dan Peran

**1 Jenis User Utama: Mahasiswa**
- **Peran**: Pendaftar beasiswa
- **Hak Akses**:
  - Melihat jenis beasiswa dan syarat-syaratnya
  - Mendaftar beasiswa (jika IPK ≥ 3.0)
  - Mengisi form pendaftaran
  - Upload berkas syarat
  - Melihat hasil pendaftaran beasiswa

*Catatan: Sistem ini fokus pada user mahasiswa sesuai requirement. Tidak ada admin panel karena tidak disebutkan dalam studi kasus.*

## Fitur Sistem

### 1. Halaman Utama (Beranda)
- Informasi jenis beasiswa yang tersedia
- Syarat dan ketentuan beasiswa
- Panduan cara mendaftar
- Informasi kontak

### 2. Form Pendaftaran Beasiswa
- Input nama lengkap
- Input email dengan validasi format
- Input nomor HP (hanya angka)
- Pilihan semester (1-8)
- IPK otomatis dari sistem (simulasi)
- Pilihan jenis beasiswa (aktif jika IPK ≥ 3.0)
- Upload berkas syarat (PDF, JPG, PNG, ZIP)
- Validasi client-side dan server-side

### 3. Hasil Pendaftaran
- Tampilan semua data pendaftaran
- Status ajuan: "belum di verifikasi"
- Responsive design (desktop & mobile)
- Download berkas yang diupload

## Teknologi yang Digunakan

- **Backend**: PHP Native (tanpa framework)
- **Frontend**: HTML5, CSS3 (tanpa framework)
- **Database**: MySQL
- **JavaScript**: Vanilla JS untuk validasi dan interaktivitas

## Struktur Folder

```
sistem_beasiswa/
├── config/
│   └── database.php          # Konfigurasi database
├── includes/
│   └── functions.php         # Fungsi-fungsi helper
├── pages/
│   ├── home.php             # Halaman beranda
│   ├── daftar.php           # Form pendaftaran
│   └── hasil.php            # Hasil pendaftaran
├── assets/
│   ├── css/
│   │   └── style.css        # Stylesheet utama
│   └── js/
│       └── script.js        # JavaScript untuk interaktivitas
├── uploads/                 # Folder untuk file upload
├── index.php               # File utama aplikasi
├── database.sql            # Script database
└── README.md               # Dokumentasi
```

## Instalasi dan Setup

### 1. Persiapan Database

1. Buat database MySQL:
```sql
CREATE DATABASE sistem_beasiswa;
```

2. Import file `database.sql` ke database:
```bash
mysql -u root -p sistem_beasiswa < database.sql
```

### 2. Konfigurasi Database

Edit file `config/database.php` sesuai dengan pengaturan database Anda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistem_beasiswa');
```

### 3. Setup Web Server

1. Copy folder `sistem_beasiswa` ke direktori web server (htdocs/www)
2. Pastikan folder `uploads` memiliki permission write (755 atau 777)
3. Akses aplikasi melalui browser: `http://localhost/sistem_beasiswa`

## Database Schema

### Tabel `jenis_beasiswa`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `nama_beasiswa` (VARCHAR 100)
- `syarat_ipk` (DECIMAL 3,2)
- `deskripsi` (TEXT)
- `created_at` (TIMESTAMP)

### Tabel `pendaftaran_beasiswa`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `nama` (VARCHAR 100)
- `email` (VARCHAR 100)
- `no_hp` (VARCHAR 15)
- `semester` (INT)
- `ipk` (DECIMAL 3,2)
- `jenis_beasiswa_id` (INT, FOREIGN KEY)
- `berkas_syarat` (VARCHAR 255)
- `status_ajuan` (ENUM: 'belum di verifikasi', 'diverifikasi', 'ditolak')
- `created_at` (TIMESTAMP)

## Logika Bisnis

### Validasi IPK
- IPK di-generate otomatis oleh sistem (simulasi)
- Jika IPK < 3.0: form beasiswa, upload, dan tombol submit dinonaktifkan
- Jika IPK ≥ 3.0: semua elemen form aktif, fokus otomatis ke pilihan beasiswa

### Validasi Form
- **Client-side**: JavaScript untuk validasi real-time
- **Server-side**: PHP untuk validasi final sebelum menyimpan
- Email: format email valid
- Nomor HP: hanya angka
- File upload: PDF/JPG/PNG/ZIP, maksimal 5MB

### Status Pendaftaran
- Default: "belum di verifikasi"
- Dapat diubah menjadi "diverifikasi" atau "ditolak" (melalui database)

## Keamanan

- Input sanitization dengan `htmlspecialchars()`
- Prepared statements untuk query database
- Validasi file upload (ekstensi dan ukuran)
- CSRF protection melalui form validation

## Responsive Design

- Mobile-first approach
- Breakpoint: 768px
- Tabel berubah menjadi card layout di mobile
- Navigation yang mobile-friendly

## Pengembangan Lebih Lanjut

Untuk pengembangan selanjutnya, sistem dapat ditambahkan:

1. **Admin Panel**
   - Login admin
   - Verifikasi pendaftaran
   - Manajemen jenis beasiswa

2. **Notifikasi**
   - Email notification
   - SMS notification

3. **Reporting**
   - Export data ke Excel/PDF
   - Statistik pendaftaran

4. **Security Enhancement**
   - Login system
   - Session management
   - Rate limiting

## Troubleshooting

### Error Database Connection
- Pastikan MySQL service berjalan
- Cek konfigurasi database di `config/database.php`
- Pastikan database dan tabel sudah dibuat

### File Upload Error
- Cek permission folder `uploads` (755/777)
- Pastikan `upload_max_filesize` dan `post_max_size` di php.ini cukup besar

### JavaScript Not Working
- Pastikan path file `assets/js/script.js` benar
- Cek console browser untuk error JavaScript

## Kontribusi

Sistem ini dibuat untuk keperluan pembelajaran web development tingkat junior. Silakan modifikasi sesuai kebutuhan.

## Lisensi

Open source - bebas digunakan untuk keperluan pembelajaran.