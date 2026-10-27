{{-- Checklist aturan kata sandi (live). Hanya bantuan tampilan; validasi sebenarnya di server. --}}
<ul id="aturanSandi" style="list-style: none; margin: 10px 0 0; padding: 0; display: grid; gap: 4px; font-size: 12.5px; font-weight: 700;">
    <li data-rule="panjang" style="color: #94A3B8;">○ Minimal 8 karakter</li>
    <li data-rule="huruf"   style="color: #94A3B8;">○ Mengandung huruf</li>
    <li data-rule="angka"   style="color: #94A3B8;">○ Mengandung angka</li>
    <li data-rule="cocok"   style="color: #94A3B8;">○ Konfirmasi kata sandi cocok</li>
</ul>

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/js/auth/aturan-sandi.js'])
@endif
