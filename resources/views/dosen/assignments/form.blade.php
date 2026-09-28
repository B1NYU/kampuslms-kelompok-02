@extends('layouts.plain')
@section('title', $assignment->exists ? 'Ubah Tugas' : 'Tugas Baru')
@section('content')
    <h1>{{ $assignment->exists ? 'Ubah Tugas' : 'Tugas Baru' }} &mdash; {{ $course->code }}</h1>
    <form method="POST"
          action="{{ $assignment->exists ? route('dosen.assignments.update', $assignment) : route('dosen.courses.assignments.store', $course) }}">
        @csrf
        @if($assignment->exists) @method('PUT') @endif
        <label>Judul<input name="title" value="{{ old('title', $assignment->title) }}" required></label>
        <label>Instruksi<textarea name="instructions" rows="5" required>{{ old('instructions', $assignment->instructions) }}</textarea></label>
        <label>Batas waktu<input type="datetime-local" name="due_at" value="{{ old('due_at', $assignment->due_at?->format('Y-m-d\TH:i')) }}" required></label>
        <label>Nilai maksimal<input type="number" name="max_score" min="1" max="100" value="{{ old('max_score', $assignment->max_score ?? 100) }}" required></label>
        <label><input type="checkbox" name="allow_late" value="1" @checked(old('allow_late', $assignment->allow_late ?? true))> Boleh terlambat</label>
        <label>Status
            <select name="status">
                @foreach(['draft', 'published'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $assignment->status ?? 'draft') === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </label>
        <p><button type="submit">Simpan</button></p>
    </form>
@endsection
