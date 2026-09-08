<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- $title diisi lewat prop dari view pemanggil, mis. <x-layout title="...">.
         Fallback 'LMS Kampus' dipakai kalau prop tidak dikirim, supaya <title>
         tidak pernah kosong. --}}
    <title>{{ $title ?? 'LMS Kampus' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white border-b px-6 py-4">
        {{-- route() dipakai (bukan href="/mata-kuliah" hardcode) supaya
             tautan navigasi tetap valid walau prefix URI berubah nanti. --}}
        <a href="{{ route('mata-kuliah.index') }}" class="font-semibold text-lg">
            LMS Kampus
        </a>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-8">
        {{-- $slot adalah konten di antara <x-layout> ... </x-layout>
             pada view yang memakai komponen ini. --}}
        {{ $slot }}
    </main>

</body>
</html>