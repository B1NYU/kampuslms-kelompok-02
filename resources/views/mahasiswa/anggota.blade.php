<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota Kelompok - Edupath</title>

    <!-- Google Fonts Nunito (Selaras dengan Halaman Utama & Dashboard) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/anggota.css', 'resources/js/app.js'])
    @endif
</head>
<body class="poster-page">
    <!-- Background Decorative Glow yang selaras dengan dashboard & welcome page -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">
        
        <!-- Tombol Back ke Halaman Login (Tanpa Navbar) -->
        <header class="anggota-topbar">
            <a href="{{ url('/') }}" class="btn-back-login" title="Kembali ke Halaman Login">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali ke Halaman Login</span>
            </a>
            <div class="anggota-brand-badge">
                <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
                <span class="anggota-brand-name">KAMPUS LMS</span>
            </div>
        </header>

        <!-- 2. Konten Utama Laman Anggota -->
        <main class="page-wrapper">
            <div class="max-w-7xl mx-auto px-4 w-full">
               
                <div class="poster-header">
                    <div class="poster-badge">
                        <span class="poster-badge-dot"></span>
                        EDUPATH • KELOMPOK 02
                        <span class="poster-badge-dot"></span>
                    </div>
                    <h1 class="poster-title">
                        <span class="poster-title-line"></span>
                        DAFTAR ANGGOTA
                        <span class="poster-title-line"></span>
                    </h1>
                    <p class="poster-subtitle">Tim Pengembang Sistem Manajemen Perkuliahan Kampus</p>
                </div>
                
                <div class="poster-container">
                    @php
                        $anggota = [
                            [
                                'no' => 1,
                                'nama' => '10241014',
                                'panggilan' => 'Baihaqi',
                                'peran' => 'System Analyst',
                                'color' => '#039FFA',
                                'foto' => 'https://ui-avatars.com/api/?name=Baihaqi+Abimanyu&background=039FFA&color=FFFFFF&size=400&bold=true',
                            ],
                            [
                                'no' => 2,
                                'nama' => '10241016',
                                'panggilan' => 'Calvin',
                                'peran' => 'UI/UX Designer',
                                'color' => '#F96305',
                                'foto' => asset('storage/image-css/DSCF8267.jpg'),
                                'fallback_foto' => 'https://ui-avatars.com/api/?name=Calvin+Adhikang&background=F96305&color=FFFFFF&size=400&bold=true',
                            ],
                            [
                                'no' => 3,
                                'nama' => '10241018',
                                'panggilan' => 'Clara',
                                'peran' => 'UI/UX Designer',
                                'color' => '#32B3F1',
                                'foto' => 'https://ui-avatars.com/api/?name=Clara+Uenike&background=32B3F1&color=FFFFFF&size=400&bold=true',
                            ],
                            [
                                'no' => 4,
                                'nama' => '10241020',
                                'panggilan' => 'Desta',
                                'peran' => 'Backend Developer',
                                'color' => '#F9B804',
                                'foto' => 'https://ui-avatars.com/api/?name=Desta+Rifqi&background=F9B804&color=FFFFFF&size=400&bold=true',
                            ],
                            [
                                'no' => 5,
                                'nama' => '10241022',
                                'panggilan' => 'Devina',
                                'peran' => 'Backend Developer',
                                'color' => '#039FFA',
                                'foto' => 'https://ui-avatars.com/api/?name=Devina+Dian&background=039FFA&color=FFFFFF&size=400&bold=true',
                            ],
                        ];
                    @endphp
                    @foreach ($anggota as $item)
                        <div
                            class="member-poster-card"
                            onclick="showDetail('{{ addslashes($item['panggilan']) }}','{{ addslashes($item['nama']) }}', '{{ addslashes($item['peran']) }}', this.querySelector('.poster-img').src, '{{ $item['color'] }}')"
                        >
                            <span class="poster-index">{{ str_pad($item['no'], 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="poster-image-wrapper">
                                <img
                                    src="{{ $item['foto'] }}"
                                    alt="{{ $item['panggilan'] }}"
                                    class="poster-img"
                                    loading="lazy"
                                    @if(isset($item['fallback_foto']))
                                        onerror="this.onerror=null; this.src='{{ $item['fallback_foto'] }}';"
                                    @endif
                                >
                            </div>
                            <div class="poster-gradient-overlay"></div>
                            <div class="poster-content">
                                <div class="poster-text-box">
                                    <span class="poster-role">{{ $item['peran'] }}</span>
                                    <h3 class="poster-name">{{ $item['panggilan'] }}</h3>
                                    <div class="poster-nickname">{{ $item['nama'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>

        <!-- 3. Panggil Komponen Footer -->
        <x-footer />

    </div>

    {{-- MODAL POPUP DETAIL --}}
    <div id="memberModal" class="member-modal-backdrop hidden" onclick="if(event.target === this) closeModal()">
        <div class="modal-card">
            <button type="button" class="modal-close-btn" onclick="closeModal()" aria-label="Tutup modal">&times;</button>
            <div class="modal-avatar-wrapper">
                <img id="modalImg" src="" class="modal-avatar-img" alt="Foto Anggota">
            </div>
            <span id="modalPeran" class="modal-role-badge"></span>
            <h3 id="modalNama" class="modal-name"></h3>
            <p id="modalNim" class="modal-nim"></p>
        </div>
    </div>
    
    <script>
        function showDetail(nama, nim, peran, foto, color) {
            document.getElementById('modalImg').src = foto;
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalNim').textContent = 'NIM: ' + nim;
            const peranEl = document.getElementById('modalPeran');
            peranEl.textContent = peran;
            if (color) {
                peranEl.style.background = color;
            }
            const modal = document.getElementById('memberModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('memberModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>
