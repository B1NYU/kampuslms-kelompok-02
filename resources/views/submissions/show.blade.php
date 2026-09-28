@extends('layouts.plain')
@section('title', 'Pengumpulan Tugas')
@section('content')
    <h1>{{ $submission->assignment->title }}</h1>
    <p>{{ $submission->assignment->course->code }} &middot; {{ $submission->assignment->course->name }}</p>
    <table>
        <tr><th>Mahasiswa</th><td>{{ $submission->student->name }}</td></tr>
        <tr><th>Berkas</th><td>{{ $submission->original_name }} ({{ number_format($submission->file_size / 1024, 1) }} KB)</td></tr>
        <tr><th>Dikumpulkan</th><td>{{ $submission->submitted_at->format('d M Y H:i') }} {{ $submission->is_late ? '(terlambat)' : '' }}</td></tr>
        <tr><th>Catatan</th><td>{{ $submission->note ?: '-' }}</td></tr>
        <tr><th>Nilai</th><td>{{ $submission->grade?->score ?? 'Belum dinilai' }}</td></tr>
        <tr><th>Umpan balik</th><td>{{ $submission->grade?->feedback ?: '-' }}</td></tr>
    </table>
@endsection
