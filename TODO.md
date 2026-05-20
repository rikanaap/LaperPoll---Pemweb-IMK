# TODO - Swipe Resep Refactor (Components)

## Info
- Ubah halaman swipe rasa dan halaman filter/rekomendasi menjadi komponen under `resources/views/components/swipe-resep/...`.
- CSS dan JS tetap dipakai, tapi CSS & JS akan dibungkus/di-render via Blade component (tanpa mengubah logic JS).
- Warna card swipe menjadi **random**.

## Steps
1. Buat component Blade untuk halaman swipe rasa:
   - `resources/views/components/swipe-resep/index.blade.php`
   - Pecah menjadi subkomponen: header/info, cards stack, history drawer, dll.
2. Buat component Blade untuk halaman rekomendasi:
   - `resources/views/components/swipe-resep/filter/index.blade.php`
3. Buat component Blade untuk wrapper CSS/JS:
   - render `<link>` dan `<script>` sesuai kebutuhan (JS tetap file existing).
4. Update `resources/views/pages/swipe-resep/index.blade.php` dan `resources/views/pages/swipe-resep/filter/index.blade.php` agar cukup memanggil component baru.
5. Random color untuk card swipe rasa tanpa mengubah logic swipe JS:
   - Tambahkan styling via CSS variable + apply warna random dari Blade sesuai item (atau inline style).
   - Pastikan tetap kompatibel dengan struktur DOM yang dibuat oleh `public/js/pages/swipe-resep.js`.
6. Pastikan tidak ada perubahan logic backend/API.
7. Jalankan build/test minimal:
   - `php artisan route:list` (opsional)
   - Cek error view/asset via `php artisan serve` bila perlu.

