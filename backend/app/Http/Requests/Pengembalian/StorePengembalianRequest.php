<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
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
            'peminjaman_id' => ['required', 'intenger',
                Rule::exists('peminjaman', 'id')
            ],
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda' => ['nullable', 'intenger', 'min:0'],
        ];
    }
    public function attributes(): array
    {
        return [
            'peminjaman_id' => 'ID Peminjaman',
            'kondisi_kembali' => 'Kondisi barang kembali',
            'denda' => 'Nilai denda',
        ];
    }
}
