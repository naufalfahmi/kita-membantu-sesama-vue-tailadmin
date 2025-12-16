Place one or more exported transaction JSON files here (from the API). Files should be named `transaksi*.json`.

Format expected:
- Top-level object with `data` array (each item with fields like `id`, `amount`, `donor_id`, `fundraiser_id`, `program_id`, `branch_office_id`, `description`, `transaction_date`, `created_at`, `updated_at`)

When running `php artisan db:seed --class=TransaksiSeeder`, the seeder will import all matching JSON files, create minimal placeholder related records if they don't exist, and upsert transactions (preserving `id`, `created_at`, `updated_at` when provided).

If no JSON files are present, the seeder will fall back to generating 30 random sample transactions.
