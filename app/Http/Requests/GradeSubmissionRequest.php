<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/** Parameter route harus bernama {submission} (sudah demikian di web.php). */
class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya dosen pengampu, MK active (Q10). Admin dan mahasiswa ditolak.
        Gate::authorize('grade', $this->route('submission'));

        return true;
    }

    public function rules(): array
    {
        return [
            'score'    => ['required', 'numeric', 'min:0', 'max:'.$this->maxScore()],
            'feedback' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required'    => 'Nilai wajib diisi.',
            'score.numeric'     => 'Nilai harus berupa angka.',
            'score.min'         => 'Nilai minimal 0.',
            'score.max'         => 'Nilai maksimal '.$this->maxScore().'.',
            'feedback.required' => 'Feedback wajib diisi.',
            'feedback.max'      => 'Feedback maksimal 2000 karakter.',
        ];
    }

    /** Batas nilai mengikuti max_score tugasnya, bukan angka 100 yang ditulis mati. */
    private function maxScore(): int
    {
        return (int) $this->route('submission')->assignment->max_score;
    }
}
