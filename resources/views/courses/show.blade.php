@php
    $courseDb = $course;
    $activeTab = request('tab');
    if (!$activeTab) {
        $activeTab = ($errors->has('code') || session('tab') === 'presensi') ? 'presensi' : 'detail';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mataKuliah['nama'] }} — Portal Mahasiswa KampusLMS</title>
    <!-- Font Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    <!-- CSS & JS via Vite (Hanya Tailwind murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/mahasiswa/mahasiswa.matkul.js'])
    @endif

    <style>
        /* State tombol tab aktif */
        .subnav-tab-btn {
            background-color: #F8FAFC;
            border-color: #E2E8F0;
            color: #64748B;
        }
        .subnav-tab-btn:hover {
            background-color: #F1F5F9;
            color: #0F172A;
        }
        .subnav-tab-btn.active {
            background-color: #0284C7 !important;
            border-color: #0284C7 !important;
            color: #FFFFFF !important;
        }
        .subnav-tab-btn.active svg {
            stroke: #FFFFFF !important;
        }

        /* Kartu sesi pertemuan terpilih via JS */
        .session-card.selected-active {
            border-color: #0284C7 !important;
            background-color: #F0F9FF !important;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.25) !important;
        }

        /* Elemen dinamis yang dibentuk JS saat kartu pertemuan diklik */
        .resource-card {
            background: #F8FAFC;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .resource-card:hover {
            background: #F0F9FF;
            border-color: #BAE6FD;
        }
        .resource-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .resource-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            flex-shrink: 0;
        }
        .resource-name {
            font-size: 13px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .resource-meta {
            font-size: 11px;
            font-weight: 600;
            color: #64748B;
        }
        .btn-mini-download {
            font-size: 11px;
            font-weight: 800;
            color: #0284C7;
            background: #FFFFFF;
            border: 1px solid #BAE6FD;
            padding: 5px 12px;
            border-radius: 8px;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .btn-mini-download:hover {
            background: #0284C7;
            color: #FFFFFF;
        }

        .task-card-box {
            background: #FFFDF8;
            border-radius: 14px;
            padding: 14px;
            border: 1px solid rgba(249, 115, 22, 0.25);
            border-left: 4px solid #F97316;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .task-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .task-badge-tag {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            background: #FFEDD5;
            color: #C2410C;
            text-transform: uppercase;
        }
        .task-card-title {
            font-size: 13px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.3;
        }
        .task-deadline {
            font-size: 11px;
            font-weight: 600;
            color: #64748B;
        }
        .btn-task-action {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 800;
            color: #FFFFFF;
            background: linear-gradient(135deg, #0284C7, #039FFA);
            padding: 8px 14px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.15s ease;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
        }
        .btn-task-action:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">
        <!-- 1. NAVBAR UTAMA PORTAL MAHASISWA -->
        <x-layout />

        <!-- 2. NAVBAR KEDUA (SUB-NAVBAR DETAIL & NILAI MATA KULIAH) -->
        <nav class="bg-white/95 backdrop-blur-xs border-b border-slate-200/80 sticky top-0 z-20 shadow-xs">
            <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 text-xs font-black tracking-wide rounded-lg bg-sky-100 text-sky-700 border border-sky-200">
                        {{ $mataKuliah['kode'] }}
                    </span>
                    <span class="text-base sm:text-lg font-black text-slate-900 truncate">
                        {{ $mataKuliah['nama'] }}
                    </span>
                </div>

                <!-- TABS: DETAIL, PRESENSI & NILAI -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <button class="subnav-tab-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer {{ $activeTab === 'detail' ? 'active' : '' }}" id="tabBtnDetail" onclick="switchCourseTab('detail')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span>Detail</span>
                    </button>
                    <button class="subnav-tab-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer {{ $activeTab === 'presensi' ? 'active' : '' }}" id="tabBtnPresensi" onclick="switchCourseTab('presensi')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <span>Presensi</span>
                        @if (isset($activeSessions) && $activeSessions->isNotEmpty())
                            <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-emerald-500 text-white shadow-xs">{{ $activeSessions->count() }} Aktif</span>
                        @endif
                    </button>
                    <button class="subnav-tab-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer {{ $activeTab === 'nilai' ? 'active' : '' }}" id="tabBtnNilai" onclick="switchCourseTab('nilai')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <polyline points="9 15 11 17 15 13"></polyline>
                        </svg>
                        <span>Nilai</span>
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-sky-100 text-sky-700 border border-sky-200">{{ $nilaiData['stats']['rata_rata'] }}</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- 3. KONTEN MATA KULIAH -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- VIEW 1: DETAIL (Alur 16 Sesi & Info Matkul) -->
            <div id="panelCourseDetail" style="{{ $activeTab !== 'detail' ? 'display: none;' : '' }}">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- KOLOM KIRI: TIMELINE PERTEMUAN -->
                    <section class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col gap-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Rencana Pertemuan</h2>
                        </div>
                        @php
                            $courseAssignments = $course->assignments->sortBy('due_at')->values();
                            $pertemuanList = [];

                            // Format ukuran berkas tanpa ekstensi intl.
                            $fmtUkuran = function ($byte) {
                                $byte = (int) $byte;
                                if ($byte >= 1048576) return number_format($byte / 1048576, 1, ',', '.') . ' MB';
                                if ($byte >= 1024)    return number_format($byte / 1024, 0, ',', '.') . ' KB';
                                return $byte . ' B';
                            };

                            // Bentuk data materi untuk ditampilkan.
                            $fmtMateri = fn ($m) => [
                                'id'        => $m->id,
                                'judul'     => $m->title,
                                'nama'      => $m->isLink() ? 'Tautan eksternal' : ($m->original_name ?: 'Berkas'),
                                'meta'      => $m->isLink()
                                    ? 'Tautan'
                                    : (strtoupper(pathinfo((string) $m->original_name, PATHINFO_EXTENSION) ?: 'file')
                                        . ($m->file_size ? ' · ' . $fmtUkuran($m->file_size) : '')),
                                'deskripsi' => $m->description,
                                'kind'      => $m->type,
                                'url'       => route('mahasiswa.materi.download', $m),
                            ];
                            // Materi umum (session = NULL).
                            $materiUmum = $course->materials->whereNull('session')->map($fmtMateri)->values();

                            for ($minggu = 1; $minggu <= 16; $minggu++) {
                                $isExam = ($minggu == 8 || $minggu == 16);
                                $judul = $minggu == 8 ? 'Minggu 8 (UTS)' : ($minggu == 16 ? 'Minggu 16 (UAS)' : 'Minggu ' . $minggu);

                                $matFiles = $course->materials->where('session', $minggu)->map($fmtMateri)->values()->all();

                                $assignIdx = $minggu - 1;
                                $dbAssign = $courseAssignments->get($assignIdx);
                                $tasks = [];
                                $studentSub = null;

                                if ($dbAssign) {
                                    $studentSub = $student ? $dbAssign->submissions->firstWhere('user_id', $student->id) : null;
                                    $btnAction = 'Kumpulkan Tugas &rarr;';
                                    if ($studentSub && $studentSub->grade) {
                                        $btnAction = 'Lihat Nilai (' . round((float) $studentSub->grade->score, 1) . ') &rarr;';
                                    } elseif ($studentSub) {
                                        $btnAction = 'Sudah Dikumpulkan &rarr;';
                                    } elseif ($dbAssign->status === 'draft') {
                                        $btnAction = 'Belum Dibuka';
                                    } elseif ($dbAssign->due_at && $dbAssign->due_at->isPast()) {
                                        $btnAction = 'Lewat Batas Waktu';
                                    }

                                    $tasks[] = [
                                        'tipe' => 'Tugas Kuliah',
                                        'judul' => $dbAssign->title,
                                        'deadline' => 'Batas: ' . ($dbAssign->due_at ? $dbAssign->due_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Jadwal fleksibel'),
                                        'btn' => $btnAction,
                                        'url' => route('assignments.show', $dbAssign),
                                    ];
                                }

                                if ($isExam) {
                                    $status = 'exam';
                                    $label = $minggu == 8 ? 'Ujian UTS' : 'Ujian UAS';
                                } elseif ($dbAssign) {
                                    if ($studentSub || ($dbAssign->due_at && $dbAssign->due_at->isPast())) {
                                        $status = 'done';
                                        $label = 'Selesai';
                                    } elseif ($dbAssign->status === 'draft') {
                                        $status = 'locked';
                                        $label = 'Mendatang';
                                    } else {
                                        $status = 'active';
                                        $label = 'Berjalan';
                                    }
                                } elseif (!empty($matFiles)) {
                                    $status = 'done';
                                    $label = 'Selesai';
                                } else {
                                    $status = 'locked';
                                    $label = 'Mendatang';
                                }

                                $pertemuanList[$minggu] = [
                                    'judul' => $judul,
                                    'status' => $status,
                                    'label' => $label,
                                    'materi' => $matFiles,
                                    'tasks' => $tasks,
                                ];
                            }

                            $hasActive = collect($pertemuanList)->contains('status', 'active');
                            if (!$hasActive && ($mataKuliah['status'] ?? 'active') === 'active') {
                                foreach ($pertemuanList as $m => &$pItem) {
                                    if ($pItem['status'] === 'locked' && $m != 8 && $m != 16) {
                                        $pItem['status'] = 'active';
                                        $pItem['label'] = 'Berjalan';
                                        break;
                                    }
                                }
                                unset($pItem);
                            }
                        @endphp

                        <!-- KOTAK SCROLL TIMELINE -->
                        <div class="flex flex-col gap-3 max-h-[720px] overflow-y-auto pr-1">
                            @foreach ($pertemuanList as $minggu => $item)
                                @php
                                    $isExam = ($minggu == 8 || $minggu == 16);
                                @endphp
                                <div class="flex items-start gap-3">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs shrink-0 shadow-xs
                                            {{ $item['status'] === 'active' ? 'bg-sky-500 text-white ring-4 ring-sky-100' : '' }}
                                            {{ $item['status'] === 'done' ? 'bg-emerald-500 text-white' : '' }}
                                            {{ $item['status'] === 'exam' ? 'bg-amber-500 text-white ring-4 ring-amber-100' : '' }}
                                            {{ $item['status'] === 'locked' ? 'bg-slate-200 text-slate-500' : '' }}
                                        ">
                                            {{ $minggu }}
                                        </div>
                                        @if ($minggu < 16)
                                            <div class="w-0.5 h-8 bg-slate-200 my-1"></div>
                                        @endif
                                    </div>

                                    <div class="session-card flex-1 p-3.5 rounded-xl border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-sky-300 transition-all duration-200 cursor-pointer flex items-center justify-between gap-3 shadow-xs"
                                         id="cardWeek{{ $minggu }}"
                                         data-judul="{{ $item['judul'] }}"
                                         data-materi="{{ json_encode($item['materi']) }}"
                                         data-tasks="{{ json_encode($item['tasks']) }}"
                                         onclick="switchRightToDetail({{ $minggu }}, this)">
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800">{{ $item['judul'] }}</h4>
                                            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                                @if (!empty($item['materi']))
                                                    <span>📄 {{ count($item['materi']) }} Materi</span>
                                                @endif
                                                @if (!empty($item['tasks']))
                                                    <span class="ml-1">📝 {{ count($item['tasks']) }} Tugas</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full
                                                {{ $item['status'] === 'active' ? 'bg-sky-100 text-sky-700 border border-sky-200' : '' }}
                                                {{ $item['status'] === 'done' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                                                {{ $item['status'] === 'exam' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                                {{ $item['status'] === 'locked' ? 'bg-slate-100 text-slate-600 border border-slate-200' : '' }}
                                            ">
                                                {{ $item['label'] }}
                                            </span>
                                            <span class="text-slate-400 font-bold">&rsaquo;</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- KOLOM KANAN: INFO MATA KULIAH & DETAIL SESI -->
                    <aside class="lg:col-span-5 flex flex-col gap-6">
                        <!-- TAMPILAN DEFAULT (INFO MATA KULIAH) -->
                        <div id="defaultRightPanel" class="flex flex-col gap-6">
                            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col gap-4">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 pb-3 border-b border-slate-100">Informasi Mata Kuliah</h3>
                                <div class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-sky-500 to-sky-600 text-white font-black flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($mataKuliah['dosen'], 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-extrabold text-slate-900 truncate">{{ $mataKuliah['dosen'] }}</h5>
                                        <span class="text-xs font-semibold text-slate-500">Dosen Pengampu Utama</span>
                                    </div>
                                </div>
                                <div class="flex flex-col text-xs sm:text-sm">
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Kode Mata Kuliah</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-sky-100 text-sky-700 border border-sky-200 text-xs">{{ $mataKuliah['kode'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Beban Kredit</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-amber-100 text-amber-800 border border-amber-200 text-xs">{{ $mataKuliah['sks'] }} SKS</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Peserta Terdaftar</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs">{{ $courseDb ? $courseDb->students->count() : 0 }} Mahasiswa</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Tugas Terjadwal</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-orange-100 text-orange-800 border border-orange-200 text-xs">{{ $courseDb ? $courseDb->assignments->count() : 0 }} Tugas</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Materi Tersedia</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-sky-100 text-sky-700 border border-sky-200 text-xs">{{ $courseDb->materials->count() }} Materi</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500 font-bold">Status Kelas</span>
                                        <span class="px-2 py-0.5 rounded-md font-black bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs">{{ ucfirst($mataKuliah['status'] ?? 'Aktif') }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 py-2">
                                        <span class="text-slate-500 font-bold">Deskripsi</span>
                                        <p class="text-xs text-slate-700 leading-relaxed font-medium">{{ $mataKuliah['deskripsi'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MATERI UMUM (tidak terikat pertemuan) -->
                        @if ($materiUmum->isNotEmpty())
                            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col gap-3">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 pb-2 border-b border-slate-100">Materi Umum</h3>
                                <div class="flex flex-col gap-2.5">
                                    @foreach ($materiUmum as $m)
                                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-sky-50/60 hover:border-sky-200 transition-all">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="text-lg">{{ $m['kind'] === 'link' ? '🔗' : '📄' }}</span>
                                                <div class="min-w-0">
                                                    <div class="text-xs sm:text-sm font-black text-slate-900 truncate">{{ $m['judul'] }}</div>
                                                    <div class="text-[11px] font-semibold text-slate-500">{{ $m['meta'] }}</div>
                                                </div>
                                            </div>
                                            <a class="px-3 py-1 text-xs font-black rounded-lg bg-sky-600 text-white hover:bg-sky-700 transition-colors shrink-0" href="{{ $m['url'] }}" @if($m['kind'] === 'link') target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $m['kind'] === 'link' ? 'Buka' : 'Unduh' }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- TAMPILAN PENGGANTI (DETAIL SESI MINGGUAN) -->
                        <div id="detailRightPanel" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col gap-5" style="display: none;">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <button class="text-xs font-black text-sky-600 hover:text-sky-700 flex items-center gap-1 cursor-pointer" onclick="restoreDefaultInfo()">
                                    &larr; Kembali ke Info Matkul
                                </button>
                                <span id="detailBadgeSession" class="text-xs font-black px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-700 border border-sky-200">Minggu 1</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rincian Pertemuan</span>
                                <h3 id="detailHeading" class="text-lg font-black text-slate-900">Minggu 1</h3>
                            </div>

                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-black uppercase tracking-wider text-sky-600">Materi &amp; Berkas Perkuliahan</span>
                                <div class="flex flex-col gap-2.5" id="materiListContainer"></div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-black uppercase tracking-wider text-orange-600" id="tasksHeaderTitle">Aktivitas &amp; Tugas</span>
                                <div class="flex flex-col gap-2.5" id="tasksListContainer"></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- VIEW 2: NILAI (Rekapitulasi Nilai Tugas Mahasiswa) -->
            <div id="panelCourseNilai" style="{{ $activeTab === 'nilai' ? '' : 'display: none;' }}" class="flex flex-col gap-6">
                <!-- Stat Cards Ringkasan Nilai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-black tracking-wider uppercase text-slate-400">Rata-Rata Nilai</span>
                            <div class="text-2xl font-black text-slate-900">{{ $nilaiData['stats']['rata_rata'] }} <span class="text-xs font-bold text-slate-400">/ 100</span></div>
                            <span class="text-[11px] text-slate-500 font-semibold">Semester berjalan</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-black tracking-wider uppercase text-slate-400">Predikat Akademik</span>
                            <div class="text-2xl font-black text-emerald-600">{{ $nilaiData['stats']['indeks'] }} <span class="text-xs font-bold">({{ $nilaiData['stats']['predikat'] }})</span></div>
                            <span class="text-[11px] text-slate-500 font-semibold">Skala huruf standar</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-black tracking-wider uppercase text-slate-400">Tugas Dinilai</span>
                            <div class="text-2xl font-black text-slate-900">{{ $nilaiData['stats']['tugas_dinilai'] }}</div>
                            <span class="text-[11px] text-slate-500 font-semibold">Tugas terkoreksi</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M18 20V10"></path>
                                <path d="M12 20V4"></path>
                                <path d="M6 20v-6"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-black tracking-wider uppercase text-slate-400">Bobot Tercapai</span>
                            <div class="text-2xl font-black text-sky-600">{{ $nilaiData['stats']['bobot_tercapai'] }} <span class="text-xs font-bold text-slate-400">/ 100%</span></div>
                            <span class="text-[11px] text-slate-500 font-semibold">Akumulasi bobot</span>
                        </div>
                    </div>
                </div>

                <!-- Card Tabel Rincian Nilai -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Daftar Nilai &amp; Evaluasi Tugas</h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Hasil penilaian dan feedback resmi dari dosen pengampu: <strong>{{ $mataKuliah['dosen'] }}</strong></p>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <input type="text" id="gradeSearchInput" placeholder="Cari nama tugas..." class="w-full text-xs font-bold pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-sky-500 transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm" id="gradeTable">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-600 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                                    <th class="p-4">Aktivitas / Tugas</th>
                                    <th class="p-4">Waktu Pengumpulan</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Bobot</th>
                                    <th class="p-4">Nilai Akhir</th>
                                    <th class="p-4">Catatan &amp; Feedback</th>
                                </tr>
                            </thead>
                            <tbody id="gradeTableBody">
                                @forelse ($nilaiData['items'] as $item)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">
                                        <td class="p-4">
                                            <a href="{{ $item['url'] }}" class="font-extrabold text-slate-900 hover:text-sky-600 transition-colors">{{ $item['judul'] }}</a>
                                        </td>
                                        <td class="p-4 font-bold text-slate-600 whitespace-nowrap">
                                            {{ $item['tanggal_kumpul'] }}
                                        </td>
                                        <td class="p-4">
                                            @if ($item['status'] === 'Dinilai')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Dinilai
                                                </span>
                                            @elseif ($item['status'] === 'Menunggu Penilaian')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                    Sedang Dievaluasi
                                                </span>
                                            @elseif ($item['status'] === 'Lewat Deadline')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                    Lewat Deadline
                                                </span>
                                            @elseif ($item['status'] === 'Belum Dikumpulkan')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                    Belum Dikumpulkan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-600">
                                                    Belum Dibuka
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-black text-slate-700">
                                            {{ $item['bobot'] }}
                                        </td>
                                        <td class="p-4">
                                            @if ($item['nilai'] !== null)
                                                <span class="text-base font-black text-slate-900">{{ $item['nilai'] }}</span>
                                                <span class="text-xs text-slate-400 font-bold">/ 100</span>
                                            @else
                                                <span class="text-slate-400 font-bold">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-start gap-2 text-xs font-medium text-slate-600 bg-sky-50/60 p-2.5 rounded-xl border border-sky-100">
                                                <svg class="w-3.5 h-3.5 text-sky-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                                <span>{{ $item['feedback'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">
                                            <div class="font-extrabold text-sm text-slate-700 mb-1">Belum Ada Tugas di Database</div>
                                            <div class="text-xs">Mata kuliah ini belum memiliki penugasan atau rekaman nilai aktif.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginator Client-side untuk Tabel Nilai --}}
                    <div style="padding: 12px 20px; border-top: 1px solid #F1F5F9; background: #FAFAFA; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; border-radius: 0 0 16px 16px;">
                        <span id="gradeTablePaginatorInfo" style="font-size: 12px; font-weight: 600; color: #64748B;"></span>
                        <div id="gradeTablePaginator" style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;"></div>
                    </div>
                </div>
            </div>

            <!-- VIEW 3: PRESENSI KELAS KHUSUS MATA KULIAH INI -->
            <div id="panelCoursePresensi" style="{{ $activeTab === 'presensi' ? '' : 'display: none;' }}" class="flex flex-col gap-6">
                @if (session('success'))
                    <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl p-4 font-bold flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-rose-50 text-rose-800 border border-rose-200 rounded-xl p-4 font-bold flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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

                <!-- Hero Input Card Khusus Mata Kuliah Ini -->
                <div class="bg-gradient-to-r from-sky-600 via-sky-500 to-sky-400 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-sky-500/20 relative overflow-hidden">
                    <div class="max-w-xl">
                        <span class="text-xs font-black uppercase tracking-wider opacity-90">
                            Presensi Kehadiran Kelas
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black mt-1 mb-2 text-white">
                            {{ $mataKuliah['kode'] }} &bull; {{ $mataKuliah['nama'] }}
                        </h2>
                        <p class="text-xs sm:text-sm opacity-90 leading-relaxed">
                            Presensi kehadiran pada halaman ini hanya berlaku untuk mata kuliah ini. Masukkan kode unik yang diberikan oleh dosen pengampu di kelas.
                        </p>
                    </div>

                    <div class="bg-white/20 backdrop-blur-md border-2 border-dashed border-white/60 rounded-2xl p-5 sm:p-6 mt-6 max-w-lg">
                        <form method="POST" action="{{ route('mahasiswa.mata-kuliah.presensi.store', $course) }}">
                            @csrf
                            <label for="inputPresensiCode" class="block text-xs font-black uppercase tracking-wider text-white mb-2 text-center">
                                Masukkan Kode Presensi:
                            </label>

                            <input type="text" name="code" id="inputPresensiCode" placeholder="CONTOH: 8K9P2X" maxlength="16" autocomplete="off" required value="{{ old('code') }}" class="w-full font-mono text-2xl sm:text-3xl font-black text-center tracking-widest uppercase py-3 px-4 rounded-xl border-2 border-white/80 bg-white text-slate-900 shadow-inner outline-none focus:ring-4 focus:ring-white/40">

                            <button type="submit" class="w-full mt-3 py-3 px-6 rounded-xl font-black text-amber-950 bg-amber-400 hover:bg-amber-300 shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                Kirim Presensi Kelas
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sesi Presensi Aktif di Mata Kuliah Ini -->
                <div class="flex flex-col gap-3">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Sesi Presensi yang Sedang Berlangsung di Mata Kuliah Ini
                    </h3>

                    @if (isset($activeSessions) && $activeSessions->isNotEmpty())
                        @foreach ($activeSessions as $active)
                            @php
                                $alreadyAttended = in_array($active->id, $attendedSessionIds ?? []);
                            @endphp
                            <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
                                <div>
                                    <div class="text-sm sm:text-base font-black text-slate-900">
                                        {{ $active->title }}
                                    </div>
                                    <div class="text-xs text-slate-500 font-semibold mt-1">
                                        Masa berlaku sampai <strong>{{ $active->expires_at->format('H:i') }} WIB</strong> ({{ $active->remainingTimeForHumans() }})
                                    </div>
                                </div>

                                <div>
                                    @if ($alreadyAttended)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                                            <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            Anda Sudah Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                                            <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                        <div class="bg-white border border-dashed border-slate-200 rounded-xl p-5 text-center text-slate-400 text-xs sm:text-sm font-semibold">
                            Tidak ada sesi presensi yang sedang berlangsung untuk mata kuliah ini saat ini.
                        </div>
                    @endif
                </div>

                <!-- Riwayat Kehadiran Mahasiswa di Mata Kuliah Ini -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Riwayat Kehadiran di Mata Kuliah Ini
                        </h3>
                        <span class="text-xs font-bold text-slate-500">
                            Total: {{ isset($attendanceHistory) ? $attendanceHistory->count() : 0 }} Pertemuan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-600 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                                    <th class="p-4">Pertemuan / Sesi</th>
                                    <th class="p-4">Waktu Presensi</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="coursePresensiTbody">
                                @forelse ($attendanceHistory ?? [] as $att)
                                    @php
                                        $badgeClass = match($att->status) {
                                            'hadir' => 'bg-emerald-100 text-emerald-800',
                                            'izin' => 'bg-sky-100 text-sky-800',
                                            'sakit' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-rose-100 text-rose-800',
                                        };
                                    @endphp
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">
                                        <td class="p-4 font-black text-slate-900">
                                            {{ $att->session?->title ?? '-' }}
                                        </td>
                                        <td class="p-4 text-slate-600 font-bold">
                                            {{ $att->attended_at ? $att->attended_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase {{ $badgeClass }}">
                                                {{ strtoupper($att->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-500 font-medium">
                                            {{ $att->notes ?: 'Tercatat di sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400">
                                            <div class="font-extrabold text-sm text-slate-700 mb-1">Belum ada riwayat kehadiran pada mata kuliah ini.</div>
                                            <div class="text-xs">Masukkan kode presensi dari dosen pada formulir di atas.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginator Client-side untuk Tabel Presensi --}}
                    <div style="padding: 12px 20px; border-top: 1px solid #F1F5F9; background: #FAFAFA; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; border-radius: 0 0 16px 16px;">
                        <span id="coursePresensiPaginatorInfo" style="font-size: 12px; font-weight: 600; color: #64748B;"></span>
                        <div id="coursePresensiPaginator" style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;"></div>
                    </div>
                </div>
            </div>

        </main>

        <!-- FOOTER GLOBAL -->
        <x-footer />
    </div>

    <script>
        // ── Client-side Pagination — Detail Mata Kuliah (Nilai & Presensi) ──
        (function() {
            function setupPager(tbodyId, paginatorId, infoId, perPage = 10) {
                let currentPage = 1;
                function getRows() {
                    return Array.from(document.querySelectorAll('#' + tbodyId + ' tr'))
                        .filter(r => !r.querySelector('td[colspan]'));
                }
                function render() {
                    const rows = getRows();
                    const total = rows.length;
                    const totalPages = Math.max(1, Math.ceil(total / perPage));
                    if (currentPage > totalPages) currentPage = 1;
                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, total);
                    rows.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });
                    const infoEl = document.getElementById(infoId);
                    if (infoEl) infoEl.textContent = total > 0 ? `Menampilkan ${start + 1}–${end} dari ${total} data` : '';
                    renderControls(totalPages);
                }
                function goTo(p) {
                    const rows = getRows();
                    const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
                    currentPage = Math.max(1, Math.min(p, totalPages));
                    render();
                }
                function renderControls(totalPages) {
                    const el = document.getElementById(paginatorId);
                    if (!el) return;
                    if (totalPages <= 1) { el.innerHTML = ''; return; }
                    const btnStyle = (active, disabled) =>
                        `style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:700;cursor:${disabled ? 'default' : 'pointer'};border:1px solid ${active ? 'transparent' : (disabled ? '#E2E8F0' : '#CBD5E1')};background:${active ? '#039FFA' : (disabled ? '#F8FAFC' : '#FFF')};color:${active ? '#FFF' : (disabled ? '#CBD5E1' : '#334155')};transition:all .15s;"`;
                    let html = '';
                    const prevDis = currentPage === 1;
                    html += `<button type="button" ${btnStyle(false, prevDis)} data-p="${currentPage - 1}" ${prevDis ? 'disabled' : ''}>&#8249;</button>`;
                    const delta = 2;
                    for (let i = Math.max(1, currentPage - delta); i <= Math.min(totalPages, currentPage + delta); i++) {
                        html += `<button type="button" ${btnStyle(i === currentPage, false)} data-p="${i}">${i}</button>`;
                    }
                    const nextDis = currentPage === totalPages;
                    html += `<button type="button" ${btnStyle(false, nextDis)} data-p="${currentPage + 1}" ${nextDis ? 'disabled' : ''}>&#8250;</button>`;
                    el.innerHTML = html;
                    el.querySelectorAll('button[data-p]').forEach(btn => {
                        if (!btn.disabled) btn.addEventListener('click', () => goTo(Number(btn.dataset.p)));
                    });
                }
                return render;
            }

            const renderGrade = setupPager('gradeTableBody', 'gradeTablePaginator', 'gradeTablePaginatorInfo', 10);
            const renderPresensi = setupPager('coursePresensiTbody', 'coursePresensiPaginator', 'coursePresensiPaginatorInfo', 10);

            document.addEventListener('DOMContentLoaded', () => {
                renderGrade();
                renderPresensi();
            });
        })();
        // ──────────────────────────────────────────────────────────────────
    </script>

</body>
</html>
