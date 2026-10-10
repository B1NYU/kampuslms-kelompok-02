{{-- Checklist aturan kata sandi (live). Hanya bantuan tampilan; validasi sebenarnya di server. --}}
<ul id="aturanSandi" style="list-style: none; margin: 10px 0 0; padding: 0; display: grid; gap: 4px; font-size: 12.5px; font-weight: 700;">
    <li data-rule="panjang" style="color: #94A3B8;">○ Minimal 8 karakter</li>
    <li data-rule="huruf"   style="color: #94A3B8;">○ Mengandung huruf</li>
    <li data-rule="angka"   style="color: #94A3B8;">○ Mengandung angka</li>
    <li data-rule="cocok"   style="color: #94A3B8;">○ Konfirmasi kata sandi cocok</li>
</ul>

<script>
    (function () {
        const baru = document.getElementById('password');
        const ulang = document.getElementById('password_confirmation');
        const daftar = document.getElementById('aturanSandi');
        if (!baru || !ulang || !daftar) return;

        function periksa() {
            const v = baru.value;
            const hasil = {
                panjang: v.length >= 8,
                huruf: /[A-Za-z]/.test(v),
                angka: /[0-9]/.test(v),
                cocok: v.length > 0 && v === ulang.value,
            };
            daftar.querySelectorAll('li').forEach(li => {
                const ok = hasil[li.dataset.rule];
                li.style.color = ok ? '#1B8A5A' : '#94A3B8';
                li.textContent = (ok ? '✓ ' : '○ ') + li.textContent.slice(2);
            });
        }
        baru.addEventListener('input', periksa);
        ulang.addEventListener('input', periksa);

        // Tombol lihat/sembunyikan: <button data-toggle-sandi="idInput">
        document.querySelectorAll('[data-toggle-sandi]').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.toggleSandi);
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });
    })();
</script>
