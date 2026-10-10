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
                        <a href="{{ route('mahasiswa.mata-kuliah.show', $assignment->course_id) }}" class="btn-secondary-action">
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

                        @php
                            $tenggatLewat = $assignment->due_at->isPast();
                            $ditutup = $tenggatLewat && ! $assignment->allow_late;
                            $nilaiSaya = $mySubmission?->grade;
                        @endphp

                        @if (session('success'))
                            <div role="status" style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; font-weight: 700;">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div role="alert" style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; font-weight: 700;">
                                @foreach ($errors->all() as $pesan)
                                    <div>{{ $pesan }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if($mySubmission)
                            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 20px;">✓</span>
                                    <span style="font-size: 13px; font-weight: 700; color: #065F46;">
                                        Sudah dikumpulkan: <strong>{{ $mySubmission->original_name }}</strong>
                                        (<a href="{{ route('submissions.show', $mySubmission) }}" style="color: #039FFA; font-weight: 800; text-decoration: underline;">detail</a>
                                        &middot; <a href="{{ route('submissions.download', $mySubmission) }}" style="color: #039FFA; font-weight: 800; text-decoration: underline;">unduh</a>)
                                    </span>
                                </div>
                                <span class="badge-status badge-status-active">
                                    Terkumpul {{ $mySubmission->submitted_at ? $mySubmission->submitted_at->format('d M Y, H:i') : '' }}
                                </span>
                            </div>
                        @endif


                        @if ($nilaiSaya)
                            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 14px 18px; margin-bottom: 8px;">
                                <div style="font-size: 13px; font-weight: 800; color: #1E40AF;">
                                    Nilai: {{ rtrim(rtrim(number_format((float) $nilaiSaya->score, 2), '0'), '.') }} / {{ $assignment->max_score }}
                                </div>
                                @if ($nilaiSaya->feedback)
                                    <div style="font-size: 13px; color: #334155; margin-top: 6px; font-weight: 600;">{!! nl2br(e($nilaiSaya->feedback)) !!}</div>
                                @endif
                                <div style="font-size: 12px; color: #64748B; margin-top: 6px; font-weight: 600;">Tugas yang sudah dinilai tidak dapat dikumpulkan ulang.</div>
                            </div>
                        @elseif ($ditutup)
                            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 14px 18px; font-size: 13px; font-weight: 700; color: #92400E;">
                                Batas waktu pengumpulan telah berakhir dan tugas ini tidak menerima pengumpulan terlambat.
                                Hubungi dosen pengampu bila memerlukan dispensasi.
                            </div>
                        @else
                            @if ($tenggatLewat)
                                <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 12px 16px; margin-bottom: 14px; font-size: 13px; font-weight: 700; color: #92400E;">
                                    Tenggat sudah lewat. Pengumpulan tetap diterima tetapi akan ditandai terlambat.
                                </div>
                            @endif
                        <style>
                            @media (max-width: 860px) {
                                .submission-grid-layout {
                                    grid-template-columns: 1fr !important;
                                    gap: 18px !important;
                                }
                            }
                        </style>
                        <form id="submissionForm" action="{{ route('mahasiswa.assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data" class="card-form">
                            @csrf
                            <div class="submission-grid-layout" style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 24px; align-items: start;">
                                <!-- KOLOM KIRI: UNGGAH BERKAS TUGAS -->
                                <div class="submission-col-left">
                                    <label style="display: block; font-size: 13.5px; font-weight: 800; color: #1E293B; margin-bottom: 8px;">
                                        Unggah Berkas Tugas <span class="required">*</span>
                                    </label>

                                    <!-- DROPZONE INTERAKTIF -->
                                    <div class="submission-dropzone" id="submissionDropzone" style="width: 100%; border: 2px dashed rgba(3, 159, 250, 0.4); border-radius: 16px; padding: 32px 20px; background: #F8FAFC; text-align: center; cursor: pointer; transition: all 0.25s ease; position: relative; box-sizing: border-box;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #039FFA; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; transition: transform 0.2s ease;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <div style="font-size: 14px; font-weight: 800; color: #0F172A; margin-bottom: 4px;">
                                            Tarik &amp; letakkan berkas di sini, atau <span style="color: #039FFA; text-decoration: underline;">Pilih File</span>
                                        </div>
                                        <div style="font-size: 12px; font-weight: 600; color: #64748B;">
                                            Mendukung: <strong style="color: #0284C7;">PDF, DOC, DOCX, ZIP, TXT</strong> &bull; Maksimal 10 MB
                                        </div>

                                        <input type="file" id="submissionFileInput" name="file" required accept=".pdf,.doc,.docx,.zip,.txt"
                                               style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; display: block;">
                                    </div>

                                    <!-- PREVIEW CONTAINER BERKAS TUGAS -->
                                    <div id="submissionPreviewContainer" style="display: none; width: 100%; background: #FFFFFF; border: 1.5px solid rgba(3, 159, 250, 0.3); border-radius: 16px; padding: 16px 18px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); box-sizing: border-box;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                                <div id="fileTypeIcon" style="width: 40px; height: 40px; border-radius: 10px; background: #EFF6FF; color: #039FFA; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; font-weight: 900;">
                                                    DOC
                                                </div>
                                                <div style="min-width: 0;">
                                                    <div id="previewFileName" style="font-size: 13px; font-weight: 800; color: #0F172A; word-break: break-all;">-</div>
                                                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px; font-size: 11px; font-weight: 700;">
                                                        <span id="previewFileSize" style="color: #64748B;">-</span>
                                                        <span style="color: #CBD5E1;">&bull;</span>
                                                        <span style="background: #ECFDF5; color: #059669; padding: 1px 6px; border-radius: 5px; border: 1px solid #A7F3D0;">✓ Siap Dikumpulkan</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <button type="button" id="btnChangeFile" style="background: #F1F5F9; border: 1px solid #CBD5E1; color: #334155; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 7px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                                                    </svg>
                                                    Ganti
                                                </button>
                                                <button type="button" id="btnRemoveFile" style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 7px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <!-- PRATINJAU DOKUMEN INTERAKTIF (PDF / TEKS) -->
                                        <div id="interactivePreviewBox" style="display: none; margin-top: 14px; border-top: 1px solid #F1F5F9; padding-top: 12px;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                                <span style="font-size: 11.5px; font-weight: 800; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    Pratinjau Dokumen
                                                </span>
                                            </div>
                                            <div id="docPreviewHolder" style="width: 100%; height: 460px; border-radius: 10px; overflow: hidden; background: #F8FAFC; border: 1.5px solid #E2E8F0; box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);"></div>
                                        </div>
                                    </div>

                                    <div id="fileUploadError" role="alert" style="display: none; margin-top: 8px; padding: 8px 12px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; font-size: 12.5px; font-weight: 700; color: #B91C1C;"></div>
                                    @error('file')<small style="color:#B91C1C;font-weight:700;display:block;margin-top:6px;">{{ $message }}</small>@enderror
                                </div>

                                <!-- KOLOM KANAN: CATATAN PENGUMPULAN & SUBMIT -->
                                <div class="submission-col-right" style="display: flex; flex-direction: column; gap: 8px;">
                                    <label for="note" style="display: block; font-size: 13.5px; font-weight: 800; color: #1E293B;">
                                        Catatan Pengumpulan
                                    </label>
                                    <textarea id="note" name="note" class="form-textarea" rows="6" maxlength="1000" placeholder="Tuliskan catatan atau keterangan pengumpulan bila diperlukan..." style="width: 100%; min-height: 140px; box-sizing: border-box; resize: vertical;">{{ old('note') }}</textarea>

                                    <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                                        <button type="submit" class="btn-primary-action" style="padding: 10px 24px; font-size: 13.5px;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            {{ $mySubmission ? 'Kumpulkan Ulang' : 'Kumpulkan Tugas' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('submissionFileInput');
            const dropzone = document.getElementById('submissionDropzone');
            const previewContainer = document.getElementById('submissionPreviewContainer');
            const previewFileName = document.getElementById('previewFileName');
            const previewFileSize = document.getElementById('previewFileSize');
            const fileTypeIcon = document.getElementById('fileTypeIcon');
            const interactivePreviewBox = document.getElementById('interactivePreviewBox');
            const docPreviewHolder = document.getElementById('docPreviewHolder');
            const fileUploadError = document.getElementById('fileUploadError');
            const btnChangeFile = document.getElementById('btnChangeFile');
            const btnRemoveFile = document.getElementById('btnRemoveFile');

            if (!fileInput) return;

            let currentObjectUrl = null;

            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getFileBadgeConfig(ext) {
                switch(ext) {
                    case 'pdf':
                        return { label: 'PDF', bg: '#FEE2E2', color: '#DC2626' };
                    case 'doc':
                    case 'docx':
                        return { label: 'DOC', bg: '#EFF6FF', color: '#2563EB' };
                    case 'zip':
                        return { label: 'ZIP', bg: '#FEF3C7', color: '#D97706' };
                    case 'txt':
                        return { label: 'TXT', bg: '#F1F5F9', color: '#475569' };
                    default:
                        return { label: ext.toUpperCase().slice(0, 4) || 'FILE', bg: '#EFF6FF', color: '#039FFA' };
                }
            }

            function clearPreview() {
                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                    currentObjectUrl = null;
                }
                fileInput.value = '';
                previewContainer.style.display = 'none';
                interactivePreviewBox.style.display = 'none';
                docPreviewHolder.innerHTML = '';
                dropzone.style.display = 'block';
                if (fileUploadError) {
                    fileUploadError.style.display = 'none';
                    fileUploadError.textContent = '';
                }
            }

            if (btnRemoveFile) {
                btnRemoveFile.addEventListener('click', function(e) {
                    e.preventDefault();
                    clearPreview();
                });
            }

            if (btnChangeFile) {
                btnChangeFile.addEventListener('click', function(e) {
                    e.preventDefault();
                    fileInput.click();
                });
            }

            // Drag effects
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        dropzone.style.borderColor = '#039FFA';
                        dropzone.style.background = 'rgba(3, 159, 250, 0.08)';
                    });
                });
                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        dropzone.style.borderColor = 'rgba(3, 159, 250, 0.4)';
                        dropzone.style.background = '#F8FAFC';
                    });
                });
            }

            fileInput.addEventListener('change', function() {
                if (!fileInput.files || fileInput.files.length === 0) {
                    clearPreview();
                    return;
                }

                const file = fileInput.files[0];
                const ext = file.name.split('.').pop().toLowerCase();
                const allowedExts = ['pdf', 'doc', 'docx', 'zip', 'txt'];

                if (fileUploadError) {
                    fileUploadError.style.display = 'none';
                    fileUploadError.textContent = '';
                }

                if (!allowedExts.includes(ext)) {
                    fileUploadError.textContent = '✕ Format berkas tidak didukung. Harap pilih PDF, DOC, DOCX, ZIP, atau TXT.';
                    fileUploadError.style.display = 'block';
                    fileInput.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    fileUploadError.textContent = '✕ Ukuran berkas melebihi batas maksimal 10 MB.';
                    fileUploadError.style.display = 'block';
                    fileInput.value = '';
                    return;
                }

                // Update preview details
                previewFileName.textContent = file.name;
                previewFileSize.textContent = formatBytes(file.size);

                const badge = getFileBadgeConfig(ext);
                fileTypeIcon.textContent = badge.label;
                fileTypeIcon.style.background = badge.bg;
                fileTypeIcon.style.color = badge.color;

                // Handle interactive preview
                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                    currentObjectUrl = null;
                }
                docPreviewHolder.innerHTML = '';
                interactivePreviewBox.style.display = 'none';

                if (ext === 'pdf') {
                    currentObjectUrl = URL.createObjectURL(file);
                    const embed = document.createElement('embed');
                    embed.src = currentObjectUrl;
                    embed.type = 'application/pdf';
                    embed.style.width = '100%';
                    embed.style.height = '100%';
                    embed.style.border = 'none';
                    embed.style.display = 'block';
                    docPreviewHolder.appendChild(embed);
                    interactivePreviewBox.style.display = 'block';
                } else if (ext === 'txt') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const pre = document.createElement('pre');
                        pre.style.margin = '0';
                        pre.style.padding = '12px 14px';
                        pre.style.maxHeight = '200px';
                        pre.style.overflowY = 'auto';
                        pre.style.fontSize = '12px';
                        pre.style.fontFamily = 'monospace';
                        pre.style.color = '#334155';
                        pre.style.whiteSpace = 'pre-wrap';
                        pre.textContent = e.target.result.slice(0, 1500) + (e.target.result.length > 1500 ? '\n\n... (konten terpotong untuk pratinjau)' : '');
                        docPreviewHolder.appendChild(pre);
                        interactivePreviewBox.style.display = 'block';
                    };
                    reader.readAsText(file);
                }

                previewContainer.style.display = 'block';
                dropzone.style.display = 'none';
            });
        });
    </script>
</body>

</html>
