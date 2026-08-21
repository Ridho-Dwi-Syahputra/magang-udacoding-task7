<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Waktu update, field yang nggak dikirim harus dibiarkan apa adanya.
        // Makanya PUT dan PATCH pakai "sometimes", bukan "required".
        $wajib = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$wajib, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(Task::STATUS)],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul task wajib diisi.',
            'title.max' => 'Judul task maksimal 255 karakter.',
            'status.in' => 'Status hanya boleh todo, progress, atau done.',
            'due_date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
