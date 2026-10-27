{{-- Data ($rekap, $overallAverage, $totalGraded) disiapkan oleh Mahasiswa\RekapNilaiController@index --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Saya — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif

    <style>
        .rekap-course { margin-bottom: 22px; }
        .rekap-course-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; }
        .rekap-course-head h3 { margin: 0; font-size: 15px; font-weight: 900; color: #0F172A; }
        .rekap-course-head small { display: block; font-size: 12px; font-weight: 700; color: #64748B; }
        .rekap-avg { font-size: 12px; font-weight: 800; color: #0369A1; background: #EFF6FF; border: 1px solid rgba(3,159,250,.25); padding: 5px 12px; border-radius: 12px; }
        .tag-late { display: inline-block; margin-left: 4px; padding: 1px 6px; border-radius: 8px; font-size: 10px; font-weight: 800; background: #FEF3C7; color: #B45309; }
        .rekap-kpi { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
        .rekap-kpi-card { background: #FFFFFF; border: 1.5px solid rgba(3, 159, 250, 0.22); border-radius: 16px; padding: 14px 18px; }
        .rekap-kpi-card .lbl { display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: .06em; }
        .rekap-kpi-card .val { display: block; margin-top: 2px; font-size: 24px; font-weight: 900; color: #0F172A; line-height: 1.15; }
        @media (max-width: 700px) { .rekap-kpi { grid-template-columns: 1fr; } }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    @php
        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
        $totalTugas = $rekap->sum('total');
    @endphp

    <div class="app-window">

        <x-layout />

        <main class="dosen-content">

            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL MAHASISWA &bull; REKAP NILAI
                        </span>
                        <h1>Nilai Saya</h1>
                    </div>
                </div>
            </header>

            <div class="rekap-kpi">
                <div class="rekap-kpi-card"><span class="lbl">Mata Kuliah</span><span class="val">{{ $rekap->count() }}</span></div>
                <div class="rekap-kpi-card"><span class="lbl">Tugas Dinilai</span><span class="val">{{ $totalGraded }} / {{ $totalTugas }}</span></div>
                <div class="rekap-kpi-card"><span class="lbl">Rata-rata Keseluruhan</span><span class="val">{{ $overallAverage !== null ? $fmt($overallAverage) . '%' : '-' }}</span></div>
            </div>

            <section class="feature-section">
                <div class="section-card">

                    @forelse ($rekap as $r)
                        <div class="rekap-course">
                            <div class="rekap-course-head">
                                <div>
                                    <h3>{{ $r['course']->code }} &bull; {{ $r['course']->name }}</h3>
                                    <small>{{ $r['graded'] }} dari {{ $r['total'] }} tugas sudah dinilai</small>
                                </div>
                                <span class="rekap-avg">Rata-rata: {{ $r['average'] !== null ? $fmt($r['average']) . '%' : '-' }}</span>
                            </div>

                            @if ($r['total'] === 0)
                                <p style="font-size:13px; font-weight:700; color:#64748B;">Belum ada tugas yang dipublikasikan.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="custom-dosen-table">
                                        <thead>
                                            <tr>
                                                <th>Tugas</th>
                                                <th>Batas Waktu</th>
                                                <th>Status</th>
                                                <th style="text-align:right;">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($r['items'] as $it)
                                                <tr>
                                                    <td><strong>{{ $it['assignment']->title }}</strong></td>
                                                    <td style="font-size:12px; color:#64748B; font-weight:600;">{{ $it['assignment']->due_at->translatedFormat('d M Y, H:i') }} WITA</td>
                                                    <td>
                                                        @if ($it['state'] === 'graded')
                                                            <span class="badge-status badge-status-active">Dinilai</span>
                                                        @elseif ($it['state'] === 'waiting')
                                                            <span class="badge-status badge-status-warning">Menunggu penilaian</span>
                                                        @elseif ($it['state'] === 'missed')
                                                            <span class="badge-status badge-status-danger">Tidak dikumpulkan</span>
                                                        @else
                                                            <span class="badge-status">Belum dikumpulkan</span>
                                                        @endif
                                                        @if ($it['late'])<span class="tag-late">Terlambat</span>@endif
                                                    </td>
                                                    <td style="text-align:right; font-weight:900;">
                                                        @if ($it['score'] !== null)
                                                            <a href="{{ route('submissions.show', $it['submission_id']) }}" style="color:#0369A1; text-decoration:none;">
                                                                {{ $fmt($it['score']) }} / {{ $it['assignment']->max_score }}
                                                            </a>
                                                        @elseif ($it['submission_id'])
                                                            <a href="{{ route('submissions.show', $it['submission_id']) }}" style="color:#64748B; font-size:12px; text-decoration:none;">Lihat pengumpulan</a>
                                                        @else
                                                            <span style="color:#CBD5E1;">&mdash;</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="padding: 28px 16px; text-align: center; font-size: 13px; font-weight: 700; color: #64748B;">
                            Anda belum terdaftar di mata kuliah aktif mana pun.
                        </div>
                    @endforelse

                    <p style="font-size:12px; font-weight:600; color:#64748B; margin-top:4px;">
                        Rata-rata dihitung dari persentase (nilai &divide; nilai maksimal tugas) pada tugas yang sudah dinilai. Klik nilai untuk melihat umpan balik dosen.
                    </p>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
