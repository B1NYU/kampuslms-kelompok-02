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
        @vite(['resources/css/app.css', 'resources/css/admin/admin.pengguna.css', 'resources/js/app.js', 'resources/js/admin/admin.pengguna.js'])
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <x-navbar-admin />

        <main class="admin-content">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Manajemen Pengguna</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <button type="button" class="btn-primary-action" id="btnOpenAddUserModal" style="padding: 9px 18px; font-size: 13px; width: auto; height: auto;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
            </header>

            <!-- Fitur: CRUD Pengguna -->
            <section>
                @php
                    $userList = \App\Models\User::withTrashed()
                        ->orderByRaw("CASE WHEN role = 'admin' THEN 1 WHEN role = 'dosen' THEN 2 ELSE 3 END, id ASC")
                        ->get();
                    $countAdmin = $userList->where('role', 'admin')->count();
                    $countDosen = $userList->where('role', 'dosen')->count();
                    $countMahasiswa = $userList->where('role', 'mahasiswa')->count();
                    $countTotal = $userList->count();
                @endphp
                <div class="section-card">
                    <div id="userPageAlert" style="display:none;margin-bottom:14px;font-size:13px;font-weight:700;padding:10px 14px;border-radius:10px;"></div>

                    <!-- Tabel Daftar Pengguna (Full Width) -->
                    <div class="table-container">
                        <div class="table-header-tools">
                            <span class="table-summary-info">Total <strong id="userTableCount">{{ $countTotal }}</strong> Pengguna Terdaftar</span>
                            <div class="search-input-box">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="searchUserInput" placeholder="Cari nama / ID...">
                            </div>
                        </div>

                        <!-- Filter Role Buttons -->
                        <div class="role-filter-group" style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button type="button" class="role-filter-btn active" data-filter="all" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:800;border:1px solid var(--admin-primary);background:var(--admin-primary);color:#fff;cursor:pointer;transition:all 0.2s;">
                                Semua (<span id="filterCountAll">{{ $countTotal }}</span>)
                            </button>
                            <button type="button" class="role-filter-btn" data-filter="admin" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:800;border:1px solid var(--admin-border);background:var(--admin-white);color:var(--admin-text);cursor:pointer;transition:all 0.2s;">
                                👑 Admin (<span id="filterCountAdmin">{{ $countAdmin }}</span>)
                            </button>
                            <button type="button" class="role-filter-btn" data-filter="dosen" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:800;border:1px solid var(--admin-border);background:var(--admin-white);color:var(--admin-text);cursor:pointer;transition:all 0.2s;">
                                🎓 Dosen (<span id="filterCountDosen">{{ $countDosen }}</span>)
                            </button>
                            <button type="button" class="role-filter-btn" data-filter="mahasiswa" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:800;border:1px solid var(--admin-border);background:var(--admin-white);color:var(--admin-text);cursor:pointer;transition:all 0.2s;">
                                👥 Mahasiswa (<span id="filterCountMhs">{{ $countMahasiswa }}</span>)
                            </button>
                        </div>

                        <div class="table-responsive" style="max-height:560px;overflow-y:auto;position:relative;">
                            <table class="custom-admin-table" id="userTable">
                                <thead>
                                    <tr style="position:sticky;top:0;z-index:10;background:var(--admin-light);box-shadow:0 1px 2px rgba(0,0,0,0.06);">
                                        <th>Pengguna</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th style="text-align:right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @forelse ($userList as $u)
                                    @php
                                        $initials = collect(explode(' ', $u->name))->map(fn($w) => mb_substr($w, 0, 1))->join('');
                                        $initials = strtoupper(mb_substr($initials, 0, 2));
                                        $roleColors = [
                                            'admin' => ['rgba(249, 99, 5, 0.12)', '#F96305'],
                                            'dosen' => ['rgba(249, 184, 4, 0.14)', '#D97706'],
                                            'mahasiswa' => ['rgba(3, 159, 250, 0.12)', '#039FFA'],
                                        ];
                                        [$bg, $c] = $roleColors[$u->role] ?? ['#F1F5F9', '#64748B'];
                                        $isNonAktif = $u->trashed();
                                    @endphp
                                    <tr data-id="{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-nim-nip="{{ $u->nim_nip }}"
                                        data-email="{{ $u->email }}"
                                        data-role="{{ $u->role }}"
                                        data-status="{{ $isNonAktif ? 'nonaktif' : 'aktif' }}">
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar" style="background:{{ $bg }};color:{{ $c }};">{{ $initials }}</div>
                                                <div class="user-meta">
                                                    <span class="user-name">{{ $u->name }}</span>
                                                    <span class="user-id">{{ $u->nim_nip }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge-role badge-role-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                                        <td><span class="badge-status badge-status-{{ $isNonAktif ? 'inactive' : 'active' }}">{{ $isNonAktif ? 'Non-aktif' : 'Aktif' }}</span></td>
                                        <td>
                                            <div class="btn-actions">
                                                <button type="button" class="btn-icon btn-icon-edit btn-edit-user" title="Edit pengguna">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </button>
                                                <button type="button" class="btn-icon btn-icon-danger btn-delete-user" title="Hapus pengguna">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="userEmptyRow">
                                        <td colspan="4" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada pengguna terdaftar.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM TAMBAH / EDIT PENGGUNA -->
        <div class="admin-modal-overlay" id="userModalOverlay">
            <div class="admin-modal-card">
                <div class="admin-modal-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="section-header-icon" style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(3, 159, 250, 0.1); color: #039FFA;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 id="formUserTitle" style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display:flex; align-items:center; gap:8px;">
                                <span>Tambah / Edit Pengguna</span>
                            </h3>
                            <span id="formUserSubtitle" style="font-size: 11px; color: #64748B; font-weight: 600;">Kelola akun mahasiswa, dosen, atau admin</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" id="btnCloseUserModal" aria-label="Tutup modal">&times;</button>
                </div>
                <div class="admin-modal-body">
                    <form id="formAddUser" style="display: flex; flex-direction: column; gap: 14px; margin: 0;">
                        <input type="hidden" id="userDbId" value="">

                        <div id="userFormAlert" style="display:none;margin-bottom:6px;font-size:12.5px;font-weight:600;padding:8px 12px;border-radius:8px;"></div>

                        <div class="form-group">
                            <label for="userNama">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="userNama" class="form-control" placeholder="Nama lengkap pengguna..." required>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="userId">NIM / NIP / ID <span class="required">*</span></label>
                                <input type="text" id="userId" class="form-control" placeholder="Contoh: 10241014" required>
                            </div>
                            <div class="form-group">
                                <label for="userRole">Role <span class="required">*</span></label>
                                <select id="userRole" class="form-select" required>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="userEmail">Email</label>
                            <input type="email" id="userEmail" class="form-control" placeholder="email@kampus.ac.id">
                        </div>

                        <div class="form-group">
                            <label for="userPassword">Password <span class="required" id="passwordRequiredMark">*</span></label>
                            <input type="password" id="userPassword" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password">
                            <small id="passwordHint" style="display:none;color:#64748B;">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>

                        <div class="form-group">
                            <label for="userStatus">Status Akun</label>
                            <select id="userStatus" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-aktif</option>
                            </select>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:8px;">
                            <button type="submit" class="btn-primary-action" id="btnSubmitUser" style="flex:1;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Simpan Pengguna</span>
                            </button>
                            <button type="button" id="userCancelEditBtn" class="btn-icon" style="padding:0 18px; width:auto; border-radius:12px; background:#FFFFFF; border:1.5px solid #CBD5E1; color:var(--admin-muted); font-weight:800; font-size:13px; height:42px;">Batal</button>
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
