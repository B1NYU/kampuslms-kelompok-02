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
                                <p>Tambah, ubah, hapus pengguna, dan tetapkan role (Admin / Dosen / Mahasiswa).</p>
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
                                <span class="table-summary-info">Total <strong id="userTableCount">{{ $userList->count() }}</strong> Pengguna Terdaftar</span>
                                <div class="search-input-box">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchUserInput" placeholder="Cari nama / ID...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="custom-admin-table" id="userTable">
                                    <thead>
                                        <tr>
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

        document.getElementById('searchUserInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#userTableBody tr[data-id]').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
