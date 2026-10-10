<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa — KampusLMS Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.pendaftaran.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.pendaftaran.css') }}">
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
                        <h1>Pendaftaran Mahasiswa ke Mata Kuliah</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <button type="button" class="btn-primary-action" id="btnOpenEnrollModal" style="padding: 9px 18px; font-size: 13px; width: auto; height: auto;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Daftarkan Mahasiswa</span>
                    </button>
                </div>
            </header>

            {{-- Pesan hasil aksi --}}
            @if (session('success'))
                <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#047857; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; margin-bottom:14px;">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; margin-bottom:14px;">
                    @foreach ($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Fitur: Pendaftaran -->
            <section>
                @php
                    // $courses, $mahasiswaList, $selectedCode dikirim AdminEnrollmentController.
                    $allEnrollments = collect();
                    foreach ($courses as $course) {
                        foreach ($course->students as $student) {
                            $allEnrollments->push(['course' => $course, 'student' => $student]);
                        }
                    }

                    // Hanya MK aktif yang bisa dipilih untuk pendaftaran baru.
                    $activeCourses = $courses->where('status', 'active');

                    // Kriteria 4.4: tiap MK >= 15 mahasiswa.
                    $kurang15 = $courses->filter(fn ($c) => $c->students_count < 15)->count();
                @endphp
                <div class="section-card">
                    <!-- Tabel Pendaftaran Aktif (Full Width) -->
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
                        <div class="filter-mk-container">
                            <button type="button" class="btn-filter-mk {{ $selectedCode === 'all' ? 'active' : '' }}" data-mk="all">
                                Semua MK ({{ $allEnrollments->count() }})
                            </button>
                            @foreach ($courses as $c)
                                <button type="button" class="btn-filter-mk {{ $selectedCode === $c->code ? 'active' : '' }}" data-mk="{{ $c->code }}">
                                    {{ $c->code }} ({{ $c->students_count }})
                                </button>
                            @endforeach
                        </div>

                        <div class="table-responsive table-scrollable">
                            <table class="custom-admin-table" id="enrollTable">
                                <thead>
                                    <tr class="table-header-sticky">
                                        <th>Mahasiswa</th>
                                        <th>Mata Kuliah</th>
                                        <th>Tanggal Daftar</th>
                                        <th style="text-align:right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="enrollTableBody">
                                    @forelse ($allEnrollments as $e)
                                    @php
                                        $mhs   = $e['student'];
                                        $mk    = $e['course'];
                                        $inits = collect(explode(' ', $mhs->name))->map(fn ($w) => mb_substr($w, 0, 1))->join('');
                                        $inits = strtoupper(mb_substr($inits, 0, 2));
                                        $tgl   = $mhs->pivot->enrolled_at
                                            ? \Illuminate\Support\Carbon::parse($mhs->pivot->enrolled_at)->translatedFormat('d M Y')
                                            : '-';
                                    @endphp
                                    <tr data-mk="{{ $mk->code }}">
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar avatar-green">
                                                    {{ $inits }}
                                                </div>
                                                <div class="user-meta">
                                                    <span class="user-name">{{ $mhs->name }}</span>
                                                    <span class="user-id">{{ $mhs->nim_nip }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="td-mk-name">
                                            <div>{{ $mk->code }} • {{ $mk->name }}</div>
                                            <small class="td-dosen-name">Dosen: {{ $mk->lecturer?->name ?? 'Belum ada dosen' }}</small>
                                        </td>
                                        <td class="td-semester">{{ $tgl }}</td>
                                        <td>
                                            <div class="btn-actions">
                                                <form method="POST" action="{{ route('admin.pendaftaran.destroy', [$mk, $mhs]) }}" style="margin:0; display:inline;"
                                                      onsubmit="return confirm('Batalkan pendaftaran {{ e($mhs->name) }} dari {{ e($mk->code) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon btn-icon-danger btn-delete-enroll" title="Batalkan pendaftaran">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="td-empty">Belum ada pendaftaran mata kuliah.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM PENDAFTARAN MK -->
        <div class="admin-modal-overlay {{ $errors->any() ? 'active' : '' }}" id="enrollModalOverlay">
            <div class="admin-modal-card">
                <div class="admin-modal-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="section-header-icon" style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(3, 159, 250, 0.1); color: #039FFA;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Daftarkan Mahasiswa</h3>
                            <span style="font-size: 11px; color: #64748B; font-weight: 600;">Pilih mahasiswa dan mata kuliah aktif</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" id="btnCloseEnrollModal" aria-label="Tutup modal">&times;</button>
                </div>
                <div class="admin-modal-body">
                    @if ($errors->any())
                        <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:10px; padding:10px 14px; font-size:12.5px; font-weight:700; margin-bottom:14px;">
                            @foreach ($errors->all() as $error)
                                <div>⚠️ {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form id="formEnroll" method="POST" action="{{ route('admin.pendaftaran.store') }}" style="display: flex; flex-direction: column; gap: 14px; margin: 0;">
                        @csrf
                        <div class="form-group">
                            <label for="enrollMahasiswa">Pilih Mahasiswa <span class="required">*</span></label>
                            <select id="enrollMahasiswa" name="student_id" class="form-select" required>
                                <option value="">— Pilih Mahasiswa ({{ $mahasiswaList->count() }} Terdaftar) —</option>
                                @foreach ($mahasiswaList as $mhs)
                                    <option value="{{ $mhs->id }}" @selected((int) old('student_id') === $mhs->id)>{{ $mhs->name }} ({{ $mhs->nim_nip }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="enrollMatkul">Pilih Mata Kuliah <span class="required">*</span></label>
                            <select id="enrollMatkul" name="course_id" class="form-select" required>
                                <option value="">— Pilih Mata Kuliah —</option>
                                @foreach ($activeCourses as $c)
                                    <option value="{{ $c->id }}" @selected((int) old('course_id') === $c->id)>{{ $c->code }} • {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students_count }} Mhs)</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:8px;">
                            <button type="submit" class="btn-primary-action" style="flex:1;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Daftarkan ke Mata Kuliah</span>
                            </button>
                            <button type="button" id="btnCancelEnrollModal" class="btn-icon" style="padding:0 18px; width:auto; border-radius:12px; background:#FFFFFF; border:1.5px solid #CBD5E1; color:var(--admin-muted); font-weight:800; font-size:13px; height:42px;">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

    <script>
        // Modal Daftarkan Mahasiswa
        const enrollModal = document.getElementById('enrollModalOverlay');
        const btnOpenEnroll = document.getElementById('btnOpenEnrollModal');
        const btnCloseEnrollModal = document.getElementById('btnCloseEnrollModal');
        const btnCancelEnrollModal = document.getElementById('btnCancelEnrollModal');

        function openEnrollModal() {
            if (enrollModal) enrollModal.classList.add('active');
        }

        function closeEnrollModal() {
            if (enrollModal) enrollModal.classList.remove('active');
        }

        if (btnOpenEnroll) btnOpenEnroll.addEventListener('click', openEnrollModal);
        if (btnCloseEnrollModal) btnCloseEnrollModal.addEventListener('click', closeEnrollModal);
        if (btnCancelEnrollModal) btnCancelEnrollModal.addEventListener('click', closeEnrollModal);

        if (enrollModal) {
            enrollModal.addEventListener('click', (e) => {
                if (e.target === enrollModal) closeEnrollModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && enrollModal && enrollModal.classList.contains('active')) {
                closeEnrollModal();
            }
        });

        // Filter MK awal berasal dari server (?mk=KODE), default 'all'
        let currentMkFilter = @json($selectedCode);

        function applyEnrollFilters() {
            const q = (document.getElementById('searchEnrollInput')?.value || '').toLowerCase().trim();
            let visible = 0;

            document.querySelectorAll('#enrollTableBody tr[data-mk]').forEach(row => {
                const matchMk = (currentMkFilter === 'all' || row.dataset.mk === currentMkFilter);
                const matchSearch = !q || row.textContent.toLowerCase().includes(q);
                const show = matchMk && matchSearch;

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('enrollCount').textContent = visible;
        }

        document.querySelectorAll('.btn-filter-mk').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.btn-filter-mk').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                currentMkFilter = this.dataset.mk;
                applyEnrollFilters();
            });
        });

        document.getElementById('searchEnrollInput').addEventListener('input', applyEnrollFilters);

        // Terapkan filter awal saat halaman dimuat
        applyEnrollFilters();
    </script>
</body>
</html>
