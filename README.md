# Export Data Toko

Aplikasi localhost sederhana untuk membaca database MySQL toko dan mengekspor **list barang** serta **detail penjualan** ke file Excel (`.xls`).

## Fitur

- Ringkasan jumlah barang, nota, dan omzet.
- Filter periode penjualan.
- Export detail penjualan per periode.
- Export barang dengan filter kode/nama/barcode, kategori, supplier, dan status stok.
- Tidak memerlukan framework atau dependency Composer.

## Persyaratan

- PHP 8.1+ dengan ekstensi `pdo_mysql`.
- MySQL 8 atau MariaDB.
- Apache/Nginx, atau PHP development server.
- File dump database dengan tabel `barang`, `penjualan`, dan `itempenjualan`.

## Setup dengan Laragon (Windows)

1. Clone repositori ke Document Root Laragon. Cek Document Root melalui **Laragon > Preferences > General > Document Root**.

   ```powershell
   git clone https://github.com/Squeezy/export-data-toko.git export-toko
   ```

2. Jalankan Apache dan MySQL dari Laragon.

3. Impor dump SQL lewat HeidiSQL, atau terminal:

   ```powershell
   mysql -u root -p < "C:\lokasi\backup.sql"
   ```

   Dump yang digunakan aplikasi harus menghasilkan database bernama `toko_1_3`. Jika namanya berbeda, sesuaikan `database` di `config.php`.

4. Sesuaikan koneksi di `config.php`:

   ```php
   return [
       'host' => '127.0.0.1',
       'port' => 3306,
       'database' => 'toko_1_3',
       'username' => 'root',
       'password' => '',
   ];
   ```

5. Buka aplikasi:

   ```text
   http://localhost/export-toko/
   ```

## Setup tanpa Laragon

Setelah MySQL aktif, jalankan server bawaan PHP dari folder proyek:

```powershell
php -S localhost:8000
```

Kemudian buka <http://localhost:8000>.

## Catatan keamanan

- Aplikasi ini ditujukan untuk localhost/jaringan internal tepercaya.
- Jangan commit password database. Untuk penggunaan publik/produksi, pindahkan kredensial ke environment variable dan tambahkan autentikasi.
- File dump SQL dan data toko tidak disertakan dalam repositori.

## Struktur data

- `barang`: master barang, kategori, supplier, stok, dan harga.
- `penjualan`: header transaksi, tanggal, pelanggan, operator, dan total.
- `itempenjualan`: detail barang pada setiap transaksi.
