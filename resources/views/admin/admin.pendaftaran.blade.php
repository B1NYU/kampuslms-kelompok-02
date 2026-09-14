<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa — KampusLMS Admin</title>
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
                            PANEL ADMIN • PENDAFTARAN MATA KULIAH
                        </span>
                        <h1>Pendaftaran Mahasiswa ke Mata Kuliah</h1>
                    </div>
                </div>
            </header>

            <!-- Fitur: Pendaftaran -->
            <section>
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <polyline points="16 11 18 13 22 9"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Daftarkan Mahasiswa ke Mata Kuliah</h2>
                                <p>Admin dapat mendaftarkan mahasiswa ke satu atau lebih mata kuliah aktif secara langsung.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Kelola Enrollment</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Pendaftaran -->
                        <form id="formEnroll" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                Form Pendaftaran MK
                            </h4>

                            <div class="form-group">
                                <label for="enrollMahasiswa">Pilih Mahasiswa <span class="required">*</span></label>
                                <select id="enrollMahasiswa" class="form-select" required>
                                    <option value="">— Pilih Mahasiswa —</option>
                                    <option value="Baihaqi Abimanyu|10241014">Baihaqi Abimanyu (10241014)</option>
                                    <option value="Calvin Adithya|10241016">Calvin Adithya (10241016)</option>
                                    <option value="Clara Shinta|10241018">Clara Shinta (10241018)</option>
                                    <option value="Desta Arkan|10241020">Desta Arkan (10241020)</option>
                                    <option value="Devina Putri|10241022">Devina Putri (10241022)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="enrollMatkul">Pilih Mata Kuliah <span class="required">*</span></label>
                                <select id="enrollMatkul" class="form-select" required>
                                    <option value="">— Pilih Mata Kuliah —</option>
                                    <option value="IF301 • Pemrograman Web Lanjut">IF301 • Pemrograman Web Lanjut (3 SKS)</option>
                                    <option value="IF302 • Basis Data & Relasional">IF302 • Basis Data &amp; Relasional (3 SKS)</option>
                                    <option value="IF305 • Kecerdasan Buatan (AI)">IF305 • Kecerdasan Buatan (AI) (3 SKS)</option>
                                    <option value="IF310 • Rekayasa Perangkat Lunak">IF310 • Rekayasa Perangkat Lunak (3 SKS)</option>
                                    <option value="IF312 • Jaringan Komputer">IF312 • Jaringan Komputer (2 SKS)</option>
                                    <option value="IF318 • Manajemen Proyek TI">IF318 • Manajemen Proyek TI (2 SKS)</option>
                                </select>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="enrollKelas">Kelas <span class="required">*</span></label>
                                    <select id="enrollKelas" class="form-select" required>
                                        <option value="SI-A">SI-A</option>
                                        <option value="SI-B">SI-B</option>
                                        <option value="TI-A">TI-A</option>
                                        <option value="TI-B">TI-B</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="enrollSemester">Semester</label>
                                    <select id="enrollSemester" class="form-select">
                                        <option value="Genap 2026" selected>Genap 2026</option>
                                        <option value="Ganjil 2026">Ganjil 2026</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Daftarkan ke Mata Kuliah
                            </button>
                        </form>

                        <!-- Tabel Pendaftaran Aktif -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Total <strong id="enrollCount">5</strong> Pendaftaran Aktif</span>
                                <div class="search-input-box">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchEnrollInput" placeholder="Cari mahasiswa / MK...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="custom-admin-table" id="enrollTable">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Mata Kuliah</th>
                                            <th>Kelas</th>
                                            <th>Semester</th>
                                            <th style="text-align:right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="enrollTableBody">
                                        @php
                                            $enrollments = [
                                                ['mhs'=>'Baihaqi Abimanyu','nim'=>'10241014','mk'=>'IF301 • Pemrograman Web Lanjut','kelas'=>'SI-A','smt'=>'Genap 2026','bg'=>'#ECFDF5','c'=>'#16A34A'],
                                                ['mhs'=>'Calvin Adithya','nim'=>'10241016','mk'=>'IF301 • Pemrograman Web Lanjut','kelas'=>'SI-A','smt'=>'Genap 2026','bg'=>'#FFF0DE','c'=>'#C98A1F'],
                                                ['mhs'=>'Clara Shinta','nim'=>'10241018','mk'=>'IF302 • Basis Data & Relasional','kelas'=>'SI-A','smt'=>'Genap 2026','bg'=>'#F2EBF9','c'=>'#8E44AD'],
                                                ['mhs'=>'Desta Arkan','nim'=>'10241020','mk'=>'IF305 • Kecerdasan Buatan','kelas'=>'SI-A','smt'=>'Genap 2026','bg'=>'#EBF9F1','c'=>'#1B8A5A'],
                                                ['mhs'=>'Devina Putri','nim'=>'10241022','mk'=>'IF310 • Rekayasa Perangkat Lunak','kelas'=>'SI-A','smt'=>'Genap 2026','bg'=>'#FFF0DE','c'=>'#C98A1F'],
                                            ];
                                        @endphp
                                        @foreach ($enrollments as $e)
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <div class="user-avatar" style="background:{{ $e['bg'] }};color:{{ $e['c'] }};">
                                                        {{ strtoupper(substr($e['mhs'],0,1).substr(strrchr($e['mhs'],' '),1,1)) }}
                                                    </div>
                                                    <div class="user-meta">
                                                        <span class="user-name">{{ $e['mhs'] }}</span>
                                                        <span class="user-id">{{ $e['nim'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="font-size:12.5px;font-weight:700;">{{ $e['mk'] }}</td>
                                            <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">{{ $e['kelas'] }}</span></td>
                                            <td style="font-size:12px;color:#64748B;">{{ $e['smt'] }}</td>
                                            <td>
                                                <div class="btn-actions">
                                                    <button class="btn-icon btn-icon-danger btn-delete-enroll" title="Batalkan pendaftaran">
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
        document.getElementById('searchEnrollInput').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#enrollTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        document.getElementById('formEnroll').addEventListener('submit', function(e) {
            e.preventDefault();
            const mhsRaw = document.getElementById('enrollMahasiswa').value;
            const mk     = document.getElementById('enrollMatkul').value;
            const kelas  = document.getElementById('enrollKelas').value;
            const smt    = document.getElementById('enrollSemester').value;
            if (!mhsRaw || !mk) return;

            const [mhs, nim] = mhsRaw.split('|');
            const inits = mhs.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();

            const tbody = document.getElementById('enrollTableBody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><div class="user-cell"><div class="user-avatar" style="background:#FFE2E8;color:#B0182D;">${inits}</div><div class="user-meta"><span class="user-name">${mhs}</span><span class="user-id">${nim}</span></div></div></td>
                <td style="font-size:12.5px;font-weight:700;">${mk}</td>
                <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">${kelas}</span></td>
                <td style="font-size:12px;color:#64748B;">${smt}</td>
                <td><div class="btn-actions"><button class="btn-icon btn-icon-danger btn-delete-enroll"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button></div></td>`;
            tbody.prepend(tr);
            document.getElementById('enrollCount').textContent = tbody.rows.length;
            this.reset();
            bindEnrollDeleteButtons();
        });

        function bindEnrollDeleteButtons() {
            document.querySelectorAll('.btn-delete-enroll').forEach(btn => {
                btn.onclick = function() {
                    if (confirm('Batalkan pendaftaran ini?')) {
                        this.closest('tr').remove();
                        document.getElementById('enrollCount').textContent = document.getElementById('enrollTableBody').rows.length;
                    }
                };
            });
        }
        bindEnrollDeleteButtons();
    </script>
</body>
</html>
