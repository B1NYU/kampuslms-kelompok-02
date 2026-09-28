@extends('layouts.plain')
@section('title', $assignment->title)
@section('content')
    <h1>{{ $assignment->title }}</h1>
    <p><strong>{{ $assignment->course->code }}</strong> &middot; {{ $assignment->course->name }}</p>
    <p>Batas: {{ $assignment->due_at->format('d M Y H:i') }} &middot; Nilai maks: {{ $assignment->max_score }}</p>
    <div>{!! nl2br(e($assignment->instructions)) !!}</div>

    @if(auth()->user()->role === 'mahasiswa')
        <h2>Pengumpulan Saya</h2>
        @if($mySubmission)
            <p>Sudah dikumpulkan: {{ $mySubmission->original_name }}
                (<a href="{{ route('submissions.show', $mySubmission) }}">detail</a>)</p>
        @endif
        <form action="{{ route('assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Berkas (pdf, doc, docx, zip, txt; maks 10 MB)<input type="file" name="file" required></label>
            <label>Catatan<textarea name="note" rows="3">{{ old('note') }}</textarea></label>
            <p><button type="submit">{{ $mySubmission ? 'Kumpulkan Ulang' : 'Kumpulkan' }}</button></p>
        </form>
    @else
        <h2>Pengumpulan Mahasiswa ({{ $submissions->count() }})</h2>
        <table>
            <tr><th>Mahasiswa</th><th>Berkas</th><th>Nilai</th><th></th></tr>
            @forelse($submissions as $s)
                <tr>
                    <td>{{ $s->student->name }}</td>
                    <td>{{ $s->original_name }}</td>
                    <td>{{ $s->grade?->score ?? '-' }}</td>
                    <td><a href="{{ route('submissions.show', $s) }}">Lihat</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada pengumpulan.</td></tr>
            @endforelse
        </table>
    @endif
@endsection
