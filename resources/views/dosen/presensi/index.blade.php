<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Perkuliahan — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.common.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.common.css') }}">
    @endif

    <style>
        .code-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1.5px;
            background: #F0FDF4;
            color: #15803D;
            border: 1px dashed #86EFAC;
            padding: 4px 10px;
            border-radius: 8px;
        }
        .code-pill.expired {
            background: #FEF2F2;
            color: #B91C1C;
            border-color: #FCA5A5;
        }
        .code-pill.closed {
            background: #F1F5F9;
            color: #64748B;
            border-color: #CBD5E1;
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .badge-status.active {
            background: #DCFCE7;
            color: #166534;
        }
        .badge-status.expired {
            background: #FEE2E2;
            color: #991B1B;
        }
        .badge-status.closed {
            background: #E2E8F0;
            color: #475569;
        }
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-box {
            background: #FFFFFF;
            border: 1px solid rgba(3, 159, 250, 0.15);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(3, 159, 250, 0.1);
            color: #039FFA;
            flex-shrink: 0;
        }
        .stat-icon.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        .stat-icon.warning {
            background: rgba(249, 184, 4, 0.15);
            color: #D97706;
        }
        .stat-icon.purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }
        .stat-num {
            font-size: 22px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.2;
        }
        .stat-text {
            font-size: 13px;
            color: #64748B;
            font-weight: 600;
        }
        .btn-action-sm {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 8px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-primary-sm {
            background: #039FFA;
            color: #FFF;
        }
        .btn-primary-sm:hover {
            background: #0284C7;
            color: #FFF;
        }
        .btn-outline-sm {
            background: #FFF;
            border-color: #CBD5E1;
            color: #334155;
        }
        .btn-outline-sm:hover {
            background: #F8FAFC;
            border-color: #94A3B8;
        }
        .btn-warning-sm {
            background: #FEF3C7;
            color: #92400E;
            border-color: #FDE68A;
        }
        .btn-warning-sm:hover {
            background: #FDE68A;
        }
        .btn-danger-sm {
            background: #FEE2E2;
            color: #991B1B;
            border-color: #FECACA;
        }
        .btn-danger-sm:hover {
            background: #FECACA;
        }
        /* Modal Custom */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.open {
            display: flex;
        }
        .modal-card {
            background: #FFF;
            border-radius: 20px;
            width: 100%;
            max-width: 540px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: modalFadeIn 0.25s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-head {
            padding: 20px 24px;
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-head h3 {
            font-size: 18px;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-foot {
            padding: 16px 24px;
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #0F172A;
            background: #FFF;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #039FFA;
            box-shadow: 0 0 0 3px rgba(3, 159, 250, 0.15);
        }
        .code-input-preview {
            display: flex;
            gap: 8px;
        }
        .code-input-preview input {
            font-family: 'Consolas', monospace;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .btn-refresh-code {
            padding: 0 14px;
            background: #EFF6FF;
            color: #039FFA;
            border: 1px solid rgba(3, 159, 250, 0.3);
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .alert-box {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- 1. Navbar Dosen -->
        <x-navbar-dosen />

        <!-- 2. Konten Utama Presensi Dosen -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Presensi Perkuliahan Berbasis Kode Unik</h1>
                    </div>
                </div>

                <!-- Tombol Buka Sesi Baru -->
                <div class="topbar-right">
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-weight: 800; border-radius: 12px; background: #039FFA; color: #FFF; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(3, 159, 250, 0.3);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Buka Sesi Presensi Baru
                    </button>
                </div>
            </header>

            @if (session('success'))
                <div class="alert-box alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $activeSessionsCount = $sessions->filter(fn ($s) => $s->isOpen())->count();
                $totalSessionsCount = $sessions->count();
                $totalAttendeesCount = $sessions->sum('attendances_count');
                $selectedCourse = $courses->firstWhere('id', $selectedCourseId);
            @endphp

            <!-- Statistik Singkat -->
            <div class="stats-overview">
                <div class="stat-box">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-num">{{ $totalSessionsCount }}</div>
                        <div class="stat-text">Total Sesi Presensi</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-num">{{ $activeSessionsCount }}</div>
                        <div class="stat-text">Sesi Sedang Aktif</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-num">{{ $totalAttendeesCount }}</div>
                        <div class="stat-text">Total Mahasiswa Hadir</div>
                    </div>
                </div>
            </div>

            <!-- Filter Mata Kuliah & Pencarian -->
            <div style="background: #FFF; border-radius: 16px; border: 1px solid rgba(3, 159, 250, 0.15); padding: 18px 24px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <span style="font-size: 14px; font-weight: 700; color: #475569;">Pilih Mata Kuliah:</span>
                    <form method="GET" action="{{ route('dosen.presensi.index') }}" id="filterForm">
                        <select name="course_id" class="form-control" style="width: auto; min-width: 280px; font-weight: 700; cursor: pointer;" onchange="document.getElementById('filterForm').submit()">
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}" {{ (string)$selectedCourseId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }} ({{ $c->students_count }} Mhs)
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div style="font-size: 13px; color: #64748B; font-weight: 600;">
                    Menampilkan <strong>{{ $sessions->count() }}</strong> sesi presensi
                </div>
            </div>

            <!-- Tabel Daftar Sesi Presensi -->
            <div style="background: #FFF; border-radius: 18px; border: 1px solid rgba(3, 159, 250, 0.18); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); overflow: hidden;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#039FFA" stroke-width="2.3">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Daftar Riwayat Sesi Presensi Kelas
                    </h2>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                <th style="padding: 14px 20px;">Pertemuan / Sesi</th>
                                <th style="padding: 14px 16px;">Kode Unik</th>
                                <th style="padding: 14px 16px;">Waktu & Masa Berlaku</th>
                                <th style="padding: 14px 16px;">Status</th>
                                <th style="padding: 14px 16px;">Kehadiran</th>
                                <th style="padding: 14px 20px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sessions as $session)
                                @php
                                    $isOpen = $session->isOpen();
                                    $isExpired = $session->isExpired();
                                    $statusClass = $isOpen ? 'active' : ($isExpired ? 'expired' : 'closed');
                                    $codePillClass = $isOpen ? '' : ($isExpired ? 'expired' : 'closed');
                                    $enrolledCount = $session->course->students_count ?? $session->course->students()->count();
                                @endphp
                                <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 16px 20px;">
                                        <div style="font-weight: 800; color: #0F172A; font-size: 15px;">
                                            {{ $session->title }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748B; margin-top: 2px;">
                                            {{ $session->course->code }} &bull; {{ $session->course->name }}
                                        </div>
                                        @if ($session->description)
                                            <div style="font-size: 12px; color: #94A3B8; margin-top: 4px; font-style: italic;">
                                                "{{ Str::limit($session->description, 50) }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 16px;">
                                        <span class="code-pill {{ $codePillClass }}" title="Kode presensi mahasiswa">
                                            {{ $session->code }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px 16px;">
                                        <div style="font-size: 13px; font-weight: 700; color: #1E293B;">
                                            {{ $session->start_time->format('d/m/Y') }} ({{ $session->start_time->format('H:i') }} - {{ $session->expires_at->format('H:i') }})
                                        </div>
                                        <div style="font-size: 12px; font-weight: 600; margin-top: 2px; color: {{ $isOpen ? '#059669' : ($isExpired ? '#DC2626' : '#64748B') }};">
                                            {{ $session->remainingTimeForHumans() }}
                                        </div>
                                    </td>
                                    <td style="padding: 16px 16px;">
                                        <span class="badge-status {{ $statusClass }}">
                                            @if ($isOpen)
                                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A; display: inline-block;"></span>
                                                Aktif
                                            @elseif ($isExpired)
                                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #DC2626; display: inline-block;"></span>
                                                Kedaluwarsa
                                            @else
                                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #64748B; display: inline-block;"></span>
                                                Ditutup
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding: 16px 16px;">
                                        <div style="display: flex; align-items: baseline; gap: 6px;">
                                            <span style="font-size: 16px; font-weight: 800; color: #0F172A;">{{ $session->attendances_count }}</span>
                                            <span style="font-size: 12px; color: #64748B;">/ {{ $enrolledCount }} mhs</span>
                                        </div>
                                    </td>
                                    <td style="padding: 16px 20px; text-align: right;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                            <a href="{{ route('dosen.presensi.show', $session) }}" class="btn-action-sm btn-primary-sm" title="Buka Detail & Daftar Kehadiran">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Detail & Hadir
                                            </a>

                                            <!-- Toggle Tutup/Buka -->
                                            <form method="POST" action="{{ route('dosen.presensi.toggle', $session) }}" style="display: inline;">
                                                @csrf
                                                @if ($session->is_active)
                                                    <button type="submit" class="btn-action-sm btn-outline-sm" title="Tutup Sesi Sekarang">
                                                        Tutup
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn-action-sm btn-outline-sm" style="color: #039FFA; border-color: #039FFA;" title="Buka Kembali Sesi">
                                                        Buka
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Hapus -->
                                            <form method="POST" action="{{ route('dosen.presensi.destroy', $session) }}" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi presensi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-sm btn-danger-sm" title="Hapus Sesi">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 48px 24px; text-align: center; color: #94A3B8;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin: 0 auto 12px; display: block;">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <div style="font-size: 15px; font-weight: 700; color: #475569;">Belum ada sesi presensi untuk mata kuliah ini.</div>
                                        <div style="font-size: 13px; margin-top: 4px;">Klik tombol "Buka Sesi Presensi Baru" di atas untuk membuat sesi presensi.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

    <!-- Modal Buka Sesi Presensi Baru -->
    <div class="modal-overlay" id="createModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>Buka Sesi Presensi Baru</h3>
                <button type="button" onclick="closeCreateModal()" style="background: transparent; border: none; font-size: 20px; font-weight: 700; color: #94A3B8; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="{{ route('dosen.presensi.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="course_id">Mata Kuliah</label>
                        <select name="course_id" id="course_id" class="form-control" required>
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}" {{ (string)$selectedCourseId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="title">Judul / Topik Pertemuan <span style="color: #EF4444;">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Pertemuan 1 — Kontrak Perkuliahan" required>
                    </div>

                    <div class="form-group">
                        <label for="code">Kode Unik Presensi</label>
                        <div class="code-input-preview">
                            <input type="text" name="code" id="code" class="form-control" value="{{ $generatedCode }}" maxlength="16" placeholder="Kode Unik">
                            <button type="button" class="btn-refresh-code" onclick="generateNewCode()" title="Acak kode baru">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <polyline points="1 20 1 14 7 14"></polyline>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                </svg>
                                Acak
                            </button>
                        </div>
                        <small style="font-size: 11px; color: #64748B; margin-top: 4px; display: block;">
                            Mahasiswa wajib memasukkan kode ini untuk mencatat kehadiran.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="duration_minutes">Masa Berlaku Presensi <span style="color: #EF4444;">*</span></label>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 8px;">
                            <button type="button" class="btn-action-sm btn-outline-sm duration-preset" onclick="setDuration(15, this)" style="justify-content: center;">15 Menit</button>
                            <button type="button" class="btn-action-sm btn-outline-sm duration-preset" onclick="setDuration(30, this)" style="justify-content: center; background: #EFF6FF; border-color: #039FFA; color: #0284C7;">30 Menit</button>
                            <button type="button" class="btn-action-sm btn-outline-sm duration-preset" onclick="setDuration(45, this)" style="justify-content: center;">45 Menit</button>
                            <button type="button" class="btn-action-sm btn-outline-sm duration-preset" onclick="setDuration(60, this)" style="justify-content: center;">60 Menit</button>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" value="30" min="1" max="1440" required>
                            <span style="font-size: 13px; font-weight: 700; color: #64748B; white-space: nowrap;">Menit sejak dibuka</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="description">Catatan Tambahan (Opsional)</label>
                        <textarea name="description" id="description" class="form-control" rows="2" placeholder="Catatan instruksi untuk mahasiswa..."></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-action-sm btn-outline-sm" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" class="btn-action-sm btn-primary-sm" style="padding: 9px 18px; font-size: 13px;">
                        Buka Sesi Presensi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.add('open');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('open');
        }

        function generateNewCode() {
            const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            let res = '';
            for (let i = 0; i < 6; i++) {
                res += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = res;
        }

        function setDuration(mins, btn) {
            document.getElementById('duration_minutes').value = mins;
            document.querySelectorAll('.duration-preset').forEach(b => {
                b.style.background = '#FFF';
                b.style.borderColor = '#CBD5E1';
                b.style.color = '#334155';
            });
            btn.style.background = '#EFF6FF';
            btn.style.borderColor = '#039FFA';
            btn.style.color = '#0284C7';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCreateModal();
        });
    </script>

</body>

</html>
