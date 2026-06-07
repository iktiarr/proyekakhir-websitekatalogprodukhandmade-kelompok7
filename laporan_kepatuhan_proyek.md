# Laporan Kepatuhan Spesifikasi Proyek Akhir - HandMadura

Laporan ini mengevaluasi status kepatuhan proyek website **HandMadura** terhadap seluruh ketentuan umum dan *minimum requirements* proyek akhir.

---

## Ringkasan Status Kepatuhan

| No | Ketentuan Spesifikasi | Status | Catatan & Lokasi Kode |
| :--- | :--- | :---: | :--- |
| **1** | **Ketentuan Umum Proyek** | | |
| | • Implementasi HTML & CSS | **Sesuai** | Struktur HTML5 standar dengan integrasi Tailwind CSS. |
| | • Framework CSS (Tailwind) | **Sesuai** | Menggunakan Tailwind CSS v4 CDN di [atas.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/bagian/atas.php#L36). |
| | • JavaScript (Interaktif) | **Sesuai** | Fitur ubah tema (*dark/light*), *burger menu*, dan filter pencarian instan di [bawah.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/bagian/bawah.php#L63-L118) dan [katalog.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/halaman/katalog.php#L170-L225). |
| | • PHP (Processing) | **Sesuai** | Memproses input formulir, manajemen sesi, dan alur otentikasi. |
| | • Koneksi Database | **Sesuai** | Terhubung ke database TiDB Cloud MySQL dengan enkripsi SSL di [koneksi.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/koneksi.php). |
| **2** | **Database & Logic** | | |
| | • Minimal 3 tabel saling berelasi & FK aktif | **Sesuai** | Memiliki 9 tabel berelasi aktif (`pengguna`, `kategori`, `daerah`, `produk`, `pesanan`, `detail_pesanan`, `keranjang`, `testimonial`, `log_aktivitas`). Lihat [uas_bersama_full.sql](file:///c:/Users/iktia/Music/uaspraktikumpbwd/database/uas_bersama_full.sql). |
| | • Minimal 1 entitas full CRUD | **Sesuai** | Entitas **produk** memiliki fungsi CRUD lengkap (Create, Read, Update, Delete) di panel admin [produk.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/admin/produk.php). |
| | • Memiliki minimal 2 role berbeda | **Sesuai** | Role **admin** dan **user** terimplementasi di tabel `pengguna` dan digunakan untuk pembatasan hak akses di [atas.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/bagian/atas.php#L10-L17). |
| | • Data dummy minimal 10 record per tabel | **Perlu Tindakan** | Beberapa tabel pada berkas dump SQL masih memiliki data di bawah 10 record. Namun, berkas seeder di [inisialisasi_db.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/utilitas/inisialisasi_db.php) sudah siap mengisi 10 record per tabel jika dijalankan. |
| **3** | **Form & Interface** | | |
| | • Validasi server-side (PHP) | **Sesuai** | Validasi input formulir dilakukan menggunakan PHP di [daftar.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/daftar.php#L20-L41) (verifikasi email unik & kesamaan sandi) serta [produk.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/admin/produk.php#L39-L41) (harga & stok tidak boleh negatif). |
| | • Fitur search / filter halaman list data | **Sesuai** | Halaman katalog [katalog.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/halaman/katalog.php) memiliki fitur pencarian teks dan filter berdasarkan kategori serta asal daerah (kabupaten). |
| | • Responsif (Mobile & Desktop) | **Sesuai** | Layout menggunakan grid responsif Tailwind (`grid-cols-2 lg:grid-cols-4`) dan sidebar responsif untuk perangkat mobile. |

---

## Analisis Detail & Rekomendasi Perbaikan

### 1. Masalah Data Dummy (Sangat Penting)
> [!WARNING]
> Berkas database dump saat ini ([uas_bersama_full.sql](file:///c:/Users/iktia/Music/uaspraktikumpbwd/database/uas_bersama_full.sql)) memiliki jumlah record data dummy yang **belum memenuhi syarat minimum 10 record** pada beberapa tabel utama:
> * `kategori`: Baru memiliki **5** record.
> * `daerah`: Baru memiliki **4** record.
> * `pengguna`: Baru memiliki **7** record.
> * `keranjang`: Baru memiliki **4** record.
> * `testimonial`: Baru memiliki **1** record.
> * `users` (tabel cadangan/tidak terpakai): Hanya memiliki **2** record.

**Solusi:**
Anda tidak perlu membuat data manual satu per satu. Anda telah membuat program seeder otomatis yang sangat baik di berkas [inisialisasi_db.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/utilitas/inisialisasi_db.php). Berkas ini akan secara otomatis melakukan truncate dan mengisikan minimal 10 record data dummy yang saling berelasi untuk setiap tabel.

**Rekomendasi Langkah Tindakan:**
1. Jalankan berkas seeder tersebut melalui browser dengan mengakses alamat: `http://localhost/uaspraktikumpbwd/utilitas/inisialisasi_db.php` (sesuaikan dengan nama host lokal Anda).
2. Pastikan pesan sukses berwarna hijau/biru muncul untuk seluruh seeding tabel.
3. Ekspor ulang database Anda dari TiDB Cloud / phpMyAdmin lokal Anda, lalu perbarui isi dari [uas_bersama_full.sql](file:///c:/Users/iktia/Music/uaspraktikumpbwd/database/uas_bersama_full.sql).

---

### 2. Poin Tambahan yang Sangat Baik (Kelebihan Proyek Anda)
> [!TIP]
> Proyek Anda memiliki beberapa kelebihan teknis yang melampaui standar praktikum biasa:
> 1. **Integrasi TiDB Cloud & Keamanan SSL:** Menggunakan koneksi database cloud terdistribusi TiDB dengan sertifikat SSL (`MYSQLI_CLIENT_SSL`) di [koneksi.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/koneksi.php).
> 2. **Pencarian Hybrid:** Pencarian pada katalog produk tidak hanya memproses di server-side (PHP/SQL), tetapi juga memiliki filter responsif instan di sisi klien dengan JavaScript di [katalog.php](file:///c:/Users/iktia/Music/uaspraktikumpbwd/halaman/katalog.php#L170-L225).
> 3. **Log Aktivitas:** Sistem mencatat log aktivitas penting (seperti penambahan produk, pendaftaran user baru, perubahan status pembayaran) ke dalam tabel khusus `log_aktivitas`.
> 4. **Fitur Dark Mode:** Desain premium yang mendukung transisi mode gelap dan terang (*dark mode toggler*) menggunakan penyimpanan preferensi lokal browser (*local storage*).
