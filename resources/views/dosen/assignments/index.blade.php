@extends('layouts.plain')
@section('title', 'Tugas ' . $course->code)
@section('content')
    <h1>Tugas {{ $course->code }} &mdash; {{ $course->name }}</h1>
    <p><a class="btn" href="{{ route('dosen.courses.assignments.create', $course) }}">+ Tugas baru</a></p>
    <table>
        <tr><th>Judul</th><th>Batas</th><th>Status</th><th>Pengumpulan</th><th></th></tr>
        @forelse($assignments as $a)
            <tr>
                <td><a href="{{ route('assignments.show', $a) }}">{{ $a->title }}</a></td>
                <td>{{ $a->due_at->format('d M Y H:i') }}</td>
                <td>{{ $a->status }}</td>
                <td>{{ $a->submissions_count }}</td>
                <td>
                    <a href="{{ route('dosen.assignments.edit', $a) }}">Ubah</a>
                    <form action="{{ route('dosen.assignments.destroy', $a) }}" method="POST" style="display:inline"
                            onsubmit="return confirm('Hapus tugas ini?')">
                        @csrf @method('DELETE')<button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada tugas.</td></tr>
        @endforelse
    </table>
@endsection
