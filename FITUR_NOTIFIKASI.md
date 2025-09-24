# Fitur Notifikasi Pajak Kendaraan

Fitur ini akan secara otomatis memberikan notifikasi kepada pengguna admin ketika pajak kendaraan akan jatuh tempo dalam 7 hari ke depan.

## Cara Kerja

1. **Command Terjadwal**: Command `pajak:check-jatuh-tempo` akan berjalan setiap hari secara otomatis
2. **Pengecekan Database**: Command akan memeriksa semua aset dengan jenis "Kendaraan" yang tanggal pajaknya tepat 7 hari dari sekarang
3. **Notifikasi**: Jika ditemukan kendaraan yang pajaknya akan jatuh tempo, notifikasi akan dikirim ke semua user dengan role "admin"
4. **Tampilan UI**: Notifikasi akan ditampilkan di sidebar dengan badge merah yang menunjukkan jumlah notifikasi yang belum dibaca

## File yang Dibuat/Dimodifikasi

### 1. Database
- Tabel `notifications` (dibuat otomatis oleh Laravel)

### 2. Notifikasi
- `app/Notifications/PajakJatuhTempoNotification.php`: Kelas notifikasi untuk pajak jatuh tempo

### 3. Command
- `app/Console/Commands/CheckPajakJatuhTempo.php`: Command untuk memeriksa pajak jatuh tempo
- `app/Console/Kernel.php`: Penjadwalan command untuk berjalan setiap hari

### 4. Routes
- `routes/web.php`: Route untuk menampilkan dan menandai notifikasi sebagai sudah dibaca

### 5. Views
- `resources/views/notifications/index.blade.php`: Halaman untuk menampilkan daftar notifikasi
- `resources/views/layouts/app.blade.php`: Sidebar dengan ikon notifikasi dan badge

## Penggunaan

### Menjalankan Command Manual
```bash
php artisan pajak:check-jatuh-tempo
```

### Melihat Notifikasi
1. Login sebagai admin
2. Lihat sidebar, akan ada ikon bell/lonceng
3. Jika ada notifikasi belum dibaca, akan muncul badge merah dengan angka
4. Klik link "Notifikasi" untuk melihat daftar notifikasi
5. Klik "Tandai sudah dibaca" untuk menandai notifikasi sebagai sudah dibaca

### Penjadwalan Otomatis
Command sudah dijadwalkan untuk berjalan setiap hari secara otomatis. Untuk memastikan penjadwalan berjalan, pastikan cron job Laravel sudah diatur di server:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## Pengembangan Lebih Lanjut

Fitur ini dapat dikembangkan lebih lanjut dengan:
1. Menambahkan notifikasi email
2. Menambahkan pengaturan waktu reminder yang dapat dikustomisasi
3. Menambahkan notifikasi untuk jenis reminder lainnya (servis, dll)
4. Menambahkan notifikasi push/browser notification
