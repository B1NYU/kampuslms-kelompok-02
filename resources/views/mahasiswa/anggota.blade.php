<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota Kelompok - Edupath</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/anggota.css', 'resources/js/app.js'])
    @endif
</head>
<body class="poster-page">
    <!-- Background Decorative Glow yang selaras dengan dashboard -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">
        
        <!-- 1. Panggil Komponen Navbar -->
        <x-layout />

        <!-- 2. Konten Utama Laman Anggota -->
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px;">
            <div class="max-w-7xl mx-auto px-4 w-full">
               
                <div class="poster-header" style="margin-bottom: 1rem;">
                    <div class="poster-badge">
                        <span class="poster-badge-dot"></span>
                        EDUPATH • KELOMPOK 2
                        <span class="poster-badge-dot"></span>
                    </div>
                    <h1 class="poster-title">
                        <span class="poster-title-line"></span>
                        DAFTAR ANGGOTA
                        <span class="poster-title-line"></span>
                    </h1>
                </div>
                
                <div class="poster-container">
                    @php
                        $anggota = [
                            [
                                'no' => 1,
                                'nama' => '10241014',
                                'panggilan' => 'Baihaqi',
                                'peran' => 'System Analyst',
                                'foto' => 'https://ui-avatars.com/api/?name=Baihaqi+Abimanyu&background=B0183D&color=FCEDD8&size=300',
                            ],
                            [
                                'no' => 2,
                                'nama' => '10241016',
                                'panggilan' => 'Calvin',
                                'peran' => 'UI/UX Designer',
                                'foto' => asset('storage/image-css/DSCF8267.jpg'),
                            ],
                            [
                                'no' => 3,
                                'nama' => '10241018',
                                'panggilan' => 'Clara',
                                'peran' => 'UI/UX Designer',
                                'foto' => 'https://ui-avatars.com/api/?name=Clara+Uenike&background=E23C64&color=FCEDD8&size=300',
                            ],
                            [
                                'no' => 4,
                                'nama' => '10241020',
                                'panggilan' => 'Desta',
                                'peran' => 'Backend Developer',
                                'foto' => 'https://ui-avatars.com/api/?name=Desta+Rifqi&background=FF5E5E&color=FCEDD8&size=300',
                            ],
                            [
                                'no' => 5,
                                'nama' => '10241022',
                                'panggilan' => 'Devina',
                                'peran' => 'Backend Developer',
                                'foto' => 'https://ui-avatars.com/api/?name=Devina+Dian&background=820F28&color=FCEDD8&size=300',
                            ],
                        ];
                    @endphp
                    @foreach ($anggota as $item)
                        <div
                            class="member-poster-card"
                            onclick="showDetail('{{ addslashes($item['panggilan']) }}','{{ addslashes($item['nama']) }}', '{{ addslashes($item['peran']) }}', '{{ $item['foto'] }}')"
                        >
                            <span class="poster-index">{{ str_pad($item['no'], 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="poster-image-wrapper">
                                <img src="{{ $item['foto'] }}" alt="{{ $item['panggilan'] }}" class="poster-img" loading="lazy">
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
        </div>

        <!-- 3. Panggil Komponen Footer -->
        <x-footer />

    </div>

    {{-- MODAL POPUP DETAIL --}}
    <div id="memberModal" class="fixed inset-0 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4 z-50">
        <div class="modal-card bg-[#1a0509] border border-[#FFD464]/50 p-6 rounded-2xl max-w-sm w-full text-center relative shadow-[0_0_50px_rgba(176,24,61,0.5)] text-[#FCEDD8]">
            <div class="w-28 h-36 mx-auto mb-4 rounded-lg overflow-hidden border-2 border-[#FFD464] shadow-md bg-[#2a080f]">
                <img id="modalImg" src="" class="w-full h-full object-cover">
            </div>
            <span id="modalPeran" class="text-xs font-bold text-[#FFD464] bg-[#B0183D] px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-2 border border-[#FFD464]/30"></span>
            <h3 id="modalNama" class="text-2xl font-black uppercase tracking-wide mb-1 text-[#FCEDD8]"></h3>
            <p id="modalNim" class="text-sm font-semibold text-[#FF5E5E] tracking-wider"></p>
        </div>
    </div>
    
    <script>
        function showDetail(nama, nim, peran, foto) {
            document.getElementById('modalImg').src = foto;
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalNim').textContent = nim;
            document.getElementById('modalPeran').textContent = peran;
            const modal = document.getElementById('memberModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('memberModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.getElementById('memberModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
