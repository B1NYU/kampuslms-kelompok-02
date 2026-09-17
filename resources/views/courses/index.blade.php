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
    <div class="bg-shape bg-shape-3"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">

        <!-- 1. Panggil Komponen Layout/Sidebar -->
        <x-layout />

        <!-- 2. Konten Utama Mata Kuliah -->
        <main class="courses-content">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            SEMESTER GENAP 2026 • KELOMPOK 02
                        </span>
                        <h1>Mata Kuliah Saya</h1>
                    </div>
                </div>
            </header>

            <!-- Grid Kartu Mata Kuliah -->
            <section class="courses-grid">

                @php
                    $dbCourses = $courses ?? \App\Models\Course::with(['lecturer', 'students', 'assignments'])->get();
                    $colorList = ['banner-violet', 'banner-gold', 'banner-rose', 'banner-teal', 'banner-blue', 'banner-plum'];
                @endphp

                @forelse ($dbCourses as $idx => $item)
                    @php
                        $warna = $colorList[$idx % count($colorList)];
                        $studentCount = $item->students->count();
                        $assignCount = $item->assignments->count();
                    @endphp
                    <a href="{{ route('mata-kuliah.show', ['mata_kuliah' => $item->id]) }}" class="course-card">
                        <div class="course-banner {{ $warna }}">
                            <span class="course-tag">{{ $studentCount }} Mahasiswa</span>
                            <span class="course-go">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>

                        <div class="course-body">
                            <span class="course-code">{{ $item->code }}</span>
                            <h3 class="course-name">{{ $item->name }}</h3>
                            <span class="course-dosen">{{ $item->lecturer?->name ?? 'Dosen Pengampu' }} • {{ $item->sks }} SKS</span>

                            <div style="margin-top: 4px; font-size: 11.5px; color: #8E6570; font-weight: 700;">
                                📝 {{ $assignCount }} Tugas Terdaftar
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #94A3B8;">
                        Belum ada data mata kuliah di database.
                    </div>
                @endforelse

            </section>

        </main>
        <x-footer />
    </div>

</body>
</html>