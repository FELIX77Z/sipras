<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPRAS | SMK Informatika Sumedang</title>
    <meta name="description" content="Sistem Informasi Pengaduan Sarana dan Prasarana SMK Informatika Sumedang.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/landing-regna.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-nav">
        <div class="container nav-inner">
            <a href="#beranda" class="brand" aria-label="SIPRAS beranda"><img src="{{ asset('images/ifsu-logo.png') }}" alt="Logo SMK Informatika Sumedang"><span>SIPRAS<small>SMK INFORMATIKA SUMEDANG</small></span></a>
            <nav class="nav-links" aria-label="Navigasi utama"><a href="#tentang">Tentang</a><a href="#fitur">Fitur</a><a href="#alur">Alur</a><a href="#statistik">Statistik</a><a href="#faq">FAQ</a></nav>
            <details class="mobile-nav">
                <summary aria-label="Buka navigasi"><i class="bi bi-list" aria-hidden="true"></i></summary>
                <nav class="mobile-nav-panel" aria-label="Navigasi utama">
                    <a href="#tentang">Tentang</a>
                    <a href="#fitur">Layanan</a>
                    <a href="#alur">Alur</a>
                    <a href="#statistik">Statistik</a>
                    <a href="#faq">FAQ</a>
                    <a href="#kontak">Kontak</a>
                </nav>
            </details>
            <div class="nav-actions"><a href="#kontak" class="button button-ghost">Kontak</a><a href="{{ url('/login') }}" class="button button-primary">Masuk <i class="bi bi-arrow-up-right"></i></a></div>
        </div>
    </header>
    <main>
        <section id="beranda" class="hero" aria-labelledby="hero-title">
            <div class="hero-shade">
                <div class="container hero-content">
                    <span class="eyebrow">SMK Informatika Sumedang</span>
                    <h1 id="hero-title">Fasilitas sekolah lebih terawat, dimulai dari satu laporan.</h1>
                    <p class="hero-copy">Sampaikan pengaduan sarana dan prasarana, ikuti prosesnya, dan lihat tindak lanjutnya dalam satu sistem yang terbuka.</p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="{{ url('/login') }}">Buat Pengaduan <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        <a class="hero-link" href="#tentang">Kenali SIPRAS <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                    </div>
                </div>
                <a class="hero-scroll" href="#tentang" aria-label="Gulir ke bagian tentang SIPRAS"><span></span></a>
            </div>
        </section>
        <section id="tentang" class="section about" aria-labelledby="about-title">
            <div class="container">
                <div class="section-heading" data-reveal="left">
                    <span class="section-kicker">Tentang SIPRAS</span>
                    <h2 id="about-title">Laporan jelas. Tindak lanjut terarah.</h2>
                    <p>SIPRAS membantu siswa, guru, dan pengelola sekolah menangani kebutuhan fasilitas dengan proses yang mudah dipahami.</p>
                </div>
                <div class="about-grid">
                    <div class="about-visual" data-reveal="left" role="img" aria-label="Suasana ruang belajar di sekolah">
                        <div class="visual-caption"><span>SMK Informatika Sumedang</span><strong>Ruang belajar yang nyaman untuk semua.</strong></div>
                    </div>
                    <div class="about-copy" data-reveal="right">
                        <p>Mulai dari kerusakan kecil hingga kebutuhan laboratorium, setiap laporan masuk ke alur yang sama: tercatat, diverifikasi, lalu ditindaklanjuti.</p>
                        <ul class="issue-list">
                            <li><i class="bi bi-check2" aria-hidden="true"></i> Kursi dan meja rusak</li>
                            <li><i class="bi bi-check2" aria-hidden="true"></i> Lampu dan kipas mati</li>
                            <li><i class="bi bi-check2" aria-hidden="true"></i> Komputer bermasalah</li>
                            <li><i class="bi bi-check2" aria-hidden="true"></i> Fasilitas laboratorium</li>
                        </ul>
                        <a class="text-link" href="#alur">Lihat alur pengaduan <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </section>
        <section id="fitur" class="section services" aria-labelledby="services-title">
            <div class="container">
                <div class="section-heading" data-reveal="right">
                    <span class="section-kicker">Layanan</span>
                    <h2 id="services-title">Semua kebutuhan, satu tempat.</h2>
                    <p>Dari laporan awal sampai ringkasan kondisi fasilitas, SIPRAS menjaga proses tetap ringkas dan transparan.</p>
                </div>
                <div class="feature-grid">
                    @foreach ([['bi-send-check','Pengaduan Online','Sampaikan masalah fasilitas dengan detail yang jelas.'],['bi-activity','Pantau Status','Ketahui perkembangan laporan tanpa menebak-nebak.'],['bi-buildings','Data Sarana','Informasi fasilitas sekolah tersimpan lebih terstruktur.'],['bi-bell','Notifikasi','Dapatkan kabar saat laporan mengalami perubahan.'],['bi-clock-history','Riwayat Laporan','Tinjau kembali pengaduan dan tindak lanjut sebelumnya.'],['bi-bar-chart-line','Statistik','Lihat ringkasan kondisi pengaduan secara aktual.']] as $feature)
                        <article class="feature" data-reveal="{{ $loop->iteration % 3 === 1 ? 'left' : ($loop->iteration % 3 === 2 ? 'bottom' : 'right') }}">
                            <div class="feature-icon"><i class="bi {{ $feature[0] }}" aria-hidden="true"></i></div>
                            <div><h3>{{ $feature[1] }}</h3><p>{{ $feature[2] }}</p></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="callout" aria-labelledby="callout-title">
            <div class="container callout-inner">
                <div data-reveal="left">
                    <span class="section-kicker">Mulai dari sekarang</span>
                    <h2 id="callout-title">Ada fasilitas yang perlu diperbaiki?</h2>
                    <p>Buat laporan melalui akun sekolah dan bantu kami menjaga lingkungan belajar tetap nyaman.</p>
                </div>
                <a class="button button-primary" data-reveal="right" href="{{ url('/login') }}">Laporkan Fasilitas <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </section>
        <section id="alur" class="section flow" aria-labelledby="flow-title">
            <div class="container">
                <div class="section-heading" data-reveal="top">
                    <span class="section-kicker">Alur pengaduan</span>
                    <h2 id="flow-title">Dari laporan hingga selesai.</h2>
                    <p>Setiap tahap memiliki status yang bisa dipahami oleh seluruh warga sekolah.</p>
                </div>
                <div class="timeline">
                    @foreach ([['1','Masuk','Gunakan akun sekolah untuk mengakses SIPRAS.'],['2','Laporkan','Lengkapi detail fasilitas dan masalah.'],['3','Verifikasi','Admin meninjau laporan yang masuk.'],['4','Tindak lanjut','Tim terkait melakukan penanganan.'],['5','Selesai','Hasil perbaikan tercatat di sistem.']] as $step)
                        <article class="step" data-reveal="{{ $loop->even ? 'left' : 'right' }}">
                            <div class="step-number">{{ $step[0] }}</div>
                            <h3>{{ $step[1] }}</h3>
                            <p>{{ $step[2] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        <section id="statistik" class="section statistics" aria-labelledby="stats-title">
            <div class="container">
                <div class="section-heading" data-reveal="right">
                    <span class="section-kicker">Statistik publik</span>
                    <h2 id="stats-title">Proses yang bisa dipantau.</h2>
                    <p>Ringkasan pengaduan mengikuti aktivitas SIPRAS secara aktual.</p>
                </div>
                <div class="stat-grid">
                    @foreach ($stats as $stat)
                        <div class="stat" data-reveal="{{ $loop->even ? 'bottom' : 'top' }}"><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
                    @endforeach
                </div>
            </div>
        </section>
        <section id="faq" class="section faq-section" aria-labelledby="faq-title">
            <div class="container faq">
                <div data-reveal="left">
                    <span class="section-kicker">FAQ</span>
                    <h2 id="faq-title">Yang sering ditanyakan.</h2>
                </div>
                <div class="faq-list" data-reveal="right">
                    @foreach ([['Apa itu SIPRAS?','SIPRAS adalah Sistem Informasi Pengaduan Sarana dan Prasarana milik SMK Informatika Sumedang.'],['Siapa yang dapat menggunakan SIPRAS?','Siswa dan guru dapat membuat pengaduan, sedangkan Super Admin mengelola proses tindak lanjut.'],['Bagaimana cara membuat pengaduan?','Masuk dengan akun sekolah, pilih menu Buat Pengaduan, lalu isi detail fasilitas dan masalahnya.'],['Bagaimana cara melihat status pengaduan?','Status dapat dipantau dari menu Pengaduan Saya setelah laporan berhasil dikirim.'],['Siapa yang mengelola pengaduan?','Super Admin memverifikasi laporan dan mengoordinasikan proses penyelesaiannya.']] as $faqItem)
                        <details>
                            <summary>{{ $faqItem[0] }} <i class="bi bi-plus" aria-hidden="true"></i></summary>
                            <p>{{ $faqItem[1] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
    <footer id="kontak" class="footer">
        <div class="container footer-grid" data-reveal="bottom">
            <section class="footer-school" aria-labelledby="school-footer-title">
                <a class="brand" href="#beranda"><img src="{{ asset('images/ifsu-logo.png') }}" alt="Logo IFSU"><span>SIPRAS<small>SMK INFORMATIKA SUMEDANG</small></span></a>
                <h2 id="school-footer-title">SMK Informatika Sumedang</h2>
                <p>Menjadi institusi pendidikan vokasi terdepan dalam menghasilkan SDM unggul di bidang teknologi informasi yang berakhlak mulia dan kompetitif di era global.</p>
            </section>
            <nav class="footer-quick-links" aria-label="Tautan cepat sekolah">
                <h3>Tautan Cepat</h3>
                <ul>
                    <li><a href="#tentang">Tentang</a></li>
                    <li><a href="#fitur">Fitur</a></li>
                    <li><a href="#alur">Alur</a></li>
                    <li><a href="#statistik">Statistik</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </nav>
            <section class="footer-contact-section" aria-labelledby="contact-footer-title">
                <h3 id="contact-footer-title">Hubungi Kami</h3>
                <address class="footer-contact">
                    <div><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Jl. Angkrek Situ No.19, Situ, Kec. Sumedang Utara, Kabupaten Sumedang, Jawa Barat 45323</span></div>
                    <div><i class="bi bi-telephone" aria-hidden="true"></i><a href="tel:+62261202767">(0261) 202767</a></div>
                    <div><i class="bi bi-envelope" aria-hidden="true"></i><a href="mailto:info@smkifsu.sch.id">info@smkifsu.sch.id</a></div>
                </address>
                <a class="footer-location" href="https://maps.google.com/?q=SMK+Informatika+Sumedang,+Jl.+Angkrek+Situ+No.19,+Situ,+Sumedang+Utara,+Sumedang,+Jawa+Barat+45323" target="_blank" rel="noopener noreferrer"><i class="bi bi-map" aria-hidden="true"></i>Lokasi sekolah</a>
                <iframe class="footer-map" title="Peta lokasi SMK Informatika Sumedang" src="https://www.google.com/maps?q=SMK+Informatika+Sumedang,+Jl.+Angkrek+Situ+No.19,+Situ,+Sumedang+Utara,+Sumedang,+Jawa+Barat+45323&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </section>
        </div>
        <div class="container footer-bottom"><span>&copy; {{ date('Y') }} SMK Informatika Sumedang. Seluruh Hak Cipta Dilindungi.</span><span>SIPRAS dikembangkan oleh Ikhwan. Profesional Religius.</span></div>
    </footer>
</body>
</html>