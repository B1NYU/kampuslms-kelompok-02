<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} — Presensi Kelas KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.common.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.common.css') }}">
    @endif

    <style>
        .code-display-card {
            background: linear-gradient(135deg, #0284C7 0%, #039FFA 50%, #38BDF8 100%);
            border-radius: 20px;
            padding: 32px;
            color: #FFFFFF;
            box-shadow: 0 10px 30px rgba(3, 159, 250, 0.25);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .code-display-card::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }
        .code-big-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            border: 2px dashed rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 16px 28px;
            display: inline-flex;
            align-items: center;
            gap: 16px;
            margin: 16px 0;
        }
        .code-big-text {
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 44px;
            font-weight: 900;
            letter-spacing: 6px;
            color: #FFFFFF;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-copy-code {
            background: #FFFFFF;
            color: #0284C7;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.15s, background 0.15s;
        }
        .btn-copy-code:hover {
            background: #F0F9FF;
            transform: translateY(-1px);
        }
        .btn-copy-code:active {
            transform: translateY(1px);
        }
        .status-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge-pill.open {
            background: rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .status-badge-pill.expired {
            background: #FEE2E2;
            color: #991B1B;
        }
        .status-badge-pill.closed {
            background: #E2E8F0;
            color: #475569;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card-custom {
            background: #FFFFFF;
            border: 1px solid rgba(3, 159, 250, 0.15);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }
        .stat-card-custom .val {
            font-size: 26px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.1;
        }
        .stat-card-custom .lbl {
            font-size: 13px;
            font-weight: 600;
            color: #64748B;
            margin-top: 4px;
        }
        .tab-btn {
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: #64748B;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active {
            color: #039FFA;
            border-bottom-color: #039FFA;
        }
        .btn-status-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
        }
        .btn-status-badge.hadir { background: #DCFCE7; color: #166534; }
        .btn-status-badge.izin { background: #E0F2FE; color: #0369A1; }
        .btn-status-badge.sakit { background: #FEF3C7; color: #92400E; }
        .btn-status-badge.alpa { background: #FEE2E2; color: #991B1B; }

        .btn-quick-mark {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
        }
        .btn-qm-hadir { background: #DCFCE7; color: #166534; border-color: #86EFAC; }
        .btn-qm-hadir:hover { background: #BBF7D0; }
        .btn-qm-izin { background: #E0F2FE; color: #0369A1; border-color: #BAE6FD; }
        .btn-qm-izin:hover { background: #BAE6FD; }
        .btn-qm-sakit { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
        .btn-qm-sakit:hover { background: #FDE68A; }
        .btn-qm-alpa { background: #FEE2E2; color: #991B1B; border-color: #FECACA; }
        .btn-qm-alpa:hover { background: #FECACA; }

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
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- 1. Navbar Dosen -->
        <x-navbar-dosen />

        <!-- 2. Konten Utama Detail Presensi -->
        <main class="dosen-content">

            <!-- Breadcrumb Navigation -->
            <div style="margin-bottom: 16px;">
                <a href="{{ route('dosen.presensi.index', ['course_id' => $course->id]) }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #039FFA; text-decoration: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Daftar Presensi ({{ $course->code }})
                </a>
            </div>

            @if (session('success'))
                <div class="alert-box alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                $isOpen = $session->isOpen();
                $isExpired = $session->isExpired();
            @endphp

            <!-- Card Utama: Showcase Kode Unik Presensi -->
            <div class="code-display-card">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                            {{ $course->code }} &bull; {{ $course->name }} ({{ $course->sks }} SKS)
                        </div>
                        <h1 style="font-size: 26px; font-weight: 900; margin: 4px 0 8px 0; color: #FFF;">
                            {{ $session->title }}
                        </h1>
                        @if ($session->description)
                            <div style="font-size: 14px; opacity: 0.9; max-width: 600px;">
                                {{ $session->description }}
                            </div>
                        @endif
                    </div>

                    <div>
                        @if ($isOpen)
                            <span class="status-badge-pill open">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; display: inline-block;"></span>
                                Sesi Aktif
                            </span>
                        @elseif ($isExpired)
                            <span class="status-badge-pill expired">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #DC2626; display: inline-block;"></span>
                                Masa Berlaku Habis
                            </span>
                        @else
                            <span class="status-badge-pill closed">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #64748B; display: inline-block;"></span>
                                Sesi Ditutup
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Bagian Kode Besar & Salin -->
                <div style="margin-top: 14px; display: flex; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div class="code-big-box">
                        <span class="code-big-text" id="presensiCodeVal">{{ $session->code }}</span>
                    </div>

                    <button type="button" class="btn-copy-code" onclick="copyCode('{{ $session->code }}')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="copyBtnText">Salin Kode</span>
                    </button>
                </div>

                <!-- Info Masa Berlaku & Aksi Cepat Dosen -->
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                    <div style="font-size: 14px; font-weight: 600;">
                        <span>Waktu Presensi: <strong>{{ $session->start_time->format('H:i') }} &ndash; {{ $session->expires_at->format('H:i') }} WIB</strong> ({{ $session->start_time->format('d M Y') }})</span>
                        <div style="font-size: 13px; opacity: 0.9; margin-top: 2px;">
                            Status waktu: <strong>{{ $session->remainingTimeForHumans() }}</strong>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <!-- Perpanjang +15 Menit -->
                        <form method="POST" action="{{ route('dosen.presensi.extend', $session) }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="minutes" value="15">
                            <button type="submit" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); color: #FFF; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer;">
                                +15 Menit
                            </button>
                        </form>

                        <!-- Perpanjang +30 Menit -->
                        <form method="POST" action="{{ route('dosen.presensi.extend', $session) }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="minutes" value="30">
                            <button type="submit" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); color: #FFF; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer;">
                                +30 Menit
                            </button>
                        </form>

                        <!-- Toggle Tutup/Buka Sesi -->
                        <form method="POST" action="{{ route('dosen.presensi.toggle', $session) }}" style="display: inline;">
                            @csrf
                            @if ($session->is_active)
                                <button type="submit" style="background: #EF4444; border: none; color: #FFF; padding: 8px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);">
                                    Tutup Sesi Sekarang
                                </button>
                            @else
                                <button type="submit" style="background: #10B981; border: none; color: #FFF; padding: 8px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);">
                                    Buka Kembali Sesi
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistik Kehadiran -->
            <div class="stats-grid">
                <div class="stat-card-custom">
                    <div class="val">{{ $totalEnrolled }}</div>
                    <div class="lbl">Total Mahasiswa di Kelas</div>
                </div>
                <div class="stat-card-custom">
                    <div class="val" style="color: #10B981;">{{ $totalAttended }}</div>
                    <div class="lbl">Sudah Melakukan Presensi</div>
                </div>
                <div class="stat-card-custom">
                    <div class="val" style="color: #F59E0B;">{{ count($unattendedList) }}</div>
                    <div class="lbl">Belum Hadir</div>
                </div>
                <div class="stat-card-custom">
                    <div class="val" style="color: #039FFA;">{{ $percentAttended }}%</div>
                    <div class="lbl">Tingkat Kehadiran Kelas</div>
                </div>
            </div>

            <!-- Nav Tabs Hadir vs Belum Hadir -->
            <div style="background: #FFF; border-radius: 18px; border: 1px solid rgba(3, 159, 250, 0.18); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); overflow: hidden;">
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #E2E8F0; padding: 0 20px; background: #F8FAFC;">
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="tab-btn active" id="tabBtnHadir" onclick="switchTab('hadir')">
                            Mahasiswa Hadir ({{ count($attendedList) }})
                        </button>
                        <button type="button" class="tab-btn" id="tabBtnBelum" onclick="switchTab('belum')">
                            Belum Presensi ({{ count($unattendedList) }})
                        </button>
                    </div>

                    <div style="font-size: 13px; color: #64748B; font-weight: 600;">
                        Presensi Kelas: <strong>{{ $session->title }}</strong>
                    </div>
                </div>

                <!-- TAB 1: MAHASISWA HADIR -->
                <div id="panelHadir" style="display: block;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                            <thead>
                                <tr style="background: #FFF; border-bottom: 2px solid #E2E8F0; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                    <th style="padding: 14px 20px;">No</th>
                                    <th style="padding: 14px 16px;">NIM</th>
                                    <th style="padding: 14px 16px;">Nama Mahasiswa</th>
                                    <th style="padding: 14px 16px;">Waktu Hadir</th>
                                    <th style="padding: 14px 16px;">Status</th>
                                    <th style="padding: 14px 16px;">Keterangan</th>
                                    <th style="padding: 14px 20px; text-align: right;">Ubah Status Manual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendedList as $idx => $row)
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td style="padding: 14px 20px; font-weight: 700; color: #94A3B8;">{{ $idx + 1 }}</td>
                                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 700; color: #0F172A;">
                                            {{ $row['student']->nim_nip ?: '-' }}
                                        </td>
                                        <td style="padding: 14px 16px; font-weight: 800; color: #0F172A;">
                                            {{ $row['student']->name }}
                                        </td>
                                        <td style="padding: 14px 16px; color: #475569; font-weight: 600;">
                                            {{ $row['attended_at'] ? $row['attended_at']->format('H:i:s') . ' WIB' : '-' }}
                                        </td>
                                        <td style="padding: 14px 16px;">
                                            <span class="btn-status-badge {{ $row['status'] }}">
                                                {{ strtoupper($row['status']) }}
                                            </span>
                                        </td>
                                        <td style="padding: 14px 16px; font-size: 13px; color: #64748B;">
                                            {{ $row['notes'] ?: 'Melalui kode unik' }}
                                        </td>
                                        <td style="padding: 14px 20px; text-align: right;">
                                            <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}" style="display: inline-flex; align-items: center; gap: 4px; justify-content: flex-end;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                <select name="status" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1; background: #FFF;">
                                                    <option value="hadir" {{ $row['status'] === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="izin" {{ $row['status'] === 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $row['status'] === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="alpa" {{ $row['status'] === 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="padding: 40px; text-align: center; color: #94A3B8;">
                                            <div style="font-size: 15px; font-weight: 700; color: #475569;">Belum ada mahasiswa yang memasukkan kode presensi.</div>
                                            <div style="font-size: 13px; margin-top: 4px;">Bagikan kode unik di atas kepada mahasiswa Anda.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: MAHASISWA BELUM HADIR -->
                <div id="panelBelum" style="display: none;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                            <thead>
                                <tr style="background: #FFF; border-bottom: 2px solid #E2E8F0; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                    <th style="padding: 14px 20px;">No</th>
                                    <th style="padding: 14px 16px;">NIM</th>
                                    <th style="padding: 14px 16px;">Nama Mahasiswa</th>
                                    <th style="padding: 14px 16px;">Status Saat Ini</th>
                                    <th style="padding: 14px 20px; text-align: right;">Presensi Cepat Manual oleh Dosen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($unattendedList as $idx => $row)
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td style="padding: 14px 20px; font-weight: 700; color: #94A3B8;">{{ $idx + 1 }}</td>
                                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 700; color: #0F172A;">
                                            {{ $row['student']->nim_nip ?: '-' }}
                                        </td>
                                        <td style="padding: 14px 16px; font-weight: 800; color: #0F172A;">
                                            {{ $row['student']->name }}
                                        </td>
                                        <td style="padding: 14px 16px;">
                                            <span style="font-size: 12px; font-weight: 700; color: #94A3B8; background: #F1F5F9; padding: 4px 8px; border-radius: 6px;">
                                                Belum Presensi
                                            </span>
                                        </td>
                                        <td style="padding: 14px 20px; text-align: right;">
                                            <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                                <!-- Tandai Hadir -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="hadir">
                                                    <input type="hidden" name="notes" value="Ditandai manual oleh dosen">
                                                    <button type="submit" class="btn-quick-mark btn-qm-hadir">Hadir</button>
                                                </form>

                                                <!-- Tandai Izin -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="izin">
                                                    <input type="hidden" name="notes" value="Izin dikonfirmasi dosen">
                                                    <button type="submit" class="btn-quick-mark btn-qm-izin">Izin</button>
                                                </form>

                                                <!-- Tandai Sakit -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="sakit">
                                                    <input type="hidden" name="notes" value="Sakit dikonfirmasi dosen">
                                                    <button type="submit" class="btn-quick-mark btn-qm-sakit">Sakit</button>
                                                </form>

                                                <!-- Tandai Alpa -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="alpa">
                                                    <input type="hidden" name="notes" value="Tidak hadir tanpa keterangan">
                                                    <button type="submit" class="btn-quick-mark btn-qm-alpa">Alpa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 40px; text-align: center; color: #10B981;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 8px; display: block;">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <div style="font-size: 15px; font-weight: 800;">Luar biasa! Seluruh mahasiswa di kelas ini telah melakukan presensi.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                const btnText = document.getElementById('copyBtnText');
                const orig = btnText.innerText;
                btnText.innerText = 'Tersalin!';
                setTimeout(() => {
                    btnText.innerText = orig;
                }, 2000);
            }).catch(function() {
                alert('Kode presensi: ' + code);
            });
        }

        function switchTab(tab) {
            const btnHadir = document.getElementById('tabBtnHadir');
            const btnBelum = document.getElementById('tabBtnBelum');
            const panelHadir = document.getElementById('panelHadir');
            const panelBelum = document.getElementById('panelBelum');

            if (tab === 'hadir') {
                btnHadir.classList.add('active');
                btnBelum.classList.remove('active');
                panelHadir.style.display = 'block';
                panelBelum.style.display = 'none';
            } else {
                btnBelum.classList.add('active');
                btnHadir.classList.remove('active');
                panelBelum.style.display = 'block';
                panelHadir.style.display = 'none';
            }
        }
    </script>

</body>

</html>
