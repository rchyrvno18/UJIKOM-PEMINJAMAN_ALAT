<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengembalianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda' => ['nullable', 'internger', 'min:0'],
        ];
    }
    public function attributes(): array
    {
        return [
            'kondisi_kembali' => 'Kondisi barang kembali',
            'denda' => 'Nilai denda',
        ];
    }
}
