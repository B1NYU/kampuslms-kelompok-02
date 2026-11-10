<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Materi — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Hanya Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dosen/dosen.materi.js'])
    @endif
</head>

<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- [RESPONSIVE-MOBILE] Konten Utama Unggah Materi (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            @php
                // $courses dan $materials dikirim controller
                $activeCourses  = $courses;
                $allDbMaterials = $materials;
            @endphp

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Materi Perkuliahan
                    </h1>
                </div>

                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-sky-100 shadow-xs w-full sm:w-auto overflow-hidden">
                    <span class="text-xs font-bold text-slate-500 shrink-0">MK:</span>
                    <select id="selectCurrentCourse"
                            class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0 truncate w-full sm:w-auto"
                            {{ $activeCourses->isEmpty() ? 'disabled' : '' }}>
                        @forelse ($activeCourses as $idx => $c)
                            <option value="{{ $c->id }}" {{ $idx === 0 ? 'selected' : '' }}>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @empty
                            <option value="">Belum ada mata kuliah aktif</option>
                        @endforelse
                    </select>
                </div>
            </header>

            <!-- Section: Unggah Materi -->
            <section class="flex flex-col gap-6" id="unggah-materi">
                <div class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                    <path d="M12 12v9"></path>
                                    <path d="m16 16-4-4-4 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">Publikasi &amp; Distribusi Modul Perkuliahan</h2>
                                <span class="text-xs text-slate-500 font-bold">Bagikan modul PDF, presentasi PPTX, atau tautan referensi</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            @can('create', [\App\Models\Material::class, null])
                            <button type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer"
                                    id="btnOpenUploadMaterialModal">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Unggah Materi Baru</span>
                            </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Grid Materi yang Terpublikasi -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-700">
                                Koleksi Materi Perkuliahan (<strong id="materialListCount" class="text-sky-600 font-black">{{ $allDbMaterials->count() }}</strong> Modul)
                            </span>
                            <span class="text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                Semester Genap 2026
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="materialsGrid">
                            @forelse ($allDbMaterials as $material)
                                <div class="material-card bg-white rounded-2xl border border-sky-100 p-5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-4"
                                     data-course-id="{{ $material->course_id }}">
                                    <div class="flex flex-col gap-2.5">
                                        <div class="flex items-center justify-between">
                                            @if ($material->type === 'file')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-red-50 text-red-600 border border-red-200/60">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    </svg>
                                                    {{ strtoupper(pathinfo($material->original_name ?? '', PATHINFO_EXTENSION) ?: 'FILE') }} &bull; Berkas File
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-sky-50 text-sky-600 border border-sky-200/60">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                    </svg>
                                                    LINK &bull; Tautan
                                                </span>
                                            @endif
                                            @if ($material->session)
                                                <span class="text-[10px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                                    P-{{ $material->session }}
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-black text-slate-900 leading-snug line-clamp-2">{{ $material->title }}</h4>
                                        <p class="text-xs text-slate-500 line-clamp-2 font-medium">{{ $material->description ?? 'Tidak ada catatan.' }}</p>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-3">
                                        <span class="text-[11px] font-bold text-slate-400">
                                            Diunggah: {{ $material->created_at ? $material->created_at->diffForHumans() : '-' }}
                                        </span>
                                        <div class="flex items-center justify-between gap-2">
                                            @can('view', $material)
                                            @if ($material->type === 'file')
                                                <a href="{{ route('dosen.materi.download', $material) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                                    </svg>
                                                    Unduh File
                                                </a>
                                            @else
                                                <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                                    </svg>
                                                    Buka Link
                                                </a>
                                            @endif
                                            @endcan

                                            <div class="flex items-center gap-1.5">
                                                @can('update', $material)
                                                <button type="button"
                                                        class="btn-edit-material p-1.5 rounded-lg text-sky-600 hover:bg-sky-50 border border-sky-200 transition cursor-pointer"
                                                        title="Edit materi"
                                                        data-id="{{ $material->id }}"
                                                        data-type="{{ $material->type }}"
                                                        data-title="{{ $material->title }}"
                                                        data-description="{{ $material->description }}"
                                                        data-url="{{ $material->external_url }}"
                                                        data-filename="{{ $material->original_name }}">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                    </svg>
                                                </button>
                                                @endcan

                                                @can('delete', $material)
                                                <form action="{{ route('dosen.materi.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')" class="inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 transition cursor-pointer"
                                                            title="Hapus materi">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state text-center py-12 px-4 col-span-full bg-slate-50 rounded-2xl border-2 border-dashed border-sky-200/80" id="materialEmptyState">
                                    <div class="text-4xl mb-2">📂</div>
                                    <div class="text-sm font-black text-slate-800 mb-1">Belum Ada Materi Perkuliahan</div>
                                    <p class="text-xs text-slate-500 mb-4 max-w-md mx-auto">Klik tombol di atas untuk mempublikasikan materi perkuliahan baru (PDF, PPTX, atau tautan referensi).</p>
                                    @can('create', [\App\Models\Material::class, null])
                                    <button type="button"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs cursor-pointer mx-auto"
                                            onclick="document.getElementById('btnOpenUploadMaterialModal').click()">
                                        + Unggah Materi Pertama
                                    </button>
                                    @endcan
                                </div>
                            @endforelse
                        </div>

                        {{-- Navigasi Pagination Laravel --}}
                        @if ($materials->hasPages())
                            <div class="mt-4">
                                {{ $materials->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM UNGGAH MATERI -->
        <div class="dosen-modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200 hidden [&.active]:flex" id="materialModalOverlay">
            <div class="w-full max-w-[620px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Publikasi Materi Perkuliahan</h3>
                            <span class="text-[11px] text-slate-500 font-bold">Unggah dokumen PDF, slide PPTX, atau tautan web</span>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none" id="btnCloseMaterialModal" aria-label="Tutup modal">&times;</button>
                </div>

                <!-- FORM TERHUBUNG DENGAN LARAVEL -->
                <form id="formUploadMaterial" action="{{ route('dosen.materi.store') }}" method="POST" enctype="multipart/form-data" class="m-0 flex flex-col">
                    @csrf
                    <input type="hidden" name="type" id="materialType" value="{{ old('type', 'file') }}">

                    <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-xs font-bold">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="materialCourse" class="block text-xs font-black text-slate-700 mb-1.5">
                                    Mata Kuliah Aktif <span class="text-red-500">*</span>
                                </label>
                                <select id="materialCourse" name="course_id" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required {{ $activeCourses->isEmpty() ? 'disabled' : '' }}>
                                    @forelse ($activeCourses as $c)
                                        <option value="{{ $c->id }}" data-code="{{ $c->code }}" @selected((int) old('course_id') === $c->id)>{{ $c->code }} - {{ $c->name }}</option>
                                    @empty
                                        <option value="">Belum ada mata kuliah aktif</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <label for="materialSession" class="block text-xs font-black text-slate-700 mb-1.5">Pertemuan Ke-</label>
                                <select id="materialSession" name="session" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400">
                                    <option value="">Materi umum (tanpa pertemuan)</option>
                                    @for ($i = 1; $i <= 16; $i++)
                                        <option value="{{ $i }}" @selected((int) old('session') === $i)>Pertemuan {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="materialTitle" class="block text-xs font-black text-slate-700 mb-1.5">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="materialTitle" name="title" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="Contoh: Modul 06 - Autentikasi Multi-Role Laravel" value="{{ old('title') }}" required>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5">Pilih Format Materi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="type-pill flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 bg-white hover:border-sky-300 cursor-pointer transition [&.active]:border-sky-500 [&.active]:bg-sky-50 [&.active]:text-sky-600 [&.active]:shadow-xs active" data-type="file">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <span>Berkas File (PDF / PPTX)</span>
                                </div>
                                <div class="type-pill flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 bg-white hover:border-sky-300 cursor-pointer transition [&.active]:border-sky-500 [&.active]:bg-sky-50 [&.active]:text-sky-600 [&.active]:shadow-xs" data-type="link">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                    </svg>
                                    <span>Tautan / Link</span>
                                </div>
                            </div>
                        </div>

                        <div id="fileUploadContainer">
                            <label class="block text-xs font-black text-slate-700 mb-1.5">Unggah Berkas File (PDF / PPTX)</label>
                            <div class="relative border-2 border-dashed border-sky-200 hover:border-sky-400 bg-sky-50/40 hover:bg-sky-50/70 rounded-2xl p-6 text-center transition flex flex-col items-center justify-center gap-2 cursor-pointer" id="materialDropzone">
                                <svg class="w-8 h-8 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span class="text-xs sm:text-sm font-bold text-slate-700" id="dropzoneText">Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini</span>
                                <span class="text-[11px] font-semibold text-slate-400">Maksimal ukuran file: 50MB (Format PDF, PPT, PPTX)</span>

                                {{-- Input menutupi seluruh dropzone (transparan) --}}
                                <input type="file" id="materialFileInput" name="file" accept=".pdf,.pptx,.ppt" title=" "
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer block">
                            </div>

                            <!-- PREVIEW CONTAINER BERKAS MATERI DOSEN -->
                            <div id="materialPreviewContainer" style="display: none;" class="mt-3 bg-white border border-sky-200 rounded-xl p-3.5 shadow-xs">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div id="materialFileTypeBadge" class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xs font-black">
                                            PDF
                                        </div>
                                        <div class="min-w-0">
                                            <div id="materialPreviewName" class="text-xs sm:text-sm font-black text-slate-800 truncate">-</div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] font-bold">
                                                <span id="materialPreviewSize" class="text-slate-500">-</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded border border-emerald-200">✓ Siap Dipublikasikan</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" id="btnChangeMaterial" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                                            Ganti Berkas
                                        </button>
                                        <button type="button" id="btnRemoveMaterial" class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                <!-- EMBEDDED PREVIEW JIKA BERKAS PDF -->
                                <div id="materialInteractivePreview" style="display: none;" class="mt-3 pt-3 border-t border-slate-100">
                                    <div class="text-xs font-black text-slate-700 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Pratinjau Dokumen PDF
                                    </div>
                                    <div id="materialEmbedHolder" class="w-full h-80 rounded-lg overflow-hidden bg-slate-50 border border-slate-200"></div>
                                </div>
                            </div>

                            <div id="fileStatus" role="status" aria-live="polite" style="display: none;" class="mt-2.5 px-3 py-2 rounded-xl text-xs font-bold"></div>
                        </div>

                        <div id="linkInputContainer" style="display: none;">
                            <label for="materialUrl" class="block text-xs font-black text-slate-700 mb-1.5">
                                URL Tautan / Link Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="url" id="materialUrl" name="external_url" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="https://laravel.com/docs/12.x/authentication" value="{{ old('external_url') }}">
                        </div>

                        <div>
                            <label for="materialDesc" class="block text-xs font-black text-slate-700 mb-1.5">Catatan Pembelajaran / Petunjuk</label>
                            <textarea id="materialDesc" name="description" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" rows="2" placeholder="Tulis ringkasan atau instruksi bagi mahasiswa...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer" id="btnCancelMaterialModal">Batal</button>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Unggah &amp; Publikasikan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL FORM EDIT MATERI -->
        <div class="dosen-modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200 hidden [&.active]:flex" id="editMaterialModalOverlay">
            <div class="w-full max-w-[620px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Edit Materi Perkuliahan</h3>
                        <span class="text-[11px] text-slate-500 font-bold">Ubah judul, catatan, atau ganti berkas / tautan</span>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none" id="btnCloseEditModal" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="formEditMaterial" action="#" method="POST" enctype="multipart/form-data" class="m-0 flex flex-col"
                      data-action-template="{{ route('dosen.materi.update', ['material' => '__ID__']) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" id="editMaterialId" value="{{ old('edit_id') }}">

                    <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">

                        @if ($errors->editMaterial->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-xs font-bold">
                                @foreach ($errors->editMaterial->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div>
                            <label for="editMaterialTitle" class="block text-xs font-black text-slate-700 mb-1.5">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editMaterialTitle" name="title" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required>
                        </div>

                        <div id="editFileContainer">
                            <label for="editMaterialFile" class="block text-xs font-black text-slate-700 mb-1.5">Ganti Berkas (opsional)</label>
                            <div id="editCurrentFile" class="text-xs text-slate-500 font-bold mb-1.5 truncate"></div>
                            <input type="file" id="editMaterialFile" name="file" accept=".pdf,.pptx,.ppt" class="w-full px-3.5 py-2 text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400">
                            <span class="block text-[11px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti berkas. Maks 50MB (PDF, PPT, PPTX).</span>
                        </div>

                        <div id="editLinkContainer" style="display: none;">
                            <label for="editMaterialUrl" class="block text-xs font-black text-slate-700 mb-1.5">
                                URL Tautan / Link Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="url" id="editMaterialUrl" name="external_url" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="https://...">
                        </div>

                        <div>
                            <label for="editMaterialDesc" class="block text-xs font-black text-slate-700 mb-1.5">Catatan Pembelajaran / Petunjuk</label>
                            <textarea id="editMaterialDesc" name="description" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer" id="btnCancelEditModal">Batal</button>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <x-footer />
    </div>

    <!-- Toast Notification Container -->
    <div class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none" id="toastContainer"></div>

    <div id="dosenMateriConfig" style="display:none;" data-has-errors="{{ $errors->any() ? '1' : '0' }}" data-edit-error-id="{{ ($errors->editMaterial->any() && old('edit_id')) ? old('edit_id') : '' }}" data-old-title="{{ old('title', '') }}" data-old-desc="{{ old('description', '') }}" data-old-url="{{ old('external_url', '') }}"></div>
</body>

</html>
