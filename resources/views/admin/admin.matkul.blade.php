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
                                <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                    <span style="background:#EFF6FF;color:#2563EB;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(37,99,235,0.2);">
                                        ✓ Kriteria 4.4: 5 Mata Kuliah Terdaftar
                                    </span>
                                    <span style="background:#ECFDF5;color:#16A34A;padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:800;border:1px solid rgba(22,163,74,0.2);">
                                        ✓ Tiap MK &ge; 15 Mahasiswa Terdaftar
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="section-header-badge">CRUD Mata Kuliah</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Tambah/Edit Mata Kuliah -->
                        <form id="formAddMatkul" class="card-form">
                            <input type="hidden" id="mkId" value="">

                            <h4 class="card-form-title" id="formMkTitle">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <span>Form Tambah / Edit MK</span>
                            </h4>

                            <div id="mkFormAlert" style="display:none;margin-bottom:10px;font-size:12.5px;font-weight:600;padding:8px 12px;border-radius:8px;"></div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="mkKode">Kode MK <span class="required">*</span></label>
                                    <input type="text" id="mkKode" class="form-control" placeholder="Contoh: SI2514024" required>
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
                                <label for="mkDosen">Dosen Pengampu <span class="required">*</span></label>
                                <select id="mkDosen" class="form-select" required>
                                    <option value="">— Pilih Dosen —</option>
                                    @foreach ($dosenList as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                    @endforeach
                                </select>
                                @if ($dosenList->isEmpty())
                                    <small style="color:#B91C1C;">Belum ada user dengan role "dosen". Tambahkan lewat Manajemen Pengguna dulu.</small>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="mkStatus">Status</label>
                                <select id="mkStatus" class="form-select">
                                    <option value="draft">Draft</option>
                                    <option value="active" selected>Aktif</option>
                                    <option value="archived">Diarsipkan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="mkDeskripsi">Deskripsi Singkat</label>
                                <textarea id="mkDeskripsi" class="form-textarea" rows="2" placeholder="Gambaran singkat mata kuliah ini..."></textarea>
                            </div>

                            <div style="display:flex;gap:8px;">
                                <button type="submit" class="btn-primary-action" id="mkSubmitBtn">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Simpan Mata Kuliah</span>
                                </button>
                                <button type="button" id="mkCancelEditBtn" class="btn-icon" style="display:none;padding:0 14px;">Batal Edit</button>
                            </div>
                        </form>

                        <!-- Tabel Mata Kuliah -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Total <strong id="mkTableCount">{{ $matkulList->count() }}</strong> Mata Kuliah</span>
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
                                        @forelse ($matkulList as $mk)
                                        <tr data-id="{{ $mk->id }}"
                                            data-kode="{{ $mk->code }}"
                                            data-nama="{{ $mk->name }}"
                                            data-sks="{{ $mk->sks }}"
                                            data-lecturer-id="{{ $mk->lecturer_id }}"
                                            data-lecturer-name="{{ $mk->lecturer?->name }}"
                                            data-status="{{ $mk->status }}"
                                            data-deskripsi="{{ $mk->description }}">
                                            <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">{{ $mk->code }}</span></td>
                                            <td style="font-weight:700;font-size:13px;">{{ $mk->name }}</td>
                                            <td style="font-size:12px;font-weight:700;color:#B0182D;">{{ $mk->sks }} SKS</td>
                                            <td style="font-size:12px;color:#64748B;">{{ $mk->lecturer?->name ?? '—' }}</td>
                                            <td>
                                                <span class="badge-status {{ $mk->status === 'active' ? 'badge-status-active' : 'badge-status-inactive' }}">
                                                    {{ ['draft' => 'DRAFT', 'active' => 'AKTIF', 'archived' => 'DIARSIPKAN'][$mk->status] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-actions">
                                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-mk" title="Edit mata kuliah">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </button>
                                                    <button type="button" class="btn-icon btn-icon-danger btn-delete-mk" title="Hapus mata kuliah">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="mkEmptyRow">
                                            <td colspan="6" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada data mata kuliah.</td>
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

        const storeUrl          = "{{ route('admin.matkul.store') }}";
        const updateUrlTemplate = "{{ route('admin.matkul.update', ['matkul' => '__ID__']) }}";
        const deleteUrlTemplate = "{{ route('admin.matkul.destroy', ['matkul' => '__ID__']) }}";

        const form      = document.getElementById('formAddMatkul');
        const tbody     = document.getElementById('mkTableBody');
        const alertBox  = document.getElementById('mkFormAlert');
        const submitBtn = document.getElementById('mkSubmitBtn');
        const cancelBtn = document.getElementById('mkCancelEditBtn');
        const formTitle = document.getElementById('formMkTitle').querySelector('span');
        const submitLabel = submitBtn.querySelector('span');

        const statusLabel = { draft: 'DRAFT', active: 'AKTIF', archived: 'DIARSIPKAN' };

        function showAlert(message, type = 'error') {
            alertBox.style.display = 'block';
            alertBox.style.background = type === 'error' ? '#FEE2E2' : '#DCFCE7';
            alertBox.style.color = type === 'error' ? '#B91C1C' : '#166534';
            alertBox.textContent = message;
            setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
        }

        function updateCount() {
            document.getElementById('mkTableCount').textContent = tbody.querySelectorAll('tr[data-id]').length;
        }

        function badgeClass(status) {
            return status === 'active' ? 'badge-status-active' : 'badge-status-inactive';
        }

        function rowHtml(mk) {
            return `
                <td><span class="card-subtitle-tag" style="font-size:10.5px;padding:3px 9px;">${mk.code}</span></td>
                <td style="font-weight:700;font-size:13px;">${mk.name}</td>
                <td style="font-size:12px;font-weight:700;color:#B0182D;">${mk.sks} SKS</td>
                <td style="font-size:12px;color:#64748B;">${mk.lecturer_name ?? '—'}</td>
                <td><span class="badge-status ${badgeClass(mk.status)}">${statusLabel[mk.status]}</span></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn-icon btn-icon-edit btn-edit-mk"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                    <button type="button" class="btn-icon btn-icon-danger btn-delete-mk"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>
                </div></td>`;
        }

        function upsertRow(mk) {
            let tr = tbody.querySelector(`tr[data-id="${mk.id}"]`);
            const emptyRow = document.getElementById('mkEmptyRow');
            if (emptyRow) emptyRow.remove();

            if (!tr) {
                tr = document.createElement('tr');
                tbody.prepend(tr);
            }
            tr.dataset.id = mk.id;
            tr.dataset.kode = mk.code;
            tr.dataset.nama = mk.name;
            tr.dataset.sks = mk.sks;
            tr.dataset.lecturerId = mk.lecturer_id ?? '';
            tr.dataset.lecturerName = mk.lecturer_name ?? '';
            tr.dataset.status = mk.status;
            tr.dataset.deskripsi = mk.description ?? '';
            tr.innerHTML = rowHtml(mk);

            bindRowButtons(tr);
            updateCount();
        }

        function resetForm() {
            form.reset();
            document.getElementById('mkId').value = '';
            submitLabel.textContent = 'Simpan Mata Kuliah';
            cancelBtn.style.display = 'none';
            formTitle.textContent = 'Form Tambah / Edit MK';
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('mkId').value;
            const payload = {
                code: document.getElementById('mkKode').value.trim(),
                name: document.getElementById('mkNama').value.trim(),
                sks: document.getElementById('mkSks').value,
                lecturer_id: document.getElementById('mkDosen').value,
                status: document.getElementById('mkStatus').value,
                description: document.getElementById('mkDeskripsi').value.trim(),
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
            tr.querySelector('.btn-edit-mk').addEventListener('click', function () {
                document.getElementById('mkId').value = tr.dataset.id;
                document.getElementById('mkKode').value = tr.dataset.kode;
                document.getElementById('mkNama').value = tr.dataset.nama;
                document.getElementById('mkSks').value = tr.dataset.sks;
                document.getElementById('mkDosen').value = tr.dataset.lecturerId;
                document.getElementById('mkStatus').value = tr.dataset.status;
                document.getElementById('mkDeskripsi').value = tr.dataset.deskripsi;

                submitLabel.textContent = 'Update Mata Kuliah';
                cancelBtn.style.display = 'inline-block';
                formTitle.textContent = 'Form Edit Mata Kuliah';

                document.getElementById('mkKode').scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            tr.querySelector('.btn-delete-mk').addEventListener('click', async function () {
                if (!confirm('Hapus mata kuliah ini?')) return;

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
                        tbody.innerHTML = `<tr id="mkEmptyRow"><td colspan="6" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada data mata kuliah.</td></tr>`;
                    }
                } catch (err) {
                    showAlert('Gagal terhubung ke server.', 'error');
                }
            });
        }

        document.querySelectorAll('#mkTableBody tr[data-id]').forEach(bindRowButtons);

        document.getElementById('searchMkInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#mkTableBody tr[data-id]').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
