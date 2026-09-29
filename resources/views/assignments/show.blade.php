<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} — Portal KampusLMS</title>

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

        <!-- Konten Utama Detail Tugas & Pengumpulan -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            @if(auth()->user()->role === 'dosen')
                                PORTAL DOSEN &bull; DETAIL PENGUMPULAN TUGAS
                            @elseif(auth()->user()->role === 'admin')
                                PANEL ADMIN &bull; DETAIL TUGAS &amp; PENGUMPULAN
                            @else
                                PORTAL MAHASISWA &bull; DETAIL PENUGASAN KULIAH
                            @endif
                        </span>
                        <h1>{{ $assignment->title }}</h1>
                    </div>
                </div>

                <div class="topbar-actions">
                    @if(auth()->user()->role === 'dosen')
                        <a href="{{ route('dosen.tugas') }}" class="btn-secondary-action">
                            &larr; Kembali ke Kelola Tugas
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn-secondary-action">
                            &larr; Kembali ke Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('mata-kuliah.show', $assignment->course_id) }}" class="btn-secondary-action">
                            &larr; Kembali ke Mata Kuliah
                        </a>
                    @endif
                </div>
            </header>

            <!-- 1. Kartu Informasi & Instruksi Tugas -->
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
                                <h2>Informasi &amp; Instruksi Tugas</h2>
                                <p><strong>{{ $assignment->course->code }}</strong> &middot; {{ $assignment->course->name }} &middot; Dosen: {{ $assignment->course->lecturer?->name ?? 'Dosen Pengampu' }}</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <span class="badge-status {{ $assignment->due_at->isPast() ? 'badge-status-warning' : 'badge-status-active' }}">
                                {{ $assignment->due_at->isPast() ? 'Batas Waktu Lewat' : 'Tugas Berjalan' }}
                            </span>
                            <span class="section-header-badge">Nilai Maks: {{ $assignment->max_score }}</span>
                        </div>
                    </div>

                    <!-- Ringkasan Info Detail -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px;">
                        <div style="background: #F8FAFC; border: 1px solid rgba(3, 159, 250, 0.14); border-radius: 12px; padding: 14px 16px;">
                            <span style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Tenggat Pengumpulan</span>
                            <span style="display: block; font-size: 14px; font-weight: 800; color: #0F172A; margin-top: 4px;">{{ $assignment->due_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div style="background: #F8FAFC; border: 1px solid rgba(3, 159, 250, 0.14); border-radius: 12px; padding: 14px 16px;">
                            <span style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Skor Maksimal</span>
                            <span style="display: block; font-size: 14px; font-weight: 800; color: #039FFA; margin-top: 4px;">{{ $assignment->max_score }} Poin</span>
                        </div>
                        <div style="background: #F8FAFC; border: 1px solid rgba(3, 159, 250, 0.14); border-radius: 12px; padding: 14px 16px;">
                            <span style="display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Status Pengumpulan</span>
                            <span style="display: block; font-size: 14px; font-weight: 800; color: #10B981; margin-top: 4px;">
                                @if(auth()->user()->role === 'mahasiswa')
                                    {{ $mySubmission ? 'Sudah Dikumpulkan' : 'Belum Dikumpulkan' }}
                                @else
                                    {{ $submissions->count() }} Berkas Masuk
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Petunjuk / Instruksi -->
                    <div style="background: #FFFFFF; border: 1px solid rgba(3, 159, 250, 0.18); border-radius: 12px; padding: 16px 20px;">
                        <span style="display: block; font-size: 11.5px; font-weight: 800; color: #039FFA; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
                            Deskripsi &amp; Instruksi Pengerjaan:
                        </span>
                        <div style="font-size: 13.5px; color: #334155; line-height: 1.6; font-weight: 600;">
                            {!! nl2br(e($assignment->instructions)) !!}
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. Bagian Pengumpulan (Berdasarkan Role) -->
            @if(auth()->user()->role === 'mahasiswa')
                <section class="feature-section" style="margin-top: 24px;">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-left">
                                <div class="section-header-icon" style="background: rgba(3, 159, 250, 0.12); color: #039FFA;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                </div>
                                <div class="section-header-text">
                                    <h2>Pengumpulan Saya</h2>
                                    <p>Unggah atau perbarui berkas jawaban tugas Anda sebelum batas tenggat berakhir.</p>
                                </div>
                            </div>
                            <span class="section-header-badge">{{ $mySubmission ? 'Sudah Dikumpulkan' : 'Menunggu Pengumpulan' }}</span>
                        </div>

                        @if($mySubmission)
                            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 20px;">✓</span>
                                    <span style="font-size: 13px; font-weight: 700; color: #065F46;">
                                        Sudah dikumpulkan: <strong>{{ $mySubmission->original_name }}</strong>
                                        (<a href="{{ route('submissions.show', $mySubmission) }}" style="color: #039FFA; font-weight: 800; text-decoration: underline;">detail</a>)
                                    </span>
                                </div>
                                <span class="badge-status badge-status-active">
                                    Terkumpul {{ $mySubmission->submitted_at ? $mySubmission->submitted_at->format('d M Y, H:i') : '' }}
                                </span>
                            </div>
                        @endif

                        <form action="{{ route('assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data" class="card-form">
                            @csrf
                            <div class="form-group">
                                <label for="file">
                                    Berkas (pdf, doc, docx, zip, txt; maks 10 MB) <span class="required">*</span>
                                </label>
                                <input type="file" id="file" name="file" class="form-control" required style="padding-top: 6px;">
                            </div>

                            <div class="form-group">
                                <label for="note">Catatan</label>
                                <textarea id="note" name="note" class="form-textarea" rows="3" placeholder="Tuliskan catatan atau keterangan pengumpulan bila diperlukan...">{{ old('note') }}</textarea>
                            </div>

                            <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                                <button type="submit" class="btn-primary-action">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    {{ $mySubmission ? 'Kumpulkan Ulang' : 'Kumpulkan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @else
                <section class="feature-section" style="margin-top: 24px;">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-left">
                                <div class="section-header-icon" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                                <div class="section-header-text">
                                    <h2>Pengumpulan Mahasiswa ({{ $submissions->count() }})</h2>
                                    <p>Daftar seluruh mahasiswa yang telah mengirimkan berkas pengumpulan tugas ini.</p>
                                </div>
                            </div>
                            <span class="section-header-badge">Tabel Pengumpulan</span>
                        </div>

                        <div class="table-responsive">
                            <table class="custom-dosen-table">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Berkas</th>
                                        <th>Waktu Pengumpulan</th>
                                        <th>Nilai</th>
                                        <th style="text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($submissions as $s)
                                        @php
                                            $name = $s->student->name;
                                            $words = explode(' ', trim($name));
                                            $initials = strtoupper(substr($words[0] ?? 'M', 0, 1) . substr($words[1] ?? '', 0, 1));
                                            if (strlen($initials) === 1) $initials .= strtoupper(substr($words[0] ?? 'M', 1, 1));
                                            $palette = [
                                                ['bg' => 'rgba(3, 159, 250, 0.12)', 'color' => '#039FFA'],
                                                ['bg' => 'rgba(249, 184, 4, 0.14)', 'color' => '#D97706'],
                                                ['bg' => 'rgba(50, 179, 241, 0.14)', 'color' => '#0284C7'],
                                                ['bg' => 'rgba(16, 185, 129, 0.14)', 'color' => '#10B981'],
                                                ['bg' => 'rgba(249, 99, 5, 0.12)', 'color' => '#F96305'],
                                            ];
                                            $color = $palette[$s->user_id % 5];
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background: {{ $color['bg'] }}; color: {{ $color['color'] }}; font-weight: 800;">
                                                        {{ $initials }}
                                                    </div>
                                                    <div class="student-meta">
                                                        <span class="student-name">{{ $s->student->name }}</span>
                                                        <span class="student-nim">{{ $s->student->nim_nip ?? ('NIM: 1024' . str_pad($s->user_id, 4, '0', STR_PAD_LEFT)) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span style="font-size: 16px;">📄</span>
                                                    <div>
                                                        <span style="font-weight: 700; color: #0F172A; display: block;">{{ $s->original_name }}</span>
                                                        @if($s->file_size)
                                                            <span style="font-size: 11px; color: #64748B;">{{ number_format($s->file_size / 1024, 1) }} KB</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: #334155;">
                                                    {{ $s->submitted_at ? $s->submitted_at->format('d M Y, H:i') : '-' }}
                                                </span>
                                                @if($s->is_late)
                                                    <span class="badge-status badge-status-warning" style="margin-left: 6px; font-size: 10px;">Terlambat</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($s->grade)
                                                    <span class="badge-status badge-status-active">{{ $s->grade->score }} / {{ $assignment->max_score }}</span>
                                                @else
                                                    <span style="color: #64748B; font-weight: 700;">{{ $s->grade?->score ?? '-' }}</span>
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                <a href="{{ route('submissions.show', $s) }}" class="btn-secondary-action" style="font-size: 11px; padding: 6px 14px;">
                                                    Lihat &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 36px 16px; color: #64748B; font-weight: 700;">
                                                Belum ada pengumpulan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

        </main>

        <x-footer />
    </div>

</body>

</html>
