<x-layout title="Daftar Mata Kuliah">
    <h1 class="text-2xl font-bold mb-6">Daftar Mata Kuliah</h1>

    {{-- @forelse (bukan @foreach) dipakai supaya ada tampilan fallback
         bawaan untuk kondisi data kosong, misalnya nanti setelah data
         berasal dari database dan belum ada mata kuliah tersimpan. --}}
    <div class="grid gap-4">
        @forelse ($matakuliah as $mk)
            <div class="bg-white border rounded-lg p-4 shadow-sm flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-lg">{{ $mk['nama'] }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $mk['kode'] }} &middot; {{ $mk['sks'] }} SKS &middot; {{ $mk['dosen'] }}
                    </p>
                </div>

                {{-- route('mata-kuliah.show', $mk['id']) dipakai agar URL detail
                     dibentuk otomatis oleh Laravel, bukan ditulis manual
                     seperti href="/mata-kuliah/{{ $mk['id'] }}". --}}
                <a href="{{ route('mata-kuliah.show', $mk['id']) }}"
                   class="text-blue-600 hover:underline text-sm shrink-0">
                    Lihat detail &rarr;
                </a>
            </div>
        @empty
            <p class="text-gray-500">Belum ada mata kuliah.</p>
        @endforelse
    </div>
</x-layout>