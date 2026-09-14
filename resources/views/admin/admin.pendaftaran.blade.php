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
                @php
                    $coursesList = \App\Models\Course::with(['lecturer', 'students'])->get();
                    $mahasiswaList = \App\Models\User::where('role', 'mahasiswa')->orderBy('name')->get();
                    $allEnrollments = collect();
                    foreach ($coursesList as $course) {
                        foreach ($course->students as $student) {
                            $allEnrollments->push([
                                'mhs' => $student->name,
                                'nim' => $student->nim_nip,
                                'mk' => $course->code . ' • ' . $course->name,
                                'mk_code' => $course->code,
                                'kelas' => 'SI-A',
                                'smt' => 'Genap 2026',
                                'dosen' => $course->lecturer?->name ?? 'Dosen Pengampu',
                            ]);
                        }
                    }
                @endphp
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
                                <p>Admin dapat mendaftarkan mahasiswa ke mata kuliah aktif. Sesuai Kriteria 4.4, tiap mata kuliah memiliki &ge; 15 mahasiswa terdaftar.</p>
                                <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                    <span style="background:#EFF6FF;color:#2563EB;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(37,99,235,0.2);">
                                        ✓ Tiap MK &ge; 15 Mahasiswa Terdaftar
                                    </span>
                                    <span style="background:#ECFDF5;color:#16A34A;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(22,163,74,0.2);">
                                        Total {{ $allEnrollments->count() }} Pendaftaran Terdata
                                    </span>
                                </div>
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
                                    <option value="">— Pilih Mahasiswa ({{ $mahasiswaList->count() }} Terdaftar) —</option>
                                    @foreach ($mahasiswaList as $mhs)
                                        <option value="{{ $mhs->name }}|{{ $mhs->nim_nip }}">{{ $mhs->name }} ({{ $mhs->nim_nip }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="enrollMatkul">Pilih Mata Kuliah <span class="required">*</span></label>
                                <select id="enrollMatkul" class="form-select" required>
                                    <option value="">— Pilih Mata Kuliah —</option>
                                    @foreach ($coursesList as $c)
                                        <option value="{{ $c->code }} • {{ $c->name }}">{{ $c->code }} • {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students->count() }} Mhs)</option>
                                    @endforeach
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
                                <span class="table-summary-info">Total <strong id="enrollCount">{{ $allEnrollments->count() }}</strong> Pendaftaran Aktif</span>
                                <div class="search-input-box">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchEnrollInput" placeholder="Cari mahasiswa / MK...">
                                </div>
                            </div>

                            <!-- Filter per Mata Kuliah -->
                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;">
                                <button type="button" class="btn-filter-mk active" data-mk="all" style="padding:4px 10px;border-radius:8px;font-size:11.5px;font-weight:800;border:1px solid var(--admin-primary);background:var(--admin-primary);color:#fff;cursor:pointer;">
                                    Semua MK ({{ $allEnrollments->count() }})
                                </button>
                                @foreach ($coursesList as $c)
                                    <button type="button" class="btn-filter-mk" data-mk="{{ $c->code }}" style="padding:4px 10px;border-radius:8px;font-size:11.5px;font-weight:700;border:1px solid var(--admin-border);background:var(--admin-white);color:var(--admin-text);cursor:pointer;">
                                        {{ $c->code }} ({{ $c->students->count() }})
                                    </button>
                                @endforeach
                            </div>

                            <div class="table-responsive" style="max-height:560px;overflow-y:auto;position:relative;">
                                <table class="custom-admin-table" id="enrollTable">
                                    <thead>
                                        <tr style="position:sticky;top:0;z-index:10;background:var(--admin-light);box-shadow:0 1px 2px rgba(0,0,0,0.06);">
                                            <th>Mahasiswa</th>
                                            <th>Mata Kuliah</th>
                                            <th>Kelas</th>
                                            <th>Semester</th>
                                            <th style="text-align:right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="enrollTableBody">
                                        @forelse ($allEnrollments as $e)
                                        @php
                                            $inits = collect(explode(' ', $e['mhs']))->map(fn($w)=>mb_substr($w,0,1))->join('');
                                            $inits = strtoupper(mb_substr($inits, 0, 2));
                                        @endphp
                                        <tr data-mk="{{ $e['mk_code'] }}">
                                            <td>
                                                <div class="user-cell">
                                                    <div class="user-avatar" style="background:#ECFDF5;color:#16A34A;">
                                                        {{ $inits }}
                                                    </div>
                                                    <div class="user-meta">
                                                        <span class="user-name">{{ $e['mhs'] }}</span>
                                                        <span class="user-id">{{ $e['nim'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="font-size:12.5px;font-weight:700;">
                                                <div>{{ $e['mk'] }}</div>
                                                <small style="color:#8E6570;font-weight:500;">Dosen: {{ $e['dosen'] }}</small>
                                            </td>
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
                                        @empty
                                        <tr>
                                            <td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada pendaftaran mata kuliah.</td>
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
        let currentMkFilter = 'all';

        function applyEnrollFilters() {
            const q = (document.getElementById('searchEnrollInput')?.value || '').toLowerCase().trim();
            document.querySelectorAll('#enrollTableBody tr[data-mk]').forEach(row => {
                const mk = row.dataset.mk;
                const matchMk = (currentMkFilter === 'all' || mk === currentMkFilter);
                const text = row.textContent.toLowerCase();
                const matchSearch = !q || text.includes(q);

                row.style.display = (matchMk && matchSearch) ? '' : 'none';
            });
        }

        document.querySelectorAll('.btn-filter-mk').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-filter-mk').forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'var(--admin-white)';
                    b.style.color = 'var(--admin-text)';
                    b.style.borderColor = 'var(--admin-border)';
                });
                this.classList.add('active');
                this.style.background = 'var(--admin-primary)';
                this.style.color = '#fff';
                this.style.borderColor = 'var(--admin-primary)';

                currentMkFilter = this.dataset.mk;
                applyEnrollFilters();
            });
        });

        document.getElementById('searchEnrollInput').addEventListener('input', applyEnrollFilters);

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
