<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif

    <style>
        .notif-page-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px; }
        .notif-page-item form { margin: 0; }
        .notif-page-row { width: 100%; display: flex; align-items: flex-start; gap: 14px; text-align: left; font-family: inherit; cursor: pointer;
            background: #FFFFFF; border: 1px solid rgba(3, 159, 250, 0.18); border-radius: 14px; padding: 14px 16px; color: #0F172A; }
        .notif-page-row:hover { background: #F0F9FF; }
        .notif-page-row[disabled] { cursor: default; }
        .notif-page-item.is-unread .notif-page-row { border-left: 4px solid #039FFA; background: #F8FCFF; }
        .notif-dot { flex-shrink: 0; width: 10px; height: 10px; border-radius: 50%; margin-top: 5px; background: transparent; }
        .is-unread .notif-dot { background: #039FFA; }
        .notif-page-msg { display: block; font-size: 14px; font-weight: 800; line-height: 1.4; }
        .is-read .notif-page-msg { font-weight: 700; color: #334155; }
        .notif-page-meta { display: block; margin-top: 3px; font-size: 12px; font-weight: 600; color: #64748B; }
        .notif-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .notif-tabs a.is-active { background: #039FFA; color: #FFFFFF; border-color: #039FFA; }
        .notif-pager { display: flex; justify-content: space-between; align-items: center; margin-top: 18px; font-size: 12.5px; font-weight: 700; color: #64748B; }
        .notif-pager .is-off { opacity: .4; pointer-events: none; }
        .notif-empty-page { padding: 34px 16px; text-align: center; font-size: 13.5px; font-weight: 700; color: #64748B; }
    </style>
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Dinamis Sesuai Peran -->
        @if(auth()->user()->role === 'dosen')
            <x-navbar-dosen />
        @elseif(auth()->user()->role === 'admin')
            <x-navbar-admin />
        @else
            <x-layout />
        @endif

        <main class="dosen-content">

            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            KOTAK MASUK
                        </span>
                        <h1>Notifikasi</h1>
                        <p style="font-size: 13px; font-weight: 700; color: #64748B; margin-top: 4px;">
                            {{ $unreadCount > 0 ? $unreadCount . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}
                        </p>
                    </div>
                </div>

                <div class="topbar-actions">
                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifikasi.baca-semua') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-secondary-action">Tandai semua sudah dibaca</button>
                        </form>
                    @endif
                </div>
            </header>

            @if (session('success'))
                <div style="background:#d1fae5; color:#065f46; padding:12px 20px; border-radius:8px; margin-bottom:16px; border-left:4px solid #10b981; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif

            <section class="feature-section">
                <div class="section-card">

                    <div class="table-header-tools" style="margin-bottom: 16px;">
                        <div class="notif-tabs">
                            <a href="{{ route('notifikasi.index') }}" class="btn-secondary-action {{ $filter === 'semua' ? 'is-active' : '' }}">Semua</a>
                            <a href="{{ route('notifikasi.index', ['filter' => 'belum']) }}" class="btn-secondary-action {{ $filter === 'belum' ? 'is-active' : '' }}">
                                Belum dibaca{{ $unreadCount > 0 ? ' (' . $unreadCount . ')' : '' }}
                            </a>
                        </div>
                    </div>

                    @if ($notifications->isEmpty())
                        <div class="notif-empty-page">
                            {{ $filter === 'belum' ? 'Tidak ada notifikasi yang belum dibaca.' : 'Belum ada notifikasi.' }}
                        </div>
                    @else
                        <ul class="notif-page-list">
                            @foreach ($notifications as $n)
                                <li class="notif-page-item {{ $n['dibaca'] ? 'is-read' : 'is-unread' }}">
                                    {{-- Satu POST per baris: menandai dibaca lalu mengarahkan ke tujuan. --}}
                                    <form method="POST" action="{{ route('notifikasi.baca', ['id' => $n['id']]) }}">
                                        @csrf
                                        <button type="submit" class="notif-page-row">
                                            <span class="notif-dot" aria-hidden="true"></span>
                                            <span>
                                                <span class="notif-page-msg">{{ $n['message'] }}</span>
                                                <span class="notif-page-meta">
                                                    {{ $n['waktu'] }} &middot; {{ $n['time'] }}
                                                    @if ($n['course']) &middot; {{ $n['course'] }} @endif
                                                    @unless ($n['has_url']) &middot; tanpa tautan @endunless
                                                </span>
                                            </span>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        <div class="notif-pager">
                            <a href="{{ $notifications->previousPageUrl() ?? '#' }}" class="btn-secondary-action {{ $notifications->onFirstPage() ? 'is-off' : '' }}">&larr; Sebelumnya</a>
                            <span>Halaman {{ $notifications->currentPage() }} dari {{ $notifications->lastPage() }}</span>
                            <a href="{{ $notifications->nextPageUrl() ?? '#' }}" class="btn-secondary-action {{ $notifications->hasMorePages() ? '' : 'is-off' }}">Berikutnya &rarr;</a>
                        </div>
                    @endif

                </div>
            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
