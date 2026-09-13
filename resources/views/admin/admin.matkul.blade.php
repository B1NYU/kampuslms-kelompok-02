<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah — KampusLMS Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.dashboard.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <div class="app-window">

        <x-navbar-admin />

        <main class="admin-content">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PANEL ADMIN • MANAJEMEN MATA KULIAH
                        </span>
                        <h1>CRUD Mata Kuliah</h1>
                    </div>
                </div>
            </header>

            <!-- Fitur: CRUD Mata Kuliah -->
            <section>
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Manajemen Mata Kuliah</h2>
                                <p>Tambah, ubah, dan hapus mata kuliah serta tetapkan dosen pengampu.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">CRUD Mata Kuliah</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Tambah/Edit Mata Kuliah -->
                        <form id="formAddMatkul" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Form Tambah / Edit MK
                            </h4>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="mkKode">Kode MK <span class="required">*</span></label>
                                    <input type="text" id="mkKode" class="form-control" placeholder="Contoh: SI101" required>
                                </div>
                                <div class="form-group">
                                    <label for="mkSks">Jumlah SKS <span class="required">*</span></label>
                                    <select id="mkSks" class="form-select" required>
                                        <option value="2">2 SKS</option>
                                        <option value="3" selected>3 SKS</option>
                                        <option value="4">4 SKS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mkNama">Nama Mata Kuliah <span class="required">*</span></label>
                                <input type="text" id="mkNama" class="form-control" placeholder="Nama lengkap mata kuliah..." required>
                            </div>

                            <div class="form-group">
                                <label for="mkDosen">Dosen Pengampu</label>
                                <select id="mkDosen" class="form-select">
                                    <option value="">— Pilih Dosen —</option>
                                    <option value="Dr. Budi Santoso, M.Kom">Dr. Budi Santoso, M.Kom</option>
                                    <option value="Rina Marlina, M.Kom">Rina Marlina, M.Kom</option>
                                    <option value="Dr. Ahmad Fauzan">Dr. Ahmad Fauzan</option>
                                    <option value="Dr. Yusuf Pratama">Dr. Yusuf Pratama</option>
                                    <option value="Siti Nurhaliza, M.T">Siti Nurhaliza, M.T</option>
                                </select>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="mkSemester">Semester</label>
                                    <select id="mkSemester" class="form-select">
                                        <option value="Genap 2026" selected>Genap 2026</option>
                                        <option value="Ganjil 2026">Ganjil 2026</option>
                                        <option value="Genap 2025">Genap 2025</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="mkStatus">Status</label>
                                    <select id="mkStatus" class="form-select">
                                        <option value="Aktif">Aktif</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mkDeskripsi">Deskripsi Singkat</label>
                                <textarea id="mkDeskripsi" class="form-textarea" rows="2" placeholder="Gambaran singkat mata kuliah ini..."></textarea>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Simpan Mata Kuliah
                            </button>
                        </form>

                        <!-- Tabel Mata Kuliah -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Total <strong id="mkTableCount">6</strong> Mata Kuliah</span>
                                <div class="search-input-box">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchMkInput" placeholder="Cari kode / nama MK...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="custom-admin-table" id="mkTable">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Dosen</th>
                                            <th>Status</th>
                                            <th style="text-align:right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mkTableBody">
                                        @php
                                            $matkulList = [
                                                ['kode'=>'IF301','nama'=>'Pemrograman Web Lanjut','sks'=>3,'dosen'=>'Dr. Ahmad Fauzan','status'=>'Aktif'],
                                                ['kode'=>'IF302','nama'=>'Basis Data & Relasional','sks'=>3,'dosen'=>'Rina Marlina, M.Kom','status'=>'Aktif'],
                                                ['kode'=>'IF305','nama'=>'Kecerdasan Buatan (AI)','sks'=>3,'dosen'=>'Dr. Yusuf Pratama','status'=>'Aktif'],
                                                ['kode'=>'IF310','nama'=>'Rekayasa Perangkat Lunak','sks'=>3,'dosen'=>'Siti Nurhaliza, M.T','status'=>'Aktif'],
                                                ['kode'=>'IF312','nama'=>'Jaringan Komputer','sks'=>2,'dosen'=>'Dr. Budi Santoso, M.Kom','status'=>'Tidak Aktif'],
                                                ['kode'=>'IF318','nama'=>'Manajemen Proyek TI','sks'=>2,'dosen'=>'Dr. Lestari Wibowo','status'=>'Aktif'],
                                            ];
                                        @endphp
                                        @foreach ($matkulList as $mk)
                                        <tr>
                                            <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">{{ $mk['kode'] }}</span></td>
                                            <td style="font-weight:700;font-size:13px;">{{ $mk['nama'] }}</td>
                                            <td style="font-size:12px;font-weight:700;color:#B0182D;">{{ $mk['sks'] }} SKS</td>
                                            <td style="font-size:12px;color:#64748B;">{{ $mk['dosen'] }}</td>
                                            <td>
                                                <span class="badge-status {{ $mk['status'] === 'Aktif' ? 'badge-status-active' : 'badge-status-inactive' }}">
                                                    {{ $mk['status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-actions">
                                                    <button class="btn-icon btn-icon-edit btn-edit-mk" title="Edit mata kuliah">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </button>
                                                    <button class="btn-icon btn-icon-danger btn-delete-mk" title="Hapus mata kuliah">
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
        document.getElementById('searchMkInput').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#mkTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        document.getElementById('formAddMatkul').addEventListener('submit', function(e) {
            e.preventDefault();
            const kode  = document.getElementById('mkKode').value.trim();
            const nama  = document.getElementById('mkNama').value.trim();
            const sks   = document.getElementById('mkSks').value;
            const dosen = document.getElementById('mkDosen').value || '—';
            const stat  = document.getElementById('mkStatus').value;
            if (!kode || !nama) return;

            const tbody = document.getElementById('mkTableBody');
            const tr = document.createElement('tr');
            const isAktif = stat === 'Aktif';
            tr.innerHTML = `
                <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">${kode}</span></td>
                <td style="font-weight:700;font-size:13px;">${nama}</td>
                <td style="font-size:12px;font-weight:700;color:#B0182D;">${sks} SKS</td>
                <td style="font-size:12px;color:#64748B;">${dosen}</td>
                <td><span class="badge-status ${isAktif ? 'badge-status-active' : 'badge-status-inactive'}">${stat}</span></td>
                <td><div class="btn-actions">
                    <button class="btn-icon btn-icon-edit btn-edit-mk"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                    <button class="btn-icon btn-icon-danger btn-delete-mk"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>
                </div></td>`;
            tbody.prepend(tr);
            document.getElementById('mkTableCount').textContent = tbody.rows.length;
            this.reset();
            bindMkDeleteButtons();
        });

        function bindMkDeleteButtons() {
            document.querySelectorAll('.btn-delete-mk').forEach(btn => {
                btn.onclick = function() {
                    if (confirm('Hapus mata kuliah ini?')) {
                        this.closest('tr').remove();
                        document.getElementById('mkTableCount').textContent = document.getElementById('mkTableBody').rows.length;
                    }
                };
            });
        }
        bindMkDeleteButtons();
    </script>
</body>
</html>
