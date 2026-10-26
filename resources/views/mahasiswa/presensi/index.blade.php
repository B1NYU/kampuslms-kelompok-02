<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Perkuliahan — Portal Mahasiswa KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/mahasiswa.dashboard.css', 'resources/js/app.js'])
    @endif

    <style>
        .presensi-hero-card {
            background: linear-gradient(135deg, #0284C7 0%, #039FFA 50%, #38BDF8 100%);
            border-radius: 24px;
            padding: 36px;
            color: #FFFFFF;
            box-shadow: 0 12px 36px rgba(3, 159, 250, 0.25);
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .presensi-hero-card::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            pointer-events: none;
        }
        .presensi-form-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border: 2px dashed rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 24px 28px;
            margin-top: 20px;
            max-width: 620px;
        }
        .input-code-mhs {
            width: 100%;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 5px;
            text-align: center;
            text-transform: uppercase;
            padding: 14px 20px;
            border-radius: 14px;
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: #FFFFFF;
            color: #0F172A;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            outline: none;
            transition: all 0.2s;
        }
        .input-code-mhs:focus {
            border-color: #F9B804;
            box-shadow: 0 0 0 4px rgba(249, 184, 4, 0.35);
        }
        .btn-submit-presensi {
            background: #F9B804;
            color: #78350F;
            border: none;
            font-size: 16px;
            font-weight: 900;
            padding: 14px 28px;
            border-radius: 14px;
            cursor: pointer;
            width: 100%;
            margin-top: 12px;
            box-shadow: 0 6px 18px rgba(249, 184, 4, 0.4);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit-presensi:hover {
            background: #FBBF24;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(249, 184, 4, 0.5);
        }
        .btn-submit-presensi:active {
            transform: translateY(0);
        }
        .active-session-card {
            background: #FFFFFF;
            border: 1px solid rgba(3, 159, 250, 0.2);
            border-radius: 18px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            margin-bottom: 12px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .active-session-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(3, 159, 250, 0.1);
        }
        .badge-status-mhs {
            font-size: 12px;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-status-mhs.hadir { background: #DCFCE7; color: #166534; }
        .badge-status-mhs.izin { background: #E0F2FE; color: #0369A1; }
        .badge-status-mhs.sakit { background: #FEF3C7; color: #92400E; }
        .badge-status-mhs.alpa { background: #FEE2E2; color: #991B1B; }

        .alert-box-mhs {
            padding: 16px 20px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success-mhs {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #86EFAC;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }
        .alert-danger-mhs {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- 1. Navbar Mahasiswa -->
        <x-layout />

        <!-- 2. Konten Utama Presensi Mahasiswa -->
        <main class="dashboard-content" style="padding: 24px 32px; max-width: 1280px; margin: 0 auto; width: 100%;">

            <!-- Topbar -->
            <header class="dash-topbar" style="margin-bottom: 20px;">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1 style="font-size: 26px; font-weight: 800; color: #0F172A;">Presensi Kehadiran Kelas</h1>
                        <p style="font-size: 14px; color: #64748B; margin-top: 2px;">Masukkan kode presensi unik yang diberikan oleh dosen pengampu di kelas Anda.</p>
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="alert-box-mhs alert-success-mhs">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box-mhs alert-danger-mhs">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;">
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

            <!-- HERO CARD: FORM INPUT KODE UNIK PRESENSI -->
            <div class="presensi-hero-card">
                <div style="max-width: 600px;">
                    <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.95;">
                        Portal Kehadiran Realtime
                    </div>
                    <h2 style="font-size: 28px; font-weight: 900; margin: 6px 0 8px 0; color: #FFF;">
                        Punya Kode Presensi Hari Ini?
                    </h2>
                    <p style="font-size: 14px; opacity: 0.92; line-height: 1.5; margin: 0;">
                        Setiap kode presensi memiliki batas masa berlaku tertentu. Pastikan Anda memasukkan kode sebelum sesi ditutup oleh dosen.
                    </p>
                </div>

                <div class="presensi-form-box">
                    <form method="POST" action="{{ route('mahasiswa.presensi.store') }}">
                        @csrf
                        <div style="text-align: center; margin-bottom: 8px;">
                            <label for="code" style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #FFFFFF;">
                                Masukkan Kode Unik:
                            </label>
                        </div>

                        <input type="text" name="code" id="code" class="input-code-mhs" placeholder="CONTOH: 8K9P2X" maxlength="16" autocomplete="off" autofocus required value="{{ old('code') }}">

                        <button type="submit" class="btn-submit-presensi">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            Kirim & Konfirmasi Kehadiran
                        </button>
                    </form>
                </div>
            </div>

            <!-- SESI PRESENSI AKTIF DI KELAS YANG DIIKUTI -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #0F172A; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                    Sesi Presensi yang Sedang Berlangsung di Kelas Anda
                </h3>

                @if ($activeSessions->isNotEmpty())
                    @foreach ($activeSessions as $active)
                        @php
                            $alreadyAttended = in_array($active->id, $attendedSessionIds);
                        @endphp
                        <div class="active-session-card">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 800; font-size: 13px; background: #EFF6FF; color: #0284C7; padding: 3px 8px; border-radius: 6px;">
                                        {{ $active->course->code }}
                                    </span>
                                    <span style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        {{ $active->course->name }}
                                    </span>
                                </div>
                                <div style="font-size: 15px; font-weight: 700; color: #334155; margin-top: 4px;">
                                    {{ $active->title }}
                                </div>
                                <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                                    Dosen: {{ $active->course->lecturer?->name ?? 'Dosen Pengampu' }} &bull;
                                    Masa berlaku s/d <strong>{{ $active->expires_at->format('H:i') }} WIB</strong> ({{ $active->remainingTimeForHumans() }})
                                </div>
                            </div>

                            <div>
                                @if ($alreadyAttended)
                                    <span class="badge-status-mhs hadir">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Anda Sudah Hadir
                                    </span>
                                @else
                                    <span class="badge-status-mhs" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        Belum Hadir &bull; Minta Kode ke Dosen
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 16px; padding: 24px; text-align: center; color: #64748B;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.8" style="margin: 0 auto 8px; display: block;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div style="font-weight: 700; font-size: 14px; color: #334155;">Tidak ada sesi presensi yang sedang aktif saat ini.</div>
                        <div style="font-size: 12px; margin-top: 2px;">Sesi presensi akan muncul saat dosen pengampu membuka presensi di perkuliahan.</div>
                    </div>
                @endif
            </div>

            <!-- RIWAYAT PRESENSI MAHASISWA -->
            <div style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(3, 159, 250, 0.18); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); overflow: hidden;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#039FFA" stroke-width="2.3">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Riwayat Kehadiran Anda
                    </h2>
                    <span style="font-size: 13px; font-weight: 700; color: #64748B;">
                        Total: {{ $attendanceHistory->count() }} Kali Presensi
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                <th style="padding: 14px 20px;">Mata Kuliah</th>
                                <th style="padding: 14px 16px;">Pertemuan / Sesi</th>
                                <th style="padding: 14px 16px;">Waktu Kehadiran</th>
                                <th style="padding: 14px 16px;">Status</th>
                                <th style="padding: 14px 20px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendanceHistory as $att)
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 16px 20px;">
                                        <div style="font-weight: 800; color: #0F172A;">
                                            {{ $att->session?->course?->code }} &bull; {{ $att->session?->course?->name }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748B; margin-top: 2px;">
                                            Dosen: {{ $att->session?->course?->lecturer?->name ?? 'Dosen Pengampu' }}
                                        </div>
                                    </td>
                                    <td style="padding: 16px 16px; font-weight: 700; color: #334155;">
                                        {{ $att->session?->title ?? '-' }}
                                    </td>
                                    <td style="padding: 16px 16px; color: #475569; font-weight: 600;">
                                        {{ $att->attended_at ? $att->attended_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td style="padding: 16px 16px;">
                                        <span class="badge-status-mhs {{ $att->status }}">
                                            {{ strtoupper($att->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px 20px; font-size: 13px; color: #64748B;">
                                        {{ $att->notes ?: 'Tercatat melalui sistem' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center; color: #94A3B8;">
                                        <div style="font-size: 15px; font-weight: 700; color: #475569;">Belum ada riwayat kehadiran yang tercatat.</div>
                                        <div style="font-size: 13px; margin-top: 4px;">Masukkan kode presensi dari dosen pada form di atas saat perkuliahan berlangsung.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

</body>

</html>
