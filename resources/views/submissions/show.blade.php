<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengumpulan: {{ $submission->assignment->title }} — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif
</head>

<body>

    <!-- Background Decorative Glow -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Dinamis Sesuai Peran -->
        @if(auth()->user()->role === 'dosen')
            <x-navbar-dosen />
        @elseif(auth()->user()->role === 'admin')
            <x-navbar-admin />
        @else
            <x-layout />
        @endif

        <!-- Konten Utama Detail Pengumpulan -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            @if(auth()->user()->role === 'dosen')
                                PORTAL DOSEN &bull; EVALUASI PENGUMPULAN
                            @elseif(auth()->user()->role === 'admin')
                                PANEL ADMIN &bull; DETAIL PENGUMPULAN
                            @else
                                PORTAL MAHASISWA &bull; STATUS PENGUMPULAN
                            @endif
                        </span>
                        <h1>{{ $submission->assignment->title }}</h1>
                        <p style="font-size: 13px; font-weight: 700; color: #64748B; margin-top: 4px;">
                            {{ $submission->assignment->course->code }} &middot; {{ $submission->assignment->course->name }}
                        </p>
                    </div>
                </div>

                <div class="topbar-actions">
                    <a href="{{ route('assignments.show', $submission->assignment) }}" class="btn-secondary-action">
                        &larr; Kembali ke Detail Tugas
                    </a>
                </div>
            </header>

            <!-- Kartu Detail Pengumpulan -->
            <section class="feature-section">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon" style="background: rgba(3, 159, 250, 0.12); color: #039FFA;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Informasi Pengumpulan &amp; Nilai</h2>
                                <p>Detail berkas yang dikirimkan beserta status evaluasi dosen pengampu.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            @if ($submission->is_late)
                                <span class="badge-status badge-status-warning">Terlambat</span>
                            @else
                                <span class="badge-status badge-status-active">Tepat Waktu</span>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Metadata Pengumpulan -->
                    <div class="table-responsive">
                        <table class="custom-dosen-table">
                            <tbody>
                                <tr>
                                    <th style="width: 220px; background: #F8FAFC;">Mahasiswa</th>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background: rgba(3, 159, 250, 0.12); color: #039FFA; font-weight: 800;">
                                                {{ strtoupper(substr($submission->student->name, 0, 2)) }}
                                            </div>
                                            <div class="student-meta">
                                                <span class="student-name">{{ $submission->student->name }}</span>
                                                <span class="student-nim">{{ $submission->student->nim_nip ?? 'Mahasiswa' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #F8FAFC;">Berkas</th>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 18px;">📄</span>
                                            <strong style="color: #0F172A;">{{ $submission->original_name }}</strong>
                                            <span style="font-size: 12px; color: #64748B;">
                                                ({{ number_format($submission->file_size / 1024, 1) }} KB)
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #F8FAFC;">Dikumpulkan</th>
                                    <td>
                                        <span style="font-weight: 700; color: #334155;">
                                            {{ $submission->submitted_at->format('d M Y H:i') }}
                                            @if($submission->is_late)
                                                <span style="color: #D97706; margin-left: 4px;">(terlambat)</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #F8FAFC;">Catatan</th>
                                    <td style="color: #334155;">
                                        {{ $submission->note ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #F8FAFC;">Nilai</th>
                                    <td>
                                        @if($submission->grade)
                                            <span class="badge-status badge-status-active" style="font-size: 13px; font-weight: 800; padding: 6px 14px;">
                                                {{ $submission->grade->score }} / {{ $submission->assignment->max_score }}
                                            </span>
                                        @else
                                            <span style="color: #64748B; font-weight: 700;">Belum dinilai</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #F8FAFC;">Umpan balik</th>
                                    <td style="color: #334155;">
                                        {{ $submission->grade?->feedback ?: '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
