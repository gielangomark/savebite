# Deploy SurplusBite ke Arenhost (Langkah Jelas Dari Awal Sampai Live)

Target domain production:
- https://savebite.gielangomark.my.id

Panduan ini dibuat untuk alur cPanel shared hosting.

## 0. Prasyarat sebelum mulai

1. Domain sudah aktif di Arenhost.
2. SSL untuk domain sudah aktif (AutoSSL atau Let's Encrypt).
3. Sudah punya data berikut:
- Username cPanel
- Password cPanel
- Hostname database MySQL (biasanya localhost)
- Kredensial Midtrans (gunakan yang sama seperti kemarin)
- Kredensial email SMTP (jika fitur email dipakai)

## 1. Siapkan project di komputer lokal

Jalankan di folder project lokal:

```powershell
.\scripts\prepare-arenhost-deploy.ps1
```

Hasil yang harus ada sebelum upload:
1. Folder public/build ada dan berisi asset.
2. Folder vendor ada.
3. File .env.arenhost.example ada.

## 2. Buat file deploy ZIP

1. Script di langkah sebelumnya membuat ZIP di `deploy/savebite-arenhost.zip`.
2. Pastikan file berikut ikut:
- artisan
- app
- bootstrap
- config
- database
- public
- resources
- routes
- storage
- vendor
- composer.json
- .env.arenhost.example (hanya template; tidak berisi kredensial)

ZIP sudah mengecualikan `.env`, `node_modules`, `public/hot`, cache Blade, log, dan folder test.

## 3. Setup domain di cPanel Arenhost

1. Login ke cPanel.
2. Buka menu Domains.
3. Tambahkan atau pilih domain savebite.gielangomark.my.id.
4. Atur document root dengan salah satu opsi di bawah.

## 4. Pilih struktur document root (wajib dipahami)

## Opsi A (paling aman dan direkomendasikan)

Document root langsung ke folder public Laravel.

Contoh:
- Project: /home/username/surplusbite
- Document root domain: /home/username/surplusbite/public

Keuntungan:
1. Tidak perlu edit index.php.
2. Risiko salah path lebih kecil.

## Opsi B (kalau Opsi A tidak tersedia)

1. Simpan source Laravel di luar public_html, contoh: /home/username/surplusbite
2. Pindahkan isi folder public Laravel ke public_html domain.
3. Edit public_html/index.php agar path ke autoload.php dan bootstrap/app.php mengarah ke /home/username/surplusbite.

Catatan: Opsi B lebih rawan salah path dan error 500.

## 5. Upload dan extract project

1. Di File Manager cPanel, masuk ke folder target project (contoh /home/username).
2. Upload file ZIP.
3. Extract ZIP.
4. Pastikan struktur akhir benar, contoh:
- /home/username/surplusbite/artisan
- /home/username/surplusbite/public/index.php

## 6. Buat database MySQL di cPanel

1. Buka menu MySQL Databases.
2. Buat database baru.
3. Buat user database baru.
4. Assign user ke database dengan ALL PRIVILEGES.
5. Catat nilai ini:
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- DB_HOST (umumnya localhost)
- DB_PORT (umumnya 3306)

## 7. Siapkan file environment production

1. Di server, copy .env.arenhost.example menjadi .env.
2. Isi nilai penting berikut di .env:
- APP_ENV=production
- APP_DEBUG=false
- APP_URL=https://savebite.gielangomark.my.id
- DB_CONNECTION=mysql
- DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- SESSION_SECURE_COOKIE=true
- DUITKU_MERCHANT_CODE dan DUITKU_API_KEY (ambil dari dashboard Duitku; jangan masukkan ke Git)
- DUITKU_IS_PRODUCTION=false saat uji sandbox, lalu true saat live
- GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI untuk login Google
- APP_TIMEZONE=Asia/Jakarta

Jika APP_KEY masih kosong, generate di langkah terminal.

## 8. Jalankan perintah terminal di hosting

Gunakan Terminal cPanel atau SSH, lalu jalankan:

```bash
cd /home/username/surplusbite
php artisan key:generate
php artisan storage:link
php artisan migrate --force
composer run deploy:optimize
```

Jika key sudah terisi dari lokal, perintah key:generate bisa dilewati.

## 9. Set permission folder penting

Pastikan writable:
1. storage
2. bootstrap/cache

Di shared hosting biasanya owner sudah benar setelah extract.
Kalau error write cache/session/log, perbaiki permission lewat File Manager.

## 10. Tambahkan cron Laravel scheduler

Masuk menu Cron Jobs, tambah:

```bash
* * * * * /usr/bin/php /home/username/surplusbite/artisan schedule:run >> /dev/null 2>&1
```

Sesuaikan path PHP jika Arenhost memakai path berbeda.

## 11. Set callback Duitku

Pastikan endpoint callback di Duitku mengarah ke:
- https://savebite.gielangomark.my.id/duitku/callback

Aktifkan API Disbursement di akun Duitku jika merchant akan menarik saldo.

## 12. Uji website setelah live

Uji minimal skenario ini:
1. Buka homepage.
2. Register dan login.
3. Checkout dan create order.
4. Pembayaran Duitku dan callback sukses.
5. OTP email dan login Google.
5. Riwayat pesanan di /history.
6. Upload gambar produk.

## 13. Checklist akhir go-live

1. APP_DEBUG sudah false.
2. APP_URL sudah https domain production.
3. .env tidak bisa diakses dari browser.
4. Folder public/build sudah ada.
5. storage link aktif.
6. Migrasi selesai tanpa error.
7. Cron scheduler aktif.

## Troubleshooting cepat

## Error 500
1. Cek storage/logs/laravel.log.
2. Cek APP_KEY sudah terisi.
3. Cek .env valid.
4. Cek permission storage dan bootstrap/cache.

## CSS atau JS tidak muncul
1. Pastikan npm run build sudah dijalankan di lokal.
2. Pastikan public/build ikut ter-upload.
3. Jalankan ulang composer run deploy:optimize.

## Gagal koneksi database
1. Cek DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD.
2. Pastikan user database sudah di-assign ALL PRIVILEGES.

## Gambar upload tidak tampil
1. Jalankan php artisan storage:link.
2. Cek folder storage/app/public terisi file.
