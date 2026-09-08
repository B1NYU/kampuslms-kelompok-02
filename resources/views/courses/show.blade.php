{{--
    :title (dengan awalan titik dua) dipakai, bukan title="...", karena nilainya
    berasal dari variabel PHP ($mataKuliah['nama']), bukan teks statis.
    Ini bukan {!! !!} — tetap aman karena Blade otomatis meng-escape
    atribut komponen yang di-bind dengan cara ini.
--}}
<x-layout :title="$mataKuliah['nama']">

    <a href="{{ route('mata-kuliah.index') }}" class="text-sm text-blue-600 hover:underline">
        &larr; Kembali ke daftar mata kuliah
    </a>

    <h1 class="text-2xl font-bold mt-4 mb-2">{{ $mataKuliah['nama'] }}</h1>

    <p class="text-sm text-gray-500 mb-6">
        {{ $mataKuliah['kode'] }} &middot; {{ $mataKuliah['sks'] }} SKS
        &middot; Diampu oleh {{ $mataKuliah['dosen'] }}
    </p>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
        <h2 class="font-semibold mb-2">Deskripsi</h2>

        {{-- {{ }} dipakai (bukan {!! !!}) supaya teks deskripsi selalu
             di-escape. Ini penting begitu sumber data berpindah dari array
             statis ke input pengguna/database, untuk mencegah XSS. --}}
        <p class="text-gray-700">{{ $mataKuliah['deskripsi'] }}</p>
    </div>

</x-layout>