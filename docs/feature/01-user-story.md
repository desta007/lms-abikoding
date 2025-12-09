Saya ingin membuat aplikasi LMS belajar bahasa jepang. Berikut ini list fitur-fiturnya:

1. Login
    • Input: Email dan Kata Sandi
2. Register
    • Email
	• Nama Depan dan Nama Belakang
	• Kata Sandi dan Konfirmasi Kata Sandi
	• Nomor WhatsApp
3. Halaman Utama (Home Page)
    Menampilkan daftar kursus dan fitur pendukung:
	• List Kursus
	• Pencarian Kursus (Search)
	• Tampilan Kursus: Pilihan tampilan Grid View atau List View
	• Urutkan Berdasarkan: Rating Tertinggi, Kursus Terbaru, Kursus Terlama
	• Filter Kursus Berdasarkan: Harga, Kategori, Level, Bahasa, Instruktur
4. Menu Kursus (Instruktur)
    Dashboard
	• Jumlah siswa yang terdaftar
	• Jumlah pelajaran yang dibuat
	• Jumlah kunjungan
	• Pengguna aktif
	• Tes atau ujian yang sedang aktif/berlangsung
    
    Buat Kursus
	• Slug
	• Subjudul Kursus
	• Thumbnail (gambar sampul)
	• Materi Kursus: PDF, Gambar, Video, Rekaman, Teks
	• Kategori
	• Tentang Kursus
	• Tingkat Instruksional
	• Tentang Instruktur
	• Tombol 'Buat Kursus'

    Instruktur dapat menambahkan beberapa materi sehingga membentuk daftar bab (chapter) yang bisa diakses oleh siswa.

    Kategori
    Menampilkan level bahasa Jepang dari N5 hingga N1.
    
    Tingkat (Level)
    Berisi: Beginner, Intermediate, dan Advanced.
    
    Komentar
    Menampilkan komentar per materi agar instruktur dapat memantau hasil belajar siswa.
    
    Kelola Kursus
    Menampilkan daftar kursus yang telah dibuat dan dapat diedit.

5. Menu Kursus (Siswa)
    Header Kursus
        • Rating bintang (1–5)
        • Nama kursus
        • Jumlah peserta
        • Level kursus (Beginner / Intermediate / Advanced)
    Tab Bar Konten
        • Konten Kursus: Daftar bab (chapter) yang bisa diperluas atau disembunyikan
        • Gambaran Kursus
        • Review Kursus
        • Instruktur
        • Diskusi
    Bagian Bawah (Bottom Section)
    Menampilkan kursus lain yang serupa dalam bentuk daftar rekomendasi.
    Materi Kursus
    Saat siswa memilih kursus, ditampilkan daftar bab (misalnya Bab 1–5).
    Setiap bab berisi:
        • Video materi
        • PDF materi
        • Rekaman suara
        • Deskripsi materi
        
6. Menu Komunitas
    1. SosialHub
        • Tampilan mirip Facebook
        • Postingan siswa
        • Tentang (About)
        • Anggota (Members)
        • Kursus (Courses)
        • Bundle
        • Status profil: jumlah postingan dan jumlah poin
    2. Siaran, menampilkan postingan hasil belajar berupa:
        • Video
        • PDF
        • Voice
        • Teks
    3. Kalender
        • Pilih ikon acara
        • Tanggal
        • Waktu
        • Deskripsi acara
        • Warna acara
        • Staf acara
        • Judul acara
        • Lokasi
        • Peserta
        • Durasi
        • Banner acara
        • Tombol 'Tambah Acara'
7. Menu Admin
Berisi data calon siswa yang akan bekerja di Jepang, berdasarkan kemampuan bahasa yang diperoleh di LMS.
	• Lihat biodata siswa
	• Akses seluruh aktivitas dan posting siswa
	• Menawarkan kandidat ke perusahaan Jepang yang membutuhkan
	• Menu Pembayaran (Payment) untuk proses administrasi perekrutan

Techstack yang digunakan:

Frontend: blade templates
Authentication: Laravel breeze
File storage: local storage (filesystem)
Payment gateway: midtrans (nantinya bisa diganti ke yang lain seperti stripe)
Layout: responsive dan modern, bisa dibuka di smartphone android ataupun iphone