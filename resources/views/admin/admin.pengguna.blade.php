<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Pengguna — KampusLMS Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.pengguna.js'])
    @endif
    <style>
        #userModalOverlay { display: none; }
        #userModalOverlay.active { display: flex; }
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

        <!-- Konten Utama Manajemen Pengguna -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                    </div>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnOpenAddUserModal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
            </header>

            <!-- Alert Box Halaman -->
            <div id="userPageAlert" style="display:none;" class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-sm transition-all"></div>

            @php
                $userList = \App\Models\User::withTrashed()
                    ->orderByRaw("CASE WHEN role = 'admin' THEN 1 WHEN role = 'dosen' THEN 2 ELSE 3 END, id ASC")
                    ->get();
                $countAdmin = $userList->where('role', 'admin')->count();
                $countDosen = $userList->where('role', 'dosen')->count();
                $countMahasiswa = $userList->where('role', 'mahasiswa')->count();
                $countTotal = $userList->count();
            @endphp

            <!-- Card Utama: Tabel Pengguna -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

                <!-- Toolbar: Filter Tabs & Pencarian -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Role Filter Group Buttons -->
                    <div class="role-filter-group flex flex-wrap items-center gap-2">
                        <button type="button" class="role-filter-btn active inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#039FFA] text-white shadow-sm border border-transparent transition-all cursor-pointer" data-filter="all">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20" id="filterCountAll">{{ $countTotal }}</span>
                        </button>
                        <button type="button" class="role-filter-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer" data-filter="admin">
                            <span>👑 Admin</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-600" id="filterCountAdmin">{{ $countAdmin }}</span>
                        </button>
                        <button type="button" class="role-filter-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer" data-filter="dosen">
                            <span>🎓 Dosen</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-600" id="filterCountDosen">{{ $countDosen }}</span>
                        </button>
                        <button type="button" class="role-filter-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer" data-filter="mahasiswa">
                            <span>👥 Mahasiswa</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-600" id="filterCountMhs">{{ $countMahasiswa }}</span>
                        </button>
                    </div>

                    <!-- Search Input Box -->
                    <div class="relative w-full md:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" id="searchUserInput" placeholder="Cari nama, NIM/NIP, atau email..."
                               class="w-full pl-10 pr-4 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all shadow-xs">
                    </div>
                </div>

                <!-- Tabel Daftar Pengguna -->
                <div class="overflow-x-auto" style="max-height: 580px; overflow-y: auto;">
                    <table class="w-full text-left border-collapse" id="userTable">
                        <thead>
                            <tr class="sticky top-0 z-10 bg-slate-50/95 backdrop-blur-xs border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Pengguna</th>
                                <th class="py-3.5 px-5">Role</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-slate-100 text-xs sm:text-sm font-semibold">
                            @forelse ($userList as $u)
                            @php
                                $initials = collect(explode(' ', $u->name))->map(fn($w) => mb_substr($w, 0, 1))->join('');
                                $initials = strtoupper(mb_substr($initials, 0, 2));
                                $isNonAktif = $u->trashed();
                                $avatarBg = 'bg-sky-50 text-[#039FFA] border-sky-100';
                                if ($u->role === 'admin') $avatarBg = 'bg-orange-50 text-orange-600 border-orange-100';
                                elseif ($u->role === 'dosen') $avatarBg = 'bg-amber-50 text-amber-600 border-amber-100';
                            @endphp
                            <tr data-id="{{ $u->id }}"
                                data-name="{{ $u->name }}"
                                data-nim-nip="{{ $u->nim_nip }}"
                                data-email="{{ $u->email }}"
                                data-role="{{ $u->role }}"
                                data-status="{{ $isNonAktif ? 'nonaktif' : 'aktif' }}"
                                class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 border {{ $avatarBg }}">
                                            {{ $initials }}
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-bold text-slate-900 text-sm leading-snug">{{ $u->name }}</span>
                                            <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                                                <span>{{ $u->nim_nip ?? '-' }}</span>
                                                @if ($u->email)
                                                    <span class="text-slate-300">•</span>
                                                    <span class="truncate max-w-[200px] text-slate-400">{{ $u->email }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($u->role === 'admin')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-orange-50 text-orange-600 border border-orange-200/70">👑 Admin</span>
                                    @elseif ($u->role === 'dosen')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/70">🎓 Dosen</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-sky-50 text-[#039FFA] border border-sky-200/70">👥 Mahasiswa</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($isNonAktif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Non-aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button type="button" class="btn-edit-user p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-[#039FFA] hover:border-sky-300 hover:bg-sky-50/60 shadow-xs transition-all cursor-pointer" title="Edit pengguna">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-delete-user p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/60 shadow-xs transition-all cursor-pointer" title="Hapus pengguna">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="userEmptyRow">
                                <td colspan="4" class="text-center py-12 text-slate-400 font-semibold">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg>
                                        <span>Belum ada pengguna terdaftar.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Ringkasan Kartu -->
                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                    <div>
                        Menampilkan seluruh akun terdaftar pada sistem
                    </div>
                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                        <span>Total:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-900 text-xs font-black" id="userTableCount">{{ $countTotal }}</span>
                        <span>pengguna</span>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM TAMBAH / EDIT PENGGUNA -->
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200" id="userModalOverlay">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-gradient-to-r from-sky-50 via-white to-amber-50/40 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100/80 text-[#039FFA] shrink-0 border border-sky-200/60">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 id="formUserTitle" class="text-base font-black text-slate-900 m-0">
                                <span>Tambah Pengguna Baru</span>
                            </h3>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btnCloseUserModal" aria-label="Tutup modal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="formAddUser" class="flex flex-col gap-4 m-0">
                        <input type="hidden" id="userDbId" value="">

                        <div id="userFormAlert" style="display:none;" class="text-xs font-bold px-3.5 py-2.5 rounded-xl"></div>

                        <div>
                            <label for="userNama" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" id="userNama" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Contoh: Budi Santoso" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="userId" class="block text-xs font-bold text-slate-700 mb-1.5">NIM / NIP / ID <span class="text-rose-500">*</span></label>
                                <input type="text" id="userId" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Contoh: 10241014" required>
                            </div>
                            <div>
                                <label for="userRole" class="block text-xs font-bold text-slate-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                                <select id="userRole" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer" required>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="userEmail" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                            <input type="email" id="userEmail" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="email@kampus.ac.id">
                        </div>

                        <div>
                            <label for="userPassword" class="block text-xs font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500" id="passwordRequiredMark">*</span></label>
                            <input type="password" id="userPassword" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all" placeholder="Minimal 6 karakter" autocomplete="new-password">
                            <small id="passwordHint" style="display:none;" class="block mt-1 text-[11px] font-semibold text-slate-500">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>

                        <div>
                            <label for="userStatus" class="block text-xs font-bold text-slate-700 mb-1.5">Status Akun</label>
                            <select id="userStatus" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-aktif</option>
                            </select>
                        </div>

                        <div class="flex gap-2.5 mt-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-sm cursor-pointer transition-all" id="btnSubmitUser">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Simpan Pengguna</span>
                            </button>
                            <button type="button" id="userCancelEditBtn" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all cursor-pointer">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

    <div id="penggunaRoutes" style="display:none;" data-store-url="{{ route('admin.pengguna.store') }}" data-update-url-template="{{ route('admin.pengguna.update', ['user' => '__ID__']) }}" data-delete-url-template="{{ route('admin.pengguna.destroy', ['user' => '__ID__']) }}"></div>
</body>
</html>
