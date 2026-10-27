{{-- Data ($course, $courses, $assignments, $rows, $stats) disiapkan oleh Dosen\RekapNilaiController@show --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif

    <style>
        .rekap-kpi { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .rekap-kpi-card { background: #FFFFFF; border: 1.5px solid rgba(3, 159, 250, 0.22); border-radius: 16px; padding: 14px 18px; }
        .rekap-kpi-card .lbl { display: block; font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: .06em; }
        .rekap-kpi-card .val { display: block; margin-top: 2px; font-size: 24px; font-weight: 900; color: #0F172A; line-height: 1.15; }
        .rekap-scroll { overflow: auto; max-height: 620px; border: 1px solid rgba(3, 159, 250, 0.18); border-radius: 10px; }
        .rekap-table { border-collapse: separate; border-spacing: 0; min-width: 100%; }
        .rekap-table thead th { position: sticky; top: 0; z-index: 2; background: #F8FAFC; }
        .rekap-table th.col-student, .rekap-table td.col-student { position: sticky; left: 0; z-index: 1; background: #FFFFFF; min-width: 210px; box-shadow: 1px 0 0 #E2E8F0; }
        .rekap-table thead th.col-student { z-index: 3; background: #F8FAFC; }
        .rekap-table th.col-task { min-width: 120px; text-align: center; vertical-align: bottom; }
        .rekap-table th.col-task small { display: block; margin-top: 2px; font-weight: 700; color: #64748B; text-transform: none; letter-spacing: 0; }
        .rekap-table td.cell { text-align: center; font-size: 13px; font-weight: 800; white-space: nowrap; }
        .rekap-table td.col-avg { text-align: center; font-weight: 900; background: #F8FAFC; }
        .cell-graded a { color: #0369A1; text-decoration: none; }
        .cell-graded a:hover { text-decoration: underline; }
        .cell-waiting { color: #D97706; font-size: 11px; }
        .cell-waiting a { color: #D97706; text-decoration: none; }
        .cell-none { color: #CBD5E1; }
        .tag-late { display: inline-block; margin-left: 4px; padding: 1px 6px; border-radius: 8px; font-size: 10px; font-weight: 800; background: #FEF3C7; color: #B45309; }
        .rekap-legend { margin-top: 12px; font-size: 12px; font-weight: 600; color: #64748B; }
        @media (max-width: 800px) { .rekap-kpi { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    @php
        // 85 → "85", 87.5 → "87.5"
        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
    @endphp

    <div class="app-window">

        <x-navbar-dosen />

        <main class="dosen-content">

            <header class="dash-topbar justify-end">
                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah:</span>
                    <select id="selectCurrentCourse" class="course-select"
                            onchange="if (this.value) window.location.href = this.value">
                        @foreach ($courses as $c)
                            <option value="{{ route('dosen.rekap.show', $c) }}" @selected($c->id === $course->id)>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <section class="feature-section">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Rekap Nilai &mdash; {{ $course->code }} &bull; {{ $course->name }}</h2>
                                <p>Nilai seluruh mahasiswa terdaftar pada tiap tugas yang sudah dipublikasikan.</p>
                            </div>
                        </div>
                        <a href="{{ route('dosen.penilaian') }}" class="btn-secondary-action">Ke Penilaian &rarr;</a>
                    </div>

                    <div class="rekap-kpi">
                        <div class="rekap-kpi-card"><span class="lbl">Mahasiswa</span><span class="val">{{ $stats['students'] }}</span></div>
                        <div class="rekap-kpi-card"><span class="lbl">Tugas Published</span><span class="val">{{ $stats['assignments'] }}</span></div>
                        <div class="rekap-kpi-card"><span class="lbl">Dinilai / Menunggu</span><span class="val">{{ $stats['graded'] }} / {{ $stats['waiting'] }}</span></div>
                        <div class="rekap-kpi-card"><span class="lbl">Rata-rata Kelas</span><span class="val">{{ $stats['average'] !== null ? $fmt($stats['average']) . '%' : '-' }}</span></div>
                    </div>

                    @if ($rows->isEmpty() || $assignments->isEmpty())
                        <div style="padding: 28px 16px; text-align: center; font-size: 13px; font-weight: 700; color: #64748B;">
                            @if ($rows->isEmpty())
                                Belum ada mahasiswa terdaftar di mata kuliah ini.
                            @else
                                Belum ada tugas yang dipublikasikan di mata kuliah ini.
                            @endif
                        </div>
                    @else
                        <div class="rekap-scroll">
                            <table class="custom-dosen-table rekap-table">
                                <thead>
                                    <tr>
                                        <th class="col-student">Mahasiswa</th>
                                        @foreach ($assignments as $a)
                                            <th class="col-task" title="{{ $a->title }}">
                                                {{ \Illuminate\Support\Str::limit($a->title, 22) }}
                                                <small>maks {{ $a->max_score }}</small>
                                            </th>
                                        @endforeach
                                        <th class="col-avg" style="text-align:center;">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td class="col-student">
                                                <div class="student-meta">
                                                    <span class="student-name">{{ $row['student']->name }}</span>
                                                    <span class="student-nim">{{ $row['student']->nim_nip ?? '-' }}</span>
                                                </div>
                                            </td>
                                            @foreach ($row['cells'] as $cell)
                                                <td class="cell">
                                                    @if ($cell['state'] === 'graded')
                                                        <span class="cell-graded">
                                                            <a href="{{ route('submissions.show', $cell['submission_id']) }}">{{ $fmt($cell['score']) }}</a>
                                                            @if ($cell['late'])<span class="tag-late">T</span>@endif
                                                        </span>
                                                    @elseif ($cell['state'] === 'waiting')
                                                        <span class="cell-waiting">
                                                            <a href="{{ route('submissions.show', $cell['submission_id']) }}">Menunggu</a>
                                                            @if ($cell['late'])<span class="tag-late">T</span>@endif
                                                        </span>
                                                    @else
                                                        <span class="cell-none" title="Belum mengumpulkan">&mdash;</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="col-avg">
                                                {{ $row['average'] !== null ? $fmt($row['average']) . '%' : '-' }}
                                                <div style="font-size:10.5px; font-weight:700; color:#64748B;">{{ $row['graded'] }}/{{ $assignments->count() }} dinilai</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="rekap-legend">
                            Angka = nilai &bull; <b>Menunggu</b> = sudah dikumpulkan, belum dinilai &bull; &mdash; = belum mengumpulkan &bull;
                            <span class="tag-late">T</span> = terlambat &bull; Rata-rata dihitung dari persentase (nilai &divide; nilai maksimal tugas) pada tugas yang sudah dinilai.
                        </p>
                    @endif
                </div>
            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
