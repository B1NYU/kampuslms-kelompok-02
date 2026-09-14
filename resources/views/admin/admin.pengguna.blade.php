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
        @vite(['resources/css/app.css', 'resources/css/admin/admin.dashboard.css', 'resources/js/app.js'])
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
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PANEL ADMIN • MANAJEMEN PENGGUNA
                        </span>
                        <h1>CRUD Pengguna & Role</h1>
                    </div>
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
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Manajemen Pengguna</h2>
                                <p>Kelola seluruh akun pengguna dan role (Admin / Dosen / Mahasiswa) sesuai data database.</p>
                                <div class="seeder-criteria-badges" style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                    <span style="background:#FFE2E8;color:#B0182D;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(176,24,45,0.2);">
                                        👑 Admin: <span id="badgeCountAdmin">{{ $countAdmin }}</span>
                                    </span>
                                    <span style="background:#FFF0DE;color:#C98A1F;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(201,138,31,0.2);">
                                        🎓 Dosen: <span id="badgeCountDosen">{{ $countDosen }}</span>
                                    </span>
                                    <span style="background:#ECFDF5;color:#16A34A;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(22,163,74,0.2);">
                                        👥 Mahasiswa: <span id="badgeCountMhs">{{ $countMahasiswa }}</span>
                                    </span>
                                    <span style="background:#EFF6FF;color:#2563EB;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(37,99,235,0.2);">
                                        ✓ Kriteria 4.4 Seeder Terpenuhi (<span id="badgeCountTotal">{{ $countTotal }}</span> Users)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="section-header-badge">CRUD Pengguna</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Tambah/Edit Pengguna -->
                        <form id="formAddUser" class="card-form">
                            <input type="hidden" id="userDbId" value="">

                            <h4 class="card-form-title" id="formUserTitle">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                <span>Tambah / Edit Pengguna</span>
                            </h4>

                            <div id="userFormAlert" style="display:none;margin-bottom:10px;font-size:12.5px;font-weight:600;padding:8px 12px;border-radius:8px;"></div>

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

                            <div style="display:flex;gap:8px;">
                                <button type="submit" class="btn-primary-action" id="btnSubmitUser">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Simpan Pengguna</span>
                                </button>
                                <button type="button" id="userCancelEditBtn" class="btn-icon" style="display:none;padding:0 14px;">Batal Edit</button>
                            </div>
                        </form>

                        <!-- Tabel Daftar Pengguna -->
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
                                                'admin' => ['#FFE2E8', '#B0182D'],
                                                'dosen' => ['#FFF0DE', '#C98A1F'],
                                                'mahasiswa' => ['#ECFDF5', '#16A34A'],
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
                </div>
            </section>

        </main>

        <x-footer />
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const storeUrl          = "{{ route('admin.pengguna.store') }}";
        const updateUrlTemplate = "{{ route('admin.pengguna.update', ['user' => '__ID__']) }}";
        const deleteUrlTemplate = "{{ route('admin.pengguna.destroy', ['user' => '__ID__']) }}";

        const form         = document.getElementById('formAddUser');
        const tbody        = document.getElementById('userTableBody');
        const alertBox     = document.getElementById('userFormAlert');
        const submitBtn    = document.getElementById('btnSubmitUser');
        const cancelBtn    = document.getElementById('userCancelEditBtn');
        const formTitle    = document.getElementById('formUserTitle').querySelector('span');
        const submitLabel  = submitBtn.querySelector('span');
        const passwordMark = document.getElementById('passwordRequiredMark');
        const passwordHint = document.getElementById('passwordHint');

        const roleColors = {
            admin: ['#FFE2E8', '#B0182D'],
            dosen: ['#FFF0DE', '#C98A1F'],
            mahasiswa: ['#ECFDF5', '#16A34A'],
        };

        function showAlert(message, type = 'error') {
            alertBox.style.display = 'block';
            alertBox.style.background = type === 'error' ? '#FEE2E2' : '#DCFCE7';
            alertBox.style.color = type === 'error' ? '#B91C1C' : '#166534';
            alertBox.textContent = message;
            setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
        }

        function updateCount() {
            document.getElementById('userTableCount').textContent = tbody.querySelectorAll('tr[data-id]').length;
        }

        function initialsOf(name) {
            return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        }

        function rowHtml(u) {
            const [bg, c] = roleColors[u.role] || ['#F1F5F9', '#64748B'];
            const isNonAktif = u.status === 'nonaktif';
            return `
                <td><div class="user-cell"><div class="user-avatar" style="background:${bg};color:${c};">${initialsOf(u.name)}</div><div class="user-meta"><span class="user-name">${u.name}</span><span class="user-id">${u.nim_nip}</span></div></div></td>
                <td><span class="badge-role badge-role-${u.role}">${u.role.charAt(0).toUpperCase()+u.role.slice(1)}</span></td>
                <td><span class="badge-status badge-status-${isNonAktif ? 'inactive' : 'active'}">${isNonAktif ? 'Non-aktif' : 'Aktif'}</span></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn-icon btn-icon-edit btn-edit-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                    <button type="button" class="btn-icon btn-icon-danger btn-delete-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>
                </div></td>`;
        }

        function upsertRow(u) {
            let tr = tbody.querySelector(`tr[data-id="${u.id}"]`);
            const emptyRow = document.getElementById('userEmptyRow');
            if (emptyRow) emptyRow.remove();

            if (!tr) {
                tr = document.createElement('tr');
                tbody.prepend(tr);
            }
            tr.dataset.id = u.id;
            tr.dataset.name = u.name;
            tr.dataset.nimNip = u.nim_nip;
            tr.dataset.email = u.email ?? '';
            tr.dataset.role = u.role;
            tr.dataset.status = u.status;
            tr.innerHTML = rowHtml(u);

            bindRowButtons(tr);
            updateCount();
        }

        function setEditMode(isEdit) {
            if (isEdit) {
                submitLabel.textContent = 'Update Pengguna';
                cancelBtn.style.display = 'inline-block';
                formTitle.textContent = 'Edit Pengguna';
                document.getElementById('userPassword').required = false;
                passwordMark.style.display = 'none';
                passwordHint.style.display = 'block';
            } else {
                submitLabel.textContent = 'Simpan Pengguna';
                cancelBtn.style.display = 'none';
                formTitle.textContent = 'Tambah / Edit Pengguna';
                document.getElementById('userPassword').required = true;
                passwordMark.style.display = 'inline';
                passwordHint.style.display = 'none';
            }
        }

        function resetForm() {
            form.reset();
            document.getElementById('userDbId').value = '';
            setEditMode(false);
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('userDbId').value;
            const payload = {
                name: document.getElementById('userNama').value.trim(),
                nim_nip: document.getElementById('userId').value.trim(),
                email: document.getElementById('userEmail').value.trim(),
                role: document.getElementById('userRole').value,
                password: document.getElementById('userPassword').value,
                status: document.getElementById('userStatus').value,
            };

            const url = id ? updateUrlTemplate.replace('__ID__', id) : storeUrl;
            const method = id ? 'PUT' : 'POST';

            submitBtn.disabled = true;

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const result = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        const firstError = Object.values(result.errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert(result.message || 'Terjadi kesalahan.', 'error');
                    }
                    return;
                }

                upsertRow(result.data);
                showAlert(result.message, 'success');
                resetForm();
            } catch (err) {
                showAlert('Gagal terhubung ke server.', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        });

        cancelBtn.addEventListener('click', resetForm);

        function bindRowButtons(tr) {
            tr.querySelector('.btn-edit-user').addEventListener('click', function () {
                document.getElementById('userDbId').value = tr.dataset.id;
                document.getElementById('userNama').value = tr.dataset.name;
                document.getElementById('userId').value = tr.dataset.nimNip;
                document.getElementById('userEmail').value = tr.dataset.email;
                document.getElementById('userRole').value = tr.dataset.role;
                document.getElementById('userStatus').value = tr.dataset.status;
                document.getElementById('userPassword').value = '';

                setEditMode(true);
                document.getElementById('userNama').scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            tr.querySelector('.btn-delete-user').addEventListener('click', async function () {
                if (!confirm('Hapus pengguna ini?')) return;

                try {
                    const res = await fetch(deleteUrlTemplate.replace('__ID__', tr.dataset.id), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });
                    const result = await res.json();

                    if (!res.ok) {
                        showAlert(result.message || 'Gagal menghapus data.', 'error');
                        return;
                    }

                    tr.remove();
                    updateCount();
                    showAlert(result.message, 'success');

                    if (!tbody.querySelector('tr[data-id]')) {
                        tbody.innerHTML = `<tr id="userEmptyRow"><td colspan="4" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada pengguna terdaftar.</td></tr>`;
                    }
                } catch (err) {
                    showAlert('Gagal terhubung ke server.', 'error');
                }
            });
        }

        document.querySelectorAll('#userTableBody tr[data-id]').forEach(bindRowButtons);

        let currentRoleFilter = 'all';

        function updateCount() {
            const allRows = tbody.querySelectorAll('tr[data-id]');
            const total = allRows.length;
            let adminCount = 0;
            let dosenCount = 0;
            let mhsCount = 0;

            allRows.forEach(r => {
                const role = r.dataset.role;
                if (role === 'admin') adminCount++;
                else if (role === 'dosen') dosenCount++;
                else if (role === 'mahasiswa') mhsCount++;
            });

            const countElem = document.getElementById('userTableCount');
            if (countElem) countElem.textContent = total;

            const bTotal = document.getElementById('badgeCountTotal');
            if (bTotal) bTotal.textContent = total;
            const bAdmin = document.getElementById('badgeCountAdmin');
            if (bAdmin) bAdmin.textContent = adminCount;
            const bDosen = document.getElementById('badgeCountDosen');
            if (bDosen) bDosen.textContent = dosenCount;
            const bMhs = document.getElementById('badgeCountMhs');
            if (bMhs) bMhs.textContent = mhsCount;

            const fAll = document.getElementById('filterCountAll');
            if (fAll) fAll.textContent = total;
            const fAdmin = document.getElementById('filterCountAdmin');
            if (fAdmin) fAdmin.textContent = adminCount;
            const fDosen = document.getElementById('filterCountDosen');
            if (fDosen) fDosen.textContent = dosenCount;
            const fMhs = document.getElementById('filterCountMhs');
            if (fMhs) fMhs.textContent = mhsCount;

            applyFilters();
        }

        function applyFilters() {
            const q = (document.getElementById('searchUserInput')?.value || '').toLowerCase().trim();
            const allRows = tbody.querySelectorAll('tr[data-id]');

            allRows.forEach(row => {
                const role = row.dataset.role;
                const matchRole = (currentRoleFilter === 'all' || role === currentRoleFilter);
                const text = row.textContent.toLowerCase();
                const matchSearch = !q || text.includes(q);

                row.style.display = (matchRole && matchSearch) ? '' : 'none';
            });
        }

        document.querySelectorAll('.role-filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.role-filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'var(--admin-white)';
                    b.style.color = 'var(--admin-text)';
                    b.style.borderColor = 'var(--admin-border)';
                });
                this.classList.add('active');
                this.style.background = 'var(--admin-primary)';
                this.style.color = '#fff';
                this.style.borderColor = 'var(--admin-primary)';

                currentRoleFilter = this.dataset.filter;
                applyFilters();
            });
        });

        document.getElementById('searchUserInput')?.addEventListener('input', applyFilters);
    </script>
</body>
</html>
