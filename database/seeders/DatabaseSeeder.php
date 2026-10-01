<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Facility;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminPassword = config('services.sipras.super_admin_password');

        if (app()->environment('production') && blank($superAdminPassword)) {
            throw new \RuntimeException('Set SIPRAS_SUPER_ADMIN_PASSWORD before seeding production.');
        }

        $superAdmin = User::firstOrCreate(['email' => config('services.sipras.super_admin_email')], [
            'name' => 'Super Admin SIPRAS',
            'password' => Hash::make($superAdminPassword ?? '1234567890'),
            'role' => 'super_admin',
            'status' => 'aktif',
            'phone' => '081234567890',
        ]);

        if (app()->environment('production')) {
            return;
        }

        $guru = User::updateOrCreate(['email' => 'guru@sipras.test'], [
            'name' => 'Guru SIPRAS',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'status' => 'aktif',
            'nip' => '198001012023',
            'phone' => '081111111111',
            'major' => 'Teknik Informatika',
        ]);

        $siswa = User::updateOrCreate(['email' => 'siswa@sipras.test'], [
            'name' => 'Siswa SIPRAS',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'aktif',
            'nis' => '2024001',
            'class' => 'XII RPL',
            'major' => 'Rekayasa Perangkat Lunak',
            'phone' => '082222222222',
        ]);

        $technician = User::updateOrCreate(['email' => config('services.sipras.technician_email')], [
            'name' => 'Teknisi SIPRAS',
            'password' => Hash::make(config('services.sipras.technician_password')),
            'role' => 'teknisi',
            'status' => 'aktif',
            'position' => 'Teknisi Sarana dan Prasarana',
            'phone' => '081333333333',
        ]);

        $categoryDescriptions = [
            'Fasilitas' => 'Pengaduan fasilitas sekolah secara umum.',
            'Furnitur dan Kelas' => 'Meja, kursi, papan tulis, pintu, dan perlengkapan ruang kelas.',
            'Teknologi Informasi' => 'Komputer, jaringan, printer, dan perangkat laboratorium.',
            'Teknologi Pembelajaran' => 'Proyektor, layar, speaker, dan perangkat pembelajaran.',
            'Penerangan dan Listrik' => 'Lampu, sakelar, stopkontak, dan instalasi listrik.',
            'Sanitasi dan Air' => 'Toilet, wastafel, keran, saluran air, dan kebersihan.',
            'Keamanan dan Akses' => 'Kunci, gerbang, pagar, jalur akses, dan keselamatan fasilitas.',
            'Fasilitas Umum' => 'Koridor, halaman, ruang bersama, dan fasilitas sekolah lainnya.',
        ];
        $categories = [];

        foreach ($categoryDescriptions as $name => $description) {
            $categories[$name] = Category::updateOrCreate(['nama' => $name], ['deskripsi' => $description]);
        }

        $facilityDefinitions = [
            ['LAB-001', 'Laboratorium Komputer', 'Teknologi Informasi', 'Gedung A · Laboratorium Komputer', 'rusak_ringan', 'Laboratorium komputer utama sekolah.'],
            ['EL-001', 'Lampu Ruang Kelas', 'Penerangan dan Listrik', 'Ruang Kelas XI RPL', 'rusak_ringan', 'Lampu dan sakelar ruang belajar.'],
            ['FR-001', 'Meja dan Kursi Ruang Kelas', 'Furnitur dan Kelas', 'Ruang Kelas XII RPL', 'rusak_ringan', 'Perabot untuk kegiatan belajar.'],
            ['SAN-001', 'Toilet dan Wastafel', 'Sanitasi dan Air', 'Toilet Gedung A', 'rusak_ringan', 'Fasilitas sanitasi siswa dan guru.'],
            ['PRJ-001', 'Proyektor Kelas', 'Teknologi Pembelajaran', 'Ruang Kelas X RPL', 'baik', 'Perangkat presentasi pembelajaran.'],
            ['SEC-001', 'Pintu dan Kunci Ruangan', 'Keamanan dan Akses', 'Koridor Gedung A', 'baik', 'Akses dan keamanan ruangan sekolah.'],
            ['PUB-001', 'Koridor dan Halaman', 'Fasilitas Umum', 'Area Sekolah', 'baik', 'Area sirkulasi umum sekolah.'],
        ];
        $facilities = [];

        foreach ($facilityDefinitions as [$code, $name, $categoryName, $location, $condition, $description]) {
            $facilities[$code] = Facility::updateOrCreate(['kode' => $code], [
                'kategori_id' => $categories[$categoryName]->id,
                'nama' => $name,
                'lokasi' => $location,
                'kondisi' => $condition,
                'jumlah' => 1,
                'status' => 'aktif',
                'deskripsi' => $description,
            ]);
        }

        Complaint::where('nomor', 'SP-202601010001')
            ->where('judul', 'Kursi laboratorium rusak')
            ->update(['nomor' => 'SP-00000000-0000-4000-8000-000000000001']);

        $complaint = Complaint::updateOrCreate(['nomor' => 'SP-00000000-0000-4000-8000-000000000001'], [
            'user_id' => $siswa->id,
            'kategori_id' => $categories['Fasilitas']->id,
            'sarana_id' => $facilities['LAB-001']->id,
            'judul' => 'Kursi laboratorium rusak',
            'lokasi' => 'Gedung A',
            'deskripsi' => 'Beberapa kursi di laboratorium komputer rusak dan tidak nyaman digunakan.',
            'status' => 'menunggu',
            'prioritas' => 'sedang',
            'response' => null,
            'handled_by' => $guru->id,
            'technician_id' => $technician->id,
        ]);

        $sampleComplaints = [
            ['SP-DEMO-20261001-001', 'Lampu ruang kelas mati', 'Penerangan dan Listrik', 'EL-001', 'Ruang Kelas XI RPL', 'Lampu dekat papan tulis tidak menyala dan perlu diperiksa.', 'diproses', 'sedang'],
            ['SP-DEMO-20261001-002', 'Komputer laboratorium tidak menyala', 'Teknologi Informasi', 'LAB-001', 'Laboratorium Komputer Gedung A', 'Satu unit komputer tidak dapat dinyalakan saat praktikum.', 'menunggu', 'tinggi'],
            ['SP-DEMO-20261001-003', 'Keran wastafel bocor', 'Sanitasi dan Air', 'SAN-001', 'Toilet Gedung A', 'Keran wastafel terus menetes setelah digunakan.', 'selesai', 'rendah'],
            ['SP-DEMO-20261001-004', 'Kursi ruang kelas goyang', 'Furnitur dan Kelas', 'FR-001', 'Ruang Kelas XII RPL', 'Beberapa kursi perlu dikencangkan agar aman digunakan.', 'diverifikasi', 'sedang'],
            ['SP-DEMO-20261001-005', 'Proyektor kelas tidak menampilkan gambar', 'Teknologi Pembelajaran', 'PRJ-001', 'Ruang Kelas X RPL', 'Proyektor menyala tetapi tidak menampilkan gambar dari komputer.', 'diproses', 'tinggi'],
        ];

        foreach ($sampleComplaints as [$number, $title, $categoryName, $facilityCode, $location, $description, $status, $priority]) {
            Complaint::updateOrCreate(['nomor' => $number], [
                'user_id' => $siswa->id,
                'kategori_id' => $categories[$categoryName]->id,
                'sarana_id' => $facilities[$facilityCode]->id,
                'judul' => $title,
                'lokasi' => $location,
                'deskripsi' => $description,
                'status' => $status,
                'prioritas' => $priority,
                'handled_by' => $guru->id,
                'technician_id' => $technician->id,
            ]);
        }

        Activity::firstOrCreate(['user_id' => $superAdmin->id, 'aktivitas' => 'Login ke sistem', 'referensi' => 'dashboard']);
        Activity::firstOrCreate(['user_id' => $guru->id, 'aktivitas' => 'Login ke sistem', 'referensi' => 'dashboard']);
        Activity::firstOrCreate(['user_id' => $siswa->id, 'aktivitas' => 'Membuat pengaduan', 'referensi' => $complaint->id]);

        Notification::firstOrCreate(['user_id' => $siswa->id, 'judul' => 'Pengaduan diproses', 'pesan' => 'Pengaduan Anda sedang diproses oleh guru.']);
        Notification::firstOrCreate(['user_id' => $superAdmin->id, 'judul' => 'Pengaduan baru', 'pesan' => 'Ada pengaduan baru dari siswa.']);

    }
}
