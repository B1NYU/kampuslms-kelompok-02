<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mata Kuliah — KampusLMS Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.matkul.js'])
    @endif
    <style>
        #matkulModalOverlay { display: none; }
        #matkulModalOverlay.active { display: flex; }
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

        <!-- Konten Utama Manajemen Mata Kuliah -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Mata Kuliah</h1>
                    </div>
                </div>
                <div>
                    @can('create', App\Models\Course::class)
                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnOpenAddMatkulModal">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Mata Kuliah</span>
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-rose-50 text-rose-700 border border-rose-200/80 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @php
                $isEditing = (bool) old('id');
            @endphp

            <!-- Card Utama: Tabel Mata Kuliah -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

                <!-- Toolbar: Info Total & Form Filter -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    {{-- Info Total Data & Badge Filter Aktif --}}
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs sm:text-sm font-bold text-slate-500">
                            Total <strong id="mkTableCount" class="font-black text-slate-900">{{ $matkulList->total() }}</strong> Mata Kuliah
                        </span>
                        @if ($filters['q'] || $filters['status'] || $filters['lecturer_id'])
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-50 text-[#039FFA] border border-sky-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#039FFA]"></span>
                                Filter Aktif
                            </span>
                        @endif
                    </div>

                    {{-- Form Filter Otomatis --}}
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                        {{-- Input Pencarian --}}
                        <div class="relative w-full sm:w-60 md:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari kode / nama MK..."
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all shadow-xs">
                        </div>

                        {{-- Dropdown Status --}}
                        <select name="status" onchange="this.form.submit()"
                                class="px-3 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer shadow-xs">
                            <option value="">Semua Status</option>
                            @foreach (['draft', 'active', 'archived'] as $s)
                                <option value="{{ $s }}" @selected($filters['status'] === $s)>
                                    {{ ['draft' => 'Draft', 'active' => 'Aktif', 'archived' => 'Diarsipkan'][$s] ?? ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Dropdown Dosen --}}
                        <select name="lecturer_id" onchange="this.form.submit()"
                                class="px-3 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer shadow-xs max-w-[190px] truncate">
                            <option value="">Semua Dosen</option>
                            @foreach ($dosenList as $d)
                                <option value="{{ $d->id }}" @selected($filters['lecturer_id'] == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>

                        {{-- Tombol Reset --}}
                        @if ($filters['q'] || $filters['status'] || $filters['lecturer_id'])
                            <a href="{{ url()->current() }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Reset</span>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tabel Mata Kuliah -->
                <div class="overflow-x-auto" style="max-height: 580px; overflow-y: auto;">
                    <table class="w-full text-left border-collapse" id="mkTable">
                        <thead>
                            <tr class="sticky top-0 z-10 bg-slate-50/95 backdrop-blur-xs border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Kode</th>
                                <th class="py-3.5 px-5">Nama Mata Kuliah</th>
                                <th class="py-3.5 px-5">SKS</th>
                                <th class="py-3.5 px-5">Dosen</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mkTableBody" class="divide-y divide-slate-100 text-xs sm:text-sm font-semibold">
                            @forelse ($matkulList as $mk)
                            <tr data-id="{{ $mk->id }}"
                                data-kode="{{ $mk->code }}"
                                data-nama="{{ $mk->name }}"
                                data-sks="{{ $mk->sks }}"
                                data-lecturer-id="{{ $mk->lecturer_id }}"
                                data-lecturer-name="{{ $mk->lecturer?->name }}"
                                data-status="{{ $mk->status }}"
                                data-deskripsi="{{ $mk->description }}"
                                class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-sky-50 text-[#039FFA] border border-sky-200/70 shadow-2xs">
                                        {{ $mk->code }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-slate-900 text-sm leading-snug">{{ $mk->name }}</span>
                                        @if ($mk->description)
                                            <span class="text-[11px] font-medium text-slate-400 truncate max-w-sm mt-0.5">{{ $mk->description }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-black bg-slate-100 text-slate-700 border border-slate-200/60">
                                        {{ $mk->sks }} SKS
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="text-xs sm:text-sm font-bold text-slate-700">
                                        {{ $mk->lecturer?->name ?? '—' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($mk->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>AKTIF
                                        </span>
                                    @elseif ($mk->status === 'draft')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-600 border border-amber-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>DRAFT
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>DIARSIPKAN
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @can('update', $mk)
                                            <button type="button" class="btn-edit-mk p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-[#039FFA] hover:border-sky-300 hover:bg-sky-50/60 shadow-xs transition-all cursor-pointer" title="Edit mata kuliah">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('delete', $mk)
                                            <form method="POST" action="{{ route('admin.matkul.destroy', ['matkul' => $mk->id]) }}"
                                                  onsubmit="return confirm('Hapus mata kuliah ini?');" class="inline-block m-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect_q" value="{{ $filters['q'] }}">
                                                <input type="hidden" name="redirect_status" value="{{ $filters['status'] }}">
                                                <input type="hidden" name="redirect_lecturer_id" value="{{ $filters['lecturer_id'] }}">
                                                <button type="submit" class="p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/60 shadow-xs transition-all cursor-pointer" title="Hapus mata kuliah">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="mkEmptyRow">
                                <td colspan="6" class="text-center py-12 text-slate-400 font-semibold">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                        </svg>
                                        <span>Belum ada data mata kuliah.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Navigasi Pagination Laravel --}}
                @if ($matkulList->hasPages())
                    <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
                        {{ $matkulList->links() }}
                    </div>
                @endif
            </section>

        </main>

        <!-- MODAL FORM TAMBAH / EDIT MATA KULIAH -->
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 {{ ($errors->any() || $isEditing) ? 'active' : '' }}" id="matkulModalOverlay">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-gradient-to-r from-sky-50 via-white to-amber-50/40 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100/80 text-[#039FFA] shrink-0 border border-sky-200/60">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 id="formMkTitle" class="text-base font-black text-slate-900 m-0">
                                <span>{{ $isEditing ? 'Form Edit Mata Kuliah' : 'Form Tambah / Edit MK' }}</span>
                            </h3>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btnCloseMatkulModal" aria-label="Tutup modal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 text-xs font-bold px-3.5 py-2.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200">
                            ⚠️ {{ $errors->first() }}
                        </div>
                    @endif

                    <form id="formAddMatkul" method="POST"
                          action="{{ $isEditing ? route('admin.matkul.update', ['matkul' => old('id')]) : route('admin.matkul.store') }}"
                          class="flex flex-col gap-4 m-0">
                        @csrf
                        <input type="hidden" id="mkId" name="id" value="{{ old('id') }}">
                        <input type="hidden" id="mkMethodField" name="_method" value="{{ $isEditing ? 'PUT' : '' }}">
                        <input type="hidden" name="redirect_q" value="{{ $filters['q'] }}">
                        <input type="hidden" name="redirect_status" value="{{ $filters['status'] }}">
                        <input type="hidden" name="redirect_lecturer_id" value="{{ $filters['lecturer_id'] }}">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="mkKode" class="block text-xs font-bold text-slate-700 mb-1.5">Kode MK <span class="text-rose-500">*</span></label>
                                <input type="text" id="mkKode" name="code" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all uppercase" placeholder="Contoh: SI2514024" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label for="mkSks" class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah SKS <span class="text-rose-500">*</span></label>
                                <input type="number" id="mkSks" name="sks" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Contoh: 3" min="1" value="{{ old('sks', 3) }}" required>
                                @error('sks')
                                    <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="mkNama" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Mata Kuliah <span class="text-rose-500">*</span></label>
                            <input type="text" id="mkNama" name="name" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Nama lengkap mata kuliah..." value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="mkDosen" class="block text-xs font-bold text-slate-700 mb-1.5">Dosen Pengampu <span class="text-rose-500">*</span></label>
                            <select id="mkDosen" name="lecturer_id" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer" required>
                                <option value="">— Pilih Dosen —</option>
                                @foreach ($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('lecturer_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                            @error('lecturer_id')
                                <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                            @enderror
                            @if ($dosenList->isEmpty())
                                <small class="text-amber-600 font-bold text-[11px] mt-1 block">Belum ada user dengan role "dosen". Tambahkan lewat Manajemen Pengguna dulu.</small>
                            @endif
                        </div>

                        <div>
                            <label for="mkStatus" class="block text-xs font-bold text-slate-700 mb-1.5">Status</label>
                            <select id="mkStatus" name="status" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                            </select>
                            @error('status')
                                <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="mkDeskripsi" class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Singkat</label>
                            <textarea id="mkDeskripsi" name="description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all resize-y" placeholder="Gambaran singkat mata kuliah ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex gap-2.5 mt-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-sm cursor-pointer transition-all" id="mkSubmitBtn">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Simpan Mata Kuliah</span>
                            </button>
                            <button type="button" id="mkCancelEditBtn" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all cursor-pointer">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

    <div id="matkulRoutes" style="display:none;" data-store-url="{{ route('admin.matkul.store') }}" data-update-url-template="{{ route('admin.matkul.update', ['matkul' => '__ID__']) }}" data-is-editing="{{ $isEditing ? '1' : '0' }}"></div>
</body>
</html>
