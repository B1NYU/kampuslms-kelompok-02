<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tugas — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif

    {{-- Tambahan kecil untuk elemen baru. Boleh dipindah ke dosen.tugas.css. --}}
    <style>
        .field-error { display: block; margin-top: 4px; font-size: 11.5px; font-weight: 700; color: #EF4444; }
        .check-row { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; }
        .check-row input { width: 16px; height: 16px; }
        .card-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; justify-content: flex-end; }
        .card-actions form { margin: 0; }
        .btn-danger-action { font-family: inherit; font-size: 11px; font-weight: 800; padding: 6px 12px; border-radius: 10px;
            border: 1px solid rgba(239, 68, 68, 0.25); background: #FEF2F2; color: #EF4444; cursor: pointer; }
        .assignment-card.is-editing { outline: 2px solid #039FFA; outline-offset: 2px; }
        .empty-state { padding: 28px 16px; text-align: center; font-size: 13px; font-weight: 700; color: #64748B; }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    @php
        $isEdit = $assignment->exists;
        $dueDefault = $assignment->due_at ?? now()->addWeek()->setTime(23, 59);
    @endphp

    <div class="app-window">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Buat Tugas -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar justify-end">

                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse" class="course-select"
                            onchange="if (this.value) window.location.href = this.value">
                        @foreach ($courses as $c)
                            <option value="{{ route('dosen.courses.assignments.index', $c) }}" @selected($c->id === $course->id)>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- Section: Daftar Tugas & Manajemen Deadline -->
            <section class="feature-section" id="buat-tugas">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Daftar Tugas &amp; Manajemen Deadline</h2>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span class="section-header-badge">{{ $assignments->count() }} Tugas Terdaftar</span>
                            <button type="button" class="btn-primary-action" id="btnOpenCreateAssignmentModal" style="padding: 9px 18px; font-size: 13px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Buat Tugas Baru</span>
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Tugas & Status Deadline (Full Width) -->
                    <div class="assignments-list-wrap">
                        <div class="table-header-tools">
                            <span class="table-summary-info">Penugasan Aktif: <strong>{{ $course->code }} &bull; {{ $course->name }}</strong> ({{ $assignments->count() }} Tugas)</span>
                            <span class="card-subtitle-tag" style="background:rgba(249, 99, 5, 0.1); color:#F96305; border-color:rgba(249, 99, 5, 0.25);">Monitoring Deadline</span>
                        </div>

                        <div class="assignments-grid" id="assignmentsGrid">
                            @forelse ($assignments as $a)
                                @php
                                    $pct = $studentCount > 0 ? min(100, round($a->submissions_count / $studentCount * 100)) : 0;
                                    $isPast = $a->due_at->isPast();
                                    $isSoon = ! $isPast && $a->due_at->isBefore(now()->addHours(48));

                                    if ($a->status === 'draft') {
                                        [$badgeClass, $badgeText] = ['badge-status-warning', 'Draft'];
                                    } elseif ($isPast) {
                                        [$badgeClass, $badgeText] = ['badge-status-active', 'Deadline Berakhir'];
                                    } elseif ($isSoon) {
                                        [$badgeClass, $badgeText] = ['badge-status-warning', 'Mendekati Deadline'];
                                    } else {
                                        [$badgeClass, $badgeText] = ['badge-status-active', 'Tugas Aktif'];
                                    }
                                @endphp

                                <div class="assignment-card {{ $assignment->id === $a->id ? 'is-editing' : '' }}">
                                    <div class="assignment-card-header">
                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            @php
                                                preg_match('/(Pertemuan|Minggu)\s*(\d+)/i', $a->title, $matches);
                                                $weekNum = $matches[2] ?? $loop->iteration;
                                            @endphp
                                            <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px; background: rgba(3, 159, 250, 0.1); color: #039FFA; border-color: rgba(3, 159, 250, 0.25);">Pertemuan {{ $weekNum }}</span>
                                            <h4 class="assignment-card-title" style="margin: 0;">{{ $a->title }}</h4>
                                        </div>
                                        <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">Maks. {{ $a->max_score }}</span>
                                    </div>
                                    <p style="font-size: 11.5px; color: #64748B;">{{ \Illuminate\Support\Str::limit($a->instructions, 140) }}</p>

                                    <div class="assignment-deadline-box">
                                        <svg class="deadline-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <div class="deadline-info">
                                            <span class="deadline-label">Batas Waktu Pengumpulan{{ $a->allow_late ? ' (boleh terlambat)' : '' }}</span>
                                            <span class="deadline-value">{{ $a->due_at->translatedFormat('d M Y') }} &middot; {{ $a->due_at->format('H:i') }} WITA</span>
                                        </div>
                                    </div>

                                    <div class="submission-progress-wrap">
                                        <div class="submission-progress-labels">
                                            <span>Progress Pengumpulan</span>
                                            <span><strong>{{ $a->submissions_count }}</strong> dari {{ $studentCount }} Mahasiswa ({{ $pct }}%)</span>
                                        </div>
                                        <div class="submission-progress-track">
                                            <div class="submission-progress-fill" style="width: {{ $pct }}%;{{ $pct === 100 ? ' background: #1B8A5A;' : '' }}"></div>
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                        <span class="badge-status {{ $badgeClass }}">{{ $badgeText }}</span>

                                        <div class="card-actions">
                                            <a href="{{ route('assignments.show', $a) }}" class="btn-secondary-action" style="font-size: 11px;">Lihat Pengumpulan &rarr;</a>
                                            <a href="{{ route('dosen.assignments.edit', $a) }}" class="btn-secondary-action" style="font-size: 11px;">Ubah</a>

                                            @if ($a->submissions_count === 0)
                                                <form action="{{ route('dosen.assignments.destroy', $a) }}" method="POST"
                                                      onsubmit="return confirm('Hapus tugas ini? Tindakan ini tidak dapat dibatalkan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-danger-action">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state" style="padding: 48px 16px; text-align: center; width: 100%; grid-column: 1 / -1;">
                                    <div style="font-size: 36px; margin-bottom: 8px;">📝</div>
                                    <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-bottom: 4px;">Belum Ada Tugas untuk Mata Kuliah Ini</div>
                                    <p style="font-size: 12.5px; color: #64748B; margin-bottom: 16px;">Mata kuliah ini belum memiliki penugasan aktif. Klik tombol di bawah untuk membuat tugas pertama.</p>
                                    <button type="button" class="btn-primary-action" onclick="document.getElementById('btnOpenCreateAssignmentModal').click()" style="margin: 0 auto; font-size: 12.5px;">
                                        + Buat Tugas Pertama
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM BUAT / UBAH TUGAS -->
        <div class="dosen-modal-overlay {{ ($isEdit || $errors->any()) ? 'active' : '' }}" id="assignmentModalOverlay">
            <div class="dosen-modal-card" style="max-width: 620px; width: 95%;">
                <div class="dosen-modal-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="section-header-icon" style="width: 36px; height: 36px; background: rgba(3, 159, 250, 0.1); color: #039FFA;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 id="modalAssignmentTitle">{{ $isEdit ? 'Ubah Penugasan Kuliah' : 'Buat Penugasan Baru' }}</h3>
                            <span style="font-size: 11px; color: #64748B; font-weight: 600;">{{ $course->code }} &bull; {{ $course->name }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" id="btnCloseAssignmentModal" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="formAssignment" method="POST"
                      action="{{ $isEdit ? route('dosen.assignments.update', $assignment) : route('dosen.courses.assignments.store', $course) }}"
                      style="margin: 0; display: flex; flex-direction: column;">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Mata Kuliah Target</label>
                                <input type="text" class="form-control" value="{{ $course->code }} - {{ $course->name }}" disabled style="background: #F1F5F9; color: #64748B;">
                            </div>
                            <div class="form-group">
                                <label for="taskSession">Pertemuan / Minggu Ke- <span class="required">*</span></label>
                                <select id="taskSession" name="session" class="form-select" required>
                                    @for ($w = 1; $w <= 16; $w++)
                                        <option value="{{ $w }}" @selected(old('session') == $w || Str::contains($assignment->title ?? '', ['Pertemuan ' . $w, 'Minggu ' . $w, 'Tugas 0' . $w, 'Tugas ' . $w]))>
                                            Pertemuan {{ $w }} (Minggu ke-{{ $w }})
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="taskTitle">Judul Tugas <span class="required">*</span></label>
                            <input type="text" id="taskTitle" name="title" class="form-control"
                                   value="{{ old('title', $assignment->title) }}"
                                   placeholder="Contoh: Tugas 03 - Implementasi Blade &amp; Controller" required>
                            @error('title') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="taskDesc">Instruksi / Petunjuk Pengerjaan <span class="required">*</span></label>
                            <textarea id="taskDesc" name="instructions" class="form-textarea" rows="4"
                                      placeholder="Jelaskan kebutuhan tugas, format berkas yang dikumpulkan, dan kriteria penilaian..."
                                      required>{{ old('instructions', $assignment->instructions) }}</textarea>
                            @error('instructions') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Input Tanggal & Jam Deadline -->
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="taskDeadlineDate">Batas Tanggal (Deadline) <span class="required">*</span></label>
                                <input type="date" id="taskDeadlineDate" name="due_date" class="form-control" required
                                       value="{{ old('due_date', $dueDefault->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group">
                                <label for="taskDeadlineTime">Batas Jam <span class="required">*</span></label>
                                <input type="time" id="taskDeadlineTime" name="due_time" class="form-control" required
                                       value="{{ old('due_time', $dueDefault->format('H:i')) }}">
                            </div>
                        </div>
                        @error('due_at') <span class="field-error" style="margin-top:-8px; margin-bottom:10px;">{{ $message }}</span> @enderror

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="taskMaxScore">Nilai Maksimal <span class="required">*</span></label>
                                <input type="number" id="taskMaxScore" name="max_score" class="form-control" min="1" max="100" required
                                       value="{{ old('max_score', $assignment->max_score ?? 100) }}">
                                @error('max_score') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="taskStatus">Status Publikasi <span class="required">*</span></label>
                                <select id="taskStatus" name="status" class="form-select" required>
                                    @foreach (['draft' => 'Draft (belum terlihat mahasiswa)', 'published' => 'Terbit (terlihat mahasiswa)'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $assignment->status ?? 'published') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 4px;">
                            <label class="check-row" style="cursor: pointer;">
                                <input type="checkbox" name="allow_late" value="1"
                                       @checked(old('allow_late', $isEdit ? $assignment->allow_late : true))>
                                Izinkan pengumpulan terlambat (setelah deadline)
                            </label>
                        </div>
                    </div>

                    <div class="dosen-modal-footer">
                        @if ($isEdit)
                            <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="btn-secondary-action">
                                Batal Ubah
                            </a>
                        @else
                            <button type="button" class="btn-secondary-action" id="btnCancelAssignmentModal">
                                Batal
                            </button>
                        @endif
                        <button type="submit" class="btn-primary-action">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Publikasikan Tugas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <x-footer />
    </div>

    <!-- Toast Notification Container -->
    <div class="dosen-toast-container" id="toastContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Modal Controls
            const assignmentModal = document.getElementById('assignmentModalOverlay');
            const btnOpenCreate = document.getElementById('btnOpenCreateAssignmentModal');
            const btnCloseModal = document.getElementById('btnCloseAssignmentModal');
            const btnCancelModal = document.getElementById('btnCancelAssignmentModal');

            function openModal() {
                if (assignmentModal) {
                    assignmentModal.classList.add('active');
                }
            }

            function closeModal() {
                if (assignmentModal) {
                    assignmentModal.classList.remove('active');
                    @if ($isEdit)
                        window.location.href = @json(route('dosen.courses.assignments.index', $course));
                    @endif
                }
            }

            if (btnOpenCreate) {
                btnOpenCreate.addEventListener('click', openModal);
            }
            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', closeModal);
            }
            if (btnCancelModal) {
                btnCancelModal.addEventListener('click', closeModal);
            }
            if (assignmentModal) {
                assignmentModal.addEventListener('click', (e) => {
                    if (e.target === assignmentModal) closeModal();
                });
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && assignmentModal && assignmentModal.classList.contains('active')) {
                    closeModal();
                }
            });

            function showToast(message, isSuccess = true) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = 'dosen-toast';
                if (!isSuccess) toast.style.borderLeftColor = '#EF4444';

                const icon = `
                    <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#10B981' : '#EF4444'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>`;
                toast.innerHTML = icon;

                // textContent, bukan innerHTML, supaya pesan tidak bisa menyisipkan HTML.
                const text = document.createElement('span');
                text.textContent = message;
                toast.appendChild(text);

                container.appendChild(toast);
                setTimeout(() => toast.classList.add('show'), 50);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 350);
                }, 3500);
            }

            @if (session('success'))
                showToast(@json(session('success')), true);
            @endif
            @if (session('error'))
                showToast(@json(session('error')), false);
            @endif
        });
    </script>
</body>

</html>
