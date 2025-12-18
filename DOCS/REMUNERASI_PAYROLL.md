# Remunerasi → Payroll (Per-Periode) Migration Notes

Ringkasan perubahan:

- Menambahkan tabel baru untuk menyimpan rekap gaji per periode (1 bulan):
  - `payroll_periods` — periode (month, year), status (draft/generate/transferred), generated_at
  - `payroll_records` — per karyawan per periode, total_amount, status
  - `payroll_items` — line items per record (description, qty, qty_type, unit, unit_value, amount)

- Menambahkan service `App\Services\PayrollService` untuk menghasilkan periode dan record otomatis dari semua karyawan aktif (`User::karyawan()->active()`).

- Menambahkan controller API `App\Http\Controllers\Api\PayrollController` dan route baru di:
  - `GET /admin/api/operasional/payroll/periods` — list periode
  - `POST /admin/api/operasional/payroll/periods/generate` — generate periode
  - `GET /admin/api/operasional/payroll/periods/{id}` — show periode + records
  - `POST /admin/api/operasional/payroll/periods/{id}/transfer` — dispatch transfer job
  - `GET /admin/api/operasional/payroll/periods/{periodId}/records/{recordId}` — show single record
  - `POST /admin/api/operasional/payroll/periods/{periodId}/records/{recordId}/items` — add item
  - `PUT /admin/api/operasional/payroll/periods/{periodId}/records/{recordId}/items/{itemId}` — update item
  - `DELETE /admin/api/operasional/payroll/periods/{periodId}/records/{recordId}/items/{itemId}` — delete item

- Menambahkan frontend skeleton:
  - `public/admin/src/views/Operasional/Payroll.vue` — list + generate
  - `public/admin/src/views/Operasional/PayrollDetail.vue` — list records per periode
  - `public/admin/src/views/Operasional/PayrollRecordEdit.vue` — edit record items UI
  - Routes ditambahkan ke router

- Menambahkan migration untuk memigrasi data historis dari `remunerasis` ke `payroll_*` (dipetakan sebagai `transferred` historical records). Backup DB sebelum menjalankan migration.
- Legacy: `remunerasis` API/UI telah didepresiasi dan tabel `remunerasis` dapat dihapus jika semua historis telah dimigrasi (ada migration untuk drop table). Pastikan backup sebelum deploy.

- Menambahkan permissions baru dengan seeder `RemunerasiPermissionsSeeder`:
  - `view remunerasi`, `create remunerasi`, `update remunerasi`, `delete remunerasi`, `generate remunerasi`, `manage remunerasi`, `transfer remunerasi`.

- Menambahkan job `App\Jobs\ExecutePayrollTransfer` sebagai placeholder untuk eksekusi transfer batch (dipanggil saat transfer periode).

Catatan penting sebelum deploy:

1. BACKUP database sebelum menjalankan `php artisan migrate` di production.
2. Review migrated historical data — migration membuat periods/records/items dari `remunerasis` dan menandainya sebagai `transferred`.
3. Build frontend: jalankan `npm run build` di `public/admin/` setelah menyesuaikan UI.
4. Setup queue worker untuk proses transfer job: `php artisan queue:work` (atau supervisor di production).
5. Tambahkan dan cek permissions pada role yang sesuai.
6. Tes end-to-end untuk periode generate → edit record → transfer.

Contoh perintah:

```bash
php artisan migrate
php artisan db:seed --class=RemunerasiPermissionsSeeder
npm run build --prefix public/admin
php artisan queue:work
```

Jika ingin saya lanjutkan: saya bisa membuat migrasi tambahan untuk mengganti penggunaan `Remunerasi` model di front-end menjadi period-based, menambahkan lebih banyak test coverage, atau memperhalus UI edit (preset items, templates, percent base selection).
