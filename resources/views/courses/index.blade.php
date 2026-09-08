<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah - Student Management Campus</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS via Vite sesuai lokasi resources/css/index.css -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/index.css', 'resources/js/app.js'])
    @endif
</head>
<body>

    <!-- Background Decorative Glow (selaras dengan halaman lain) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">

        <!-- 1. Panggil Komponen Layout/Sidebar -->
        <x-layout />

        <!-- 2. Konten Utama Mata Kuliah -->
        <main class="courses-content">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <button class="btn-hamburger" title="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            SEMESTER GENAP 2026 • KELOMPOK 02
                        </span>
                        <h1>Mata Kuliah Saya</h1>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="btn-back">
                    <svg class="btn-back-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </header>

            <!-- Grid Kartu Mata Kuliah -->
            <section class="courses-grid">

                @php
                    $courses = [
                        [
                            'id' => 1,
                            'kode' => 'IF301',
                            'nama' => 'Pemrograman Web Lanjut',
                            'kelas' => 'A2425',
                            'dosen' => 'Dr. Ahmad Fauzan',
                            'sks' => 3,
                            'progress' => 55,
                            'warna' => 'banner-violet',
                        ],
                        [
                            'id' => 2,
                            'kode' => 'IF302',
                            'nama' => 'Basis Data & Relasional',
                            'kelas' => 'A2526',
                            'dosen' => 'Rina Marlina, M.Kom',
                            'sks' => 3,
                            'progress' => 72,
                            'warna' => 'banner-gold',
                        ],
                        [
                            'id' => 3,
                            'kode' => 'IF305',
                            'nama' => 'Kecerdasan Buatan (AI)',
                            'kelas' => 'A2526',
                            'dosen' => 'Dr. Yusuf Pratama',
                            'sks' => 3,
                            'progress' => 40,
                            'warna' => 'banner-rose',
                        ],
                        [
                            'id' => 4,
                            'kode' => 'IF310',
                            'nama' => 'Rekayasa Perangkat Lunak',
                            'kelas' => 'A2425',
                            'dosen' => 'Siti Nurhaliza, M.T',
                            'sks' => 3,
                            'progress' => 88,
                            'warna' => 'banner-teal',
                        ],
                        [
                            'id' => 5,
                            'kode' => 'IF312',
                            'nama' => 'Jaringan Komputer',
                            'kelas' => 'E2425',
                            'dosen' => 'Budi Santoso, M.Kom',
                            'sks' => 2,
                            'progress' => 15,
                            'warna' => 'banner-blue',
                        ],
                        [
                            'id' => 6,
                            'kode' => 'IF318',
                            'nama' => 'Manajemen Proyek TI',
                            'kelas' => 'E2526',
                            'dosen' => 'Dr. Lestari Wibowo',
                            'sks' => 2,
                            'progress' => 63,
                            'warna' => 'banner-plum',
                        ],
                    ];
                @endphp

                @foreach ($courses as $item)
                    <a href="{{ route('mata-kuliah.show', ['mata_kuliah' => $item['id']]) }}" class="course-card">
                        <div class="course-banner {{ $item['warna'] }}">
                            <span class="course-tag">{{ $item['kelas'] }}</span>
                            <span class="course-go">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>

                        <div class="course-body">
                            <span class="course-code">{{ $item['kode'] }}</span>
                            <h3 class="course-name">{{ $item['nama'] }}</h3>
                            <span class="course-dosen">{{ $item['dosen'] }} • {{ $item['sks'] }} SKS</span>

                            <div class="course-progress-track">
                                <div class="course-progress-fill" style="width: {{ $item['progress'] }}%;"></div>
                            </div>
                            <span class="course-progress-label">selesai {{ $item['progress'] }}%</span>
                        </div>
                    </a>
                @endforeach

            </section>

        </main>
        <x-footer />
    </div>

</body>
</html>