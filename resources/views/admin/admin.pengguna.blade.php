<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <!-- Form Tambah Pengguna -->
                        <form id="formAddUser" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                Tambah / Edit Pengguna
                            </h4>

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
                                <label for="userProdi">Program Studi / Departemen</label>
                                <select id="userProdi" class="form-select">
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi</option>
                                    <option value="Informatika">Informatika</option>
                                    <option value="Administrasi">Administrasi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="userStatus">Status Akun</label>
                                <select id="userStatus" class="form-select">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Non-aktif</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-primary-action" id="btnSubmitUser">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Simpan Pengguna
                            </button>
                        </form>

                        <!-- Tabel Daftar Pengguna -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Total <strong id="userTableCount">8</strong> Pengguna Terdaftar</span>
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
                                            <th>Prodi</th>
                                            <th>Status</th>
                                            <th style="text-align:right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userTableBody">
                                        @php
                                            $users = [
                                                ['inisial'=>'AD','nama'=>'Super Admin','id'=>'ADMIN-001','role'=>'admin','prodi'=>'Administrasi','status'=>'aktif','bg'=>'#FFE2E8','c'=>'#B0182D'],
                                                ['inisial'=>'BS','nama'=>'Dr. Budi Santoso, M.Kom','id'=>'NIP-001','role'=>'dosen','prodi'=>'Teknologi Informasi','status'=>'aktif','bg'=>'#FFF0DE','c'=>'#C98A1F'],
                                                ['inisial'=>'RM','nama'=>'Rina Marlina, M.Kom','id'=>'NIP-002','role'=>'dosen','prodi'=>'Sistem Informasi','status'=>'aktif','bg'=>'#FFF0DE','c'=>'#C98A1F'],
                                                ['inisial'=>'BK','nama'=>'Baihaqi Abimanyu','id'=>'10241014','role'=>'mahasiswa','prodi'=>'Sistem Informasi','status'=>'aktif','bg'=>'#ECFDF5','c'=>'#16A34A'],
                                                ['inisial'=>'CA','nama'=>'Calvin Adithya','id'=>'10241016','role'=>'mahasiswa','prodi'=>'Sistem Informasi','status'=>'aktif','bg'=>'#FFF0DE','c'=>'#C98A1F'],
                                                ['inisial'=>'CL','nama'=>'Clara Shinta','id'=>'10241018','role'=>'mahasiswa','prodi'=>'Sistem Informasi','status'=>'aktif','bg'=>'#F2EBF9','c'=>'#8E44AD'],
                                                ['inisial'=>'DE','nama'=>'Desta Arkan','id'=>'10241020','role'=>'mahasiswa','prodi'=>'Sistem Informasi','status'=>'aktif','bg'=>'#EBF9F1','c'=>'#1B8A5A'],
                                                ['inisial'=>'DV','nama'=>'Devina Putri','id'=>'10241022','role'=>'mahasiswa','prodi'=>'Sistem Informasi','status'=>'nonaktif','bg'=>'#F1F5F9','c'=>'#64748B'],
                                            ];
                                        @endphp
                                        @foreach ($users as $u)
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <div class="user-avatar" style="background:{{ $u['bg'] }};color:{{ $u['c'] }};">{{ $u['inisial'] }}</div>
                                                    <div class="user-meta">
                                                        <span class="user-name">{{ $u['nama'] }}</span>
                                                        <span class="user-id">{{ $u['id'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge-role badge-role-{{ $u['role'] }}">{{ ucfirst($u['role']) }}</span></td>
                                            <td style="font-size:12px;color:#64748B;">{{ $u['prodi'] }}</td>
                                            <td><span class="badge-status badge-status-{{ $u['status'] === 'aktif' ? 'active' : 'inactive' }}">{{ ucfirst($u['status']) }}</span></td>
                                            <td>
                                                <div class="btn-actions">
                                                    <button class="btn-icon btn-icon-edit btn-edit-user" title="Edit pengguna">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </button>
                                                    <button class="btn-icon btn-icon-danger btn-delete-user" title="Hapus pengguna">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
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
        // Search filter
        document.getElementById('searchUserInput').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#userTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // Form submit feedback
        document.getElementById('formAddUser').addEventListener('submit', function(e) {
            e.preventDefault();
            const nama = document.getElementById('userNama').value;
            const id   = document.getElementById('userId').value;
            const role = document.getElementById('userRole').value;
            if (!nama || !id) return;

            const roleColors = {
                admin: ['#FFE2E8','#B0182D'],
                dosen: ['#FFF0DE','#C98A1F'],
                mahasiswa: ['#ECFDF5','#16A34A'],
            };
            const [bg, c] = roleColors[role] || ['#F1F5F9','#64748B'];
            const initials = nama.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();

            const tbody = document.getElementById('userTableBody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><div class="user-cell"><div class="user-avatar" style="background:${bg};color:${c};">${initials}</div><div class="user-meta"><span class="user-name">${nama}</span><span class="user-id">${id}</span></div></div></td>
                <td><span class="badge-role badge-role-${role}">${role.charAt(0).toUpperCase()+role.slice(1)}</span></td>
                <td style="font-size:12px;color:#64748B;">—</td>
                <td><span class="badge-status badge-status-active">Aktif</span></td>
                <td><div class="btn-actions">
                    <button class="btn-icon btn-icon-edit btn-edit-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                    <button class="btn-icon btn-icon-danger btn-delete-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>
                </div></td>`;
            tbody.prepend(tr);
            document.getElementById('userTableCount').textContent = tbody.rows.length;
            this.reset();
            bindDeleteButtons();
        });

        function bindDeleteButtons() {
            document.querySelectorAll('.btn-delete-user').forEach(btn => {
                btn.onclick = function() {
                    if (confirm('Hapus pengguna ini?')) {
                        this.closest('tr').remove();
                        const tbody = document.getElementById('userTableBody');
                        document.getElementById('userTableCount').textContent = tbody.rows.length;
                    }
                };
            });
        }
        bindDeleteButtons();
    </script>
</body>
</html>
