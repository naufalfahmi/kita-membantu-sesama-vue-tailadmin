
# Kita Membantu Sesama (KMS)

>KMS adalah aplikasi manajemen donasi, keuangan, dan administrasi untuk yayasan sosial, dibangun dengan Laravel (backend) dan Vue TailAdmin (frontend).

## Fitur Utama

- Manajemen Donatur, Karyawan, Mitra, dan Program
- Pengelolaan Absensi, Gaji, Pangkat, Jabatan, dan SOP
- Dashboard Keuangan, Laporan, dan Penyaluran Dana
- Landing Page dinamis untuk publikasi kegiatan dan program
- Sistem autentikasi dan otorisasi berbasis role/permission
- UI modern berbasis Vue TailAdmin

## Teknologi

- Laravel (PHP) untuk backend API dan logika bisnis
- Vue 3 + TailAdmin untuk frontend admin
- MySQL untuk database
- Vite untuk build frontend

## Instalasi

1. Clone repository:
	```bash
	git clone https://github.com/naufalfahmi/kita-membantu-sesama-vue-tailadmin.git
	```
2. Install backend dependencies:
	```bash
	composer install
	```
3. Install frontend dependencies:
	```bash
	cd public/admin
	npm install
	npm run build
	```
4. Copy `.env.example` ke `.env` dan sesuaikan konfigurasi database.
5. Jalankan migrasi dan seeder:
	```bash
	php artisan migrate --seed
	```
6. Jalankan server Laravel:
	```bash
	php artisan serve
	```

## Struktur Folder

- `app/` : Backend Laravel (Controllers, Models, Services)
- `public/admin/` : Frontend Vue TailAdmin
- `database/` : Migrasi dan Seeder
- `resources/views/` : Blade templates
- `routes/` : Routing Laravel

## Kontribusi

Silakan buat pull request atau issue untuk saran dan perbaikan.

## Lisensi

MIT
