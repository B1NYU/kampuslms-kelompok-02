@php
    // Hitungan awal dari server supaya lencana tidak "berkedip" saat halaman dimuat.
    $jumlahAwal = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<style>
    .notif-wrapper { position: relative; margin-right: 12px; }
    .notif-bell { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 12px; border: 1px solid rgba(3, 159, 250, 0.25); background: #FFFFFF; color: #0369A1; cursor: pointer; transition: background .2s, color .2s; }
    .notif-bell:hover, .notif-bell[aria-expanded="true"] { background: #039FFA; color: #FFFFFF; }
    .notif-bell:focus-visible { outline: 3px solid rgba(3, 159, 250, 0.45); outline-offset: 2px; }
    .notif-badge { position: absolute; top: -6px; right: -6px; min-width: 18px; height: 18px; padding: 0 5px; box-sizing: border-box; border-radius: 9px; background: #EF4444; color: #FFFFFF; font: 800 11px/18px 'Nunito', sans-serif; text-align: center; border: 2px solid #FFFFFF; }
    .notif-badge[hidden], .notif-panel[hidden] { display: none; }
    .notif-panel { position: absolute; right: 0; top: calc(100% + 8px); width: 330px; max-width: 86vw; background: #FFFFFF; border: 1px solid rgba(3, 159, 250, 0.2); border-radius: 14px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); z-index: 1000; overflow: hidden; font-family: 'Nunito', sans-serif; }
    .notif-panel-head { padding: 12px 16px; font-size: 13px; font-weight: 900; color: #0F172A; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; }
    .notif-panel-head span { color: #64748B; font-weight: 700; }
    .notif-list { list-style: none; margin: 0; padding: 0; max-height: 340px; overflow-y: auto; }
    .notif-item a, .notif-item .notif-noLink { display: block; padding: 12px 16px; text-decoration: none; color: #0F172A; border-bottom: 1px solid #F1F5F9; }
    .notif-item a:hover { background: #F0F9FF; }
    .notif-msg { display: block; font-size: 13px; font-weight: 700; line-height: 1.4; }
    .notif-time { display: block; margin-top: 3px; font-size: 11.5px; font-weight: 600; color: #64748B; }
    .notif-footer { display: block; padding: 11px 16px; text-align: center; font-size: 12.5px; font-weight: 800; color: #0369A1; text-decoration: none; background: #F8FAFC; border-top: 1px solid #E2E8F0; }
    .notif-footer:hover { background: #F0F9FF; }
    .notif-item a { cursor: pointer; }
    .notif-empty { padding: 22px 16px; text-align: center; font-size: 13px; font-weight: 600; color: #64748B; }
</style>

<div class="notif-wrapper" id="notifWrapper"
     data-url="{{ route('notifikasi.ringkas') }}"
     data-baca="{{ route('notifikasi.baca', ['id' => '__ID__']) }}"
     data-csrf="{{ csrf_token() }}">
    <button type="button" class="notif-bell" id="notifBell" aria-haspopup="true" aria-expanded="false"
            aria-controls="notifPanel" aria-label="Notifikasi, {{ $jumlahAwal }} belum dibaca" title="Notifikasi">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="notif-badge" id="notifBadge" @if ($jumlahAwal === 0) hidden @endif>{{ $jumlahAwal > 99 ? '99+' : $jumlahAwal }}</span>
    </button>

    <div class="notif-panel" id="notifPanel" hidden role="region" aria-label="Notifikasi terbaru">
        <div class="notif-panel-head">Notifikasi <span id="notifHeadCount"></span></div>
        <ul class="notif-list" id="notifList">
            <li class="notif-empty">Memuat...</li>
        </ul>
        <a class="notif-footer" href="{{ route('notifikasi.index') }}">Lihat semua notifikasi</a>
    </div>
</div>

<script>
    (function () {
        const wrap = document.getElementById('notifWrapper');
        if (!wrap) return;

        const url = wrap.dataset.url;
        const bacaTemplate = wrap.dataset.baca;
        const csrf = wrap.dataset.csrf;
        const bell = document.getElementById('notifBell');
        const badge = document.getElementById('notifBadge');
        const panel = document.getElementById('notifPanel');
        const list = document.getElementById('notifList');
        const headCount = document.getElementById('notifHeadCount');
        const INTERVAL_MS = 30000;
        let timer = null;
        let berhenti = false;

        // Semua teks dimasukkan lewat textContent (bukan innerHTML): pesan berasal dari data pengguna lain.
        function render(data) {
            const n = Number(data.unread_count) || 0;
            badge.textContent = n > 99 ? '99+' : String(n);
            badge.hidden = n === 0;
            bell.setAttribute('aria-label', 'Notifikasi, ' + n + ' belum dibaca');
            headCount.textContent = n > 0 ? n + ' belum dibaca' : '';

            list.replaceChildren();
            if (!data.items || data.items.length === 0) {
                const kosong = document.createElement('li');
                kosong.className = 'notif-empty';
                kosong.textContent = 'Tidak ada notifikasi baru.';
                list.appendChild(kosong);
                return;
            }
            data.items.forEach(function (it) {
                const li = document.createElement('li');
                li.className = 'notif-item';
                const aman = typeof it.url === 'string' && /^\/(?![\/\\])[^\\\x00-\x1F\x7F]*$/.test(it.url);
                const isi = document.createElement(aman ? 'a' : 'div');
                if (aman) {
                    isi.href = it.url;
                    // Klik = tandai dibaca (POST) lalu server mengarahkan ke tujuan. Kalau JS gagal,
                    // href biasa tetap membuka halamannya (hanya belum ditandai dibaca).
                    isi.addEventListener('click', function (e) {
                        if (!it.id || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
                        e.preventDefault();
                        const f = document.createElement('form');
                        f.method = 'POST';
                        f.action = bacaTemplate.replace('__ID__', encodeURIComponent(it.id));
                        const t = document.createElement('input');
                        t.type = 'hidden'; t.name = '_token'; t.value = csrf;
                        f.appendChild(t);
                        document.body.appendChild(f);
                        f.submit();
                    });
                } else { isi.className = 'notif-noLink'; }
                const msg = document.createElement('span');
                msg.className = 'notif-msg';
                msg.textContent = it.message;
                const waktu = document.createElement('span');
                waktu.className = 'notif-time';
                waktu.textContent = it.time;
                isi.append(msg, waktu);
                li.appendChild(isi);
                list.appendChild(li);
            });
        }

        async function muat() {
            if (berhenti || document.hidden) return;
            try {
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                // Sesi habis: berhenti polling, jangan membanjiri server dengan permintaan yang pasti gagal.
                if (res.status === 401 || res.status === 419) { berhenti = true; clearInterval(timer); return; }
                if (!res.ok) return;           // 429/5xx: coba lagi pada putaran berikutnya
                render(await res.json());
            } catch (e) { /* jaringan putus: abaikan, coba lagi nanti */ }
        }

        function tutup() { panel.hidden = true; bell.setAttribute('aria-expanded', 'false'); }

        bell.addEventListener('click', function () {
            const buka = panel.hidden;
            panel.hidden = !buka;
            bell.setAttribute('aria-expanded', String(buka));
            if (buka) muat();
        });
        document.addEventListener('click', function (e) { if (!wrap.contains(e.target)) tutup(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { tutup(); bell.focus(); } });
        document.addEventListener('visibilitychange', function () { if (!document.hidden) muat(); });

        muat();
        timer = setInterval(muat, INTERVAL_MS);   // polling 30 detik; berhenti saat tab disembunyikan
    })();
</script>
