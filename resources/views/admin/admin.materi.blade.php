<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Materi Kuliah — KampusLMS Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.materi.js'])
    @endif

    <style>
        #materialModalOverlay, #editMaterialModalOverlay { display: none; }
        #materialModalOverlay.active, #editMaterialModalOverlay.active { display: flex; }
        #courseFilterDropdown.open .filter-dropdown-menu { display: block; }
        .type-pill.active {
            background-color: #039FFA !important;
            color: #ffffff !important;
            border-color: #039FFA !important;
            box-shadow: 0 2px 8px rgba(3, 159, 250, 0.25) !important;
        }
        .filter-option-item.active {
            background-color: #F0F9FF;
            color: #039FFA;
            font-weight: 800;
        }
    </style>
</head>
<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed top-[-80px] right-[-80px] w-96 h-96 bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-80px] left-[-80px] w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Manajemen Materi -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Materi Perkuliahan</h1>
                    </div>
                </div>
                <div>
                    @can('create', [\App\Models\Material::class, null])
                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnOpenUploadMaterialModal">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Unggah Materi Baru</span>
                        </button>
                    @endcan
                </div>
            </header>

            @php
                $materials = $materials ?? \App\Models\Material::with(['course', 'uploader'])->latest()->get();
                $coursesList = $coursesList ?? \App\Models\Course::with('lecturer')->orderBy('code')->get();
                $totalDoc = $materials->where('type', 'file')->count();
                $totalLink = $materials->where('type', 'link')->count();
            @endphp

            @if(session('success'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any() && !old('edit_id'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-rose-50 text-rose-700 border border-rose-200/80 flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span>❌</span>
                        <span>Terdapat kesalahan pada formulir:</span>
                    </div>
                    <ul class="list-disc pl-6 font-semibold text-xs text-rose-600 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Baris Statistik Cepat (Asli dari Halaman) -->
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <!-- 1. Total Materi -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-sky-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Materi</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block" id="statTotalMateri">{{ $materials->count() }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">Semua format</span>
                    </div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-sky-50 text-[#039FFA] border border-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                </div>

                <!-- 2. Berkas Dokumen -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-orange-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Berkas Dokumen</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block" id="statTotalDoc">{{ $totalDoc }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">PDF, PPT, DOCX</span>
                    </div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-orange-50 text-orange-500 border border-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- 3. Tautan / Link -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-amber-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Tautan / Link</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block" id="statTotalLink">{{ $totalLink }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">URL Eksternal</span>
                    </div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-500 border border-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </div>
                </div>

                <!-- 4. Mata Kuliah Aktif -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Mata Kuliah</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block" id="statTotalMk">{{ $coursesList->count() }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">Aktif tersedia</span>
                    </div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-500 border border-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                </div>
            </section>

            <!-- Toolbar & Filter -->
            <section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3.5">
                <!-- Search Box -->
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <input type="text" id="inputSearchMateri" placeholder="Cari judul materi perkuliahan..." autocomplete="off"
                           class="w-full pl-10 pr-4 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all shadow-xs">
                </div>

                <!-- Dropdown & Select Filters -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Custom Course Filter Dropdown -->
                    <div class="relative" id="courseFilterDropdown">
                        <button type="button" class="inline-flex items-center justify-between gap-2 px-3.5 py-2 bg-slate-50/80 hover:bg-slate-100 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-bold text-slate-700 transition-all cursor-pointer shadow-xs" id="courseFilterBtn">
                            <span id="courseFilterText" class="truncate max-w-[200px] sm:max-w-[240px]">Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="filter-dropdown-menu hidden absolute right-0 top-[calc(100%+6px)] w-72 bg-white rounded-xl shadow-xl border border-slate-100 p-1.5 z-40 max-h-64 overflow-y-auto">
                            <ul class="flex flex-col gap-0.5 list-none m-0 p-0" id="courseFilterOptions">
                                <li class="filter-option-item active px-3 py-2 rounded-lg text-xs font-bold text-slate-700 hover:bg-sky-50 hover:text-[#039FFA] cursor-pointer transition-colors flex items-center justify-between" data-value="all" data-label="Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)">
                                    <span>Semua Mata Kuliah</span>
                                </li>
                                @foreach ($coursesList as $c)
                                    <li class="filter-option-item px-3 py-2 rounded-lg text-xs font-bold text-slate-700 hover:bg-sky-50 hover:text-[#039FFA] cursor-pointer transition-colors flex items-center gap-2" data-value="{{ $c->id }}" data-label="{{ $c->code }} • {{ $c->name }}">
                                        <span class="font-mono text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $c->code }}</span>
                                        <span class="truncate">{{ $c->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" id="selectFilterCourse" value="all">

                    <!-- Type Filter Select -->
                    <div class="relative">
                        <select id="selectFilterType"
                                class="px-3.5 py-2 pr-8 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer shadow-xs appearance-none">
                            <option value="all">Semua Format</option>
                            <option value="file">📄 Berkas Dokumen</option>
                            <option value="link">🔗 Tautan / URL</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grid Kartu Materi -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5" id="materialsGrid">
                @foreach ($materials as $m)
                    @php
                        $courseCode = $m->course ? $m->course->code : 'UMUM';
                        $courseName = $m->course ? $m->course->name : 'Mata Kuliah Umum';
                        $uploaderName = $m->uploader ? $m->uploader->name : 'Admin LMS';

                        $badgeBg = 'bg-rose-50 text-rose-600 border-rose-200/70';
                        $typeIcon = '📄';
                        $ext = 'PDF';

                        if ($m->type === 'link') {
                            $badgeBg = 'bg-sky-50 text-[#039FFA] border-sky-200/70';
                            $typeIcon = '🔗';
                            $ext = 'LINK';
                        } elseif ($m->original_name) {
                            $fileExt = strtolower(pathinfo($m->original_name, PATHINFO_EXTENSION));
                            if (in_array($fileExt, ['ppt', 'pptx'])) {
                                $badgeBg = 'bg-orange-50 text-orange-600 border-orange-200/70';
                                $typeIcon = '📊';
                                $ext = 'PPTX';
                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                $badgeBg = 'bg-blue-50 text-blue-600 border-blue-200/70';
                                $typeIcon = '📝';
                                $ext = 'DOCX';
                            }
                        }

                        $fileSizeStr = $m->file_size ? number_format($m->file_size / (1024 * 1024), 1) . ' MB' : '-';
                        $fileNameStr = $m->type === 'file' ? ($m->original_name ?? 'Berkas Dokumen') : $m->external_url;
                    @endphp

                    <div class="material-card bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between hover:shadow-md hover:-translate-y-0.5 transition duration-200"
                          data-course-id="{{ $m->course_id }}"
                          data-type="{{ $m->type }}"
                          data-title="{{ $m->title }}"
                          data-desc="{{ $m->description }}"
                          data-filename="{{ $fileNameStr }}"
                          data-url="{{ $m->external_url }}">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-black bg-slate-100 text-slate-700 border border-slate-200" title="{{ $courseName }}">{{ $courseCode }}</span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border {{ $badgeBg }}">
                                        <span>{{ $typeIcon }}</span>
                                        <span>{{ strtoupper($ext) }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @can('update', $m)
                                        <button type="button" class="btn-edit-material p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-[#039FFA] hover:border-sky-300 hover:bg-sky-50 transition-all cursor-pointer" title="Edit Materi"
                                                data-id="{{ $m->id }}"
                                                data-type="{{ $m->type }}"
                                                data-title="{{ $m->title }}"
                                                data-description="{{ $m->description }}"
                                                data-url="{{ $m->external_url }}"
                                                data-filename="{{ $m->original_name }}">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                    @endcan
                                    @can('delete', $m)
                                        <form action="{{ route('admin.materi.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')" class="inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 transition-all cursor-pointer" title="Hapus Materi">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            <h3 class="text-sm font-black text-slate-900 leading-snug mb-1">{{ $m->title }}</h3>
                            @if ($m->description)
                                <p class="text-xs font-semibold text-slate-500 line-clamp-2 mb-3">{{ $m->description }}</p>
                            @endif

                            <div class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] font-semibold text-slate-600 w-full mb-4">
                                <span class="truncate flex-1" title="{{ $fileNameStr }}">{{ $fileNameStr }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-white border border-slate-200 shrink-0">{{ $fileSizeStr }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-black shrink-0 border border-slate-200">
                                    {{ strtoupper(substr($uploaderName, 0, 2)) }}
                                </div>
                                <div class="flex flex-col min-w-0 leading-tight">
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $uploaderName }}</span>
                                    <span class="text-[10px] font-semibold text-slate-400">{{ $m->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div>
                                @can('view', $m)
                                    @if($m->type === 'file')
                                        <a href="{{ route('admin.materi.download', $m->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-sky-50 text-[#039FFA] hover:bg-sky-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            <span>Unduh</span>
                                        </a>
                                    @else
                                        <a href="{{ $m->external_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-sky-50 text-[#039FFA] hover:bg-sky-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            <span>Buka Link</span>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Empty State -->
                <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-12 text-center" id="materiEmptyState" style="display: none;">
                    <div class="text-4xl mb-3">📂</div>
                    <div class="text-base font-black text-slate-800 mb-1">Tidak Ada Materi yang Cocok</div>
                    <p class="text-xs font-semibold text-slate-500 max-w-sm mx-auto mb-4">Tidak ditemukan materi untuk filter atau kata kunci pencarian yang Anda pilih.</p>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#039FFA] hover:bg-sky-600 transition-all cursor-pointer shadow-sm" onclick="resetFilters()">
                        Reset Filter
                    </button>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

    <!-- MODAL FORM UNGGAH MATERI -->
    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200" id="materialModalOverlay">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-4.5 bg-gradient-to-r from-sky-50 via-white to-amber-50/40 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100/80 text-[#039FFA] shrink-0 border border-sky-200/60">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 m-0">Publikasi Materi Perkuliahan</h3>
                        <p class="text-xs font-semibold text-slate-500 m-0 mt-0.5">Unggah dokumen PDF, slide PPTX, atau tautan web</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btnCloseMaterialModal" aria-label="Tutup modal">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="formUploadMaterial" action="{{ route('admin.materi.store') }}" method="POST" enctype="multipart/form-data" class="m-0 flex flex-col">
                @csrf
                <input type="hidden" name="type" id="materialType" value="{{ old('type', 'file') }}">

                <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="materialCourse" class="block text-xs font-bold text-slate-700 mb-1.5">Mata Kuliah Aktif <span class="text-rose-500">*</span></label>
                            <select id="materialCourse" name="course_id" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer" required {{ $coursesList->isEmpty() ? 'disabled' : '' }}>
                                <option value="">Pilih Mata Kuliah...</option>
                                @forelse ($coursesList as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->code }}" @selected((int) old('course_id') === $c->id)>{{ $c->code }} - {{ $c->name }}</option>
                                @empty
                                    <option value="">Belum ada mata kuliah aktif</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label for="materialSession" class="block text-xs font-bold text-slate-700 mb-1.5">Pertemuan Ke-</label>
                            <select id="materialSession" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer">
                                @for ($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" {{ $i === 6 ? 'selected' : '' }}>Pertemuan {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="materialTitle" class="block text-xs font-bold text-slate-700 mb-1.5">Judul Materi <span class="text-rose-500">*</span></label>
                        <input type="text" id="materialTitle" name="title" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Contoh: Modul 06 - Autentikasi Multi-Role Laravel" value="{{ old('title') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Format Materi <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="type-pill active flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-200/80 bg-white text-slate-700 text-xs font-bold cursor-pointer transition-all hover:bg-slate-50" data-type="file">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <span>Berkas File (PDF / PPTX)</span>
                            </div>
                            <div class="type-pill flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-200/80 bg-white text-slate-700 text-xs font-bold cursor-pointer transition-all hover:bg-slate-50" data-type="link">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                </svg>
                                <span>Tautan / Link</span>
                            </div>
                        </div>
                    </div>

                    <div id="fileUploadContainer">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Berkas File (PDF / PPTX)</label>
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-sky-400 bg-slate-50/60 rounded-2xl p-6 text-center transition-colors cursor-pointer" id="materialDropzone">
                            <svg class="w-8 h-8 text-sky-500 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span class="block text-xs font-bold text-slate-700" id="dropzoneText">Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini</span>
                            <span class="block text-[11px] font-semibold text-slate-400 mt-1">Maksimal ukuran file: 50MB (Format PDF, PPT, PPTX)</span>
                            <input type="file" id="materialFileInput" name="file" accept=".pdf,.pptx,.ppt" title=" "
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                        <div id="fileStatus" role="status" aria-live="polite" style="display: none;" class="mt-2 text-xs font-bold px-3 py-2 rounded-xl"></div>
                    </div>

                    <div id="linkInputContainer" style="display: none;">
                        <label for="materialUrl" class="block text-xs font-bold text-slate-700 mb-1.5">URL Tautan / Link Materi <span class="text-rose-500">*</span></label>
                        <input type="url" id="materialUrl" name="external_url" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="https://laravel.com/docs/12.x/authentication" value="{{ old('external_url') }}">
                    </div>

                    <div>
                        <label for="materialDesc" class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pembelajaran / Petunjuk</label>
                        <textarea id="materialDesc" name="description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all resize-y" placeholder="Tulis ringkasan atau instruksi bagi mahasiswa...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition-all cursor-pointer" id="btnCancelMaterialModal">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-sm cursor-pointer transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Unggah &amp; Publikasikan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL FORM EDIT MATERI -->
    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200" id="editMaterialModalOverlay">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-4.5 bg-gradient-to-r from-sky-50 via-white to-amber-50/40 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 m-0">Edit Materi Perkuliahan</h3>
                    <p class="text-xs font-semibold text-slate-500 m-0 mt-0.5">Ubah judul, catatan, atau ganti berkas / tautan</p>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btnCloseEditModal" aria-label="Tutup modal">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="formEditMaterial" action="#" method="POST" enctype="multipart/form-data" class="m-0 flex flex-col"
                  data-action-template="{{ route('admin.materi.update', ['material' => '__ID__']) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_id" id="editMaterialId" value="{{ old('edit_id') }}">

                <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="editMaterialTitle" class="block text-xs font-bold text-slate-700 mb-1.5">Judul Materi <span class="text-rose-500">*</span></label>
                        <input type="text" id="editMaterialTitle" name="title" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" required>
                    </div>

                    <div id="editFileContainer">
                        <label for="editMaterialFile" class="block text-xs font-bold text-slate-700 mb-1.5">Ganti Berkas (opsional)</label>
                        <div id="editCurrentFile" class="text-xs font-semibold text-slate-500 mb-1.5 break-all"></div>
                        <input type="file" id="editMaterialFile" name="file" accept=".pdf,.pptx,.ppt" class="w-full text-xs font-semibold text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-[#039FFA] hover:file:bg-sky-100">
                        <span class="text-[11px] font-semibold text-slate-400 block mt-1">Kosongkan jika tidak ingin mengganti berkas. Maks 50MB (PDF, PPT, PPTX).</span>
                    </div>

                    <div id="editLinkContainer" style="display: none;">
                        <label for="editMaterialUrl" class="block text-xs font-bold text-slate-700 mb-1.5">URL Tautan / Link Materi <span class="text-rose-500">*</span></label>
                        <input type="url" id="editMaterialUrl" name="external_url" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="https://...">
                    </div>

                    <div>
                        <label for="editMaterialDesc" class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pembelajaran / Petunjuk</label>
                        <textarea id="editMaterialDesc" name="description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all resize-y"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition-all cursor-pointer" id="btnCancelEditModal">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-sm cursor-pointer transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
