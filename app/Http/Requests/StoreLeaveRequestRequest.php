<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\LeaveRequest::class);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(LeaveRequestType::class)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Jenis pengajuan wajib dipilih.',
            'type.Illuminate\Validation\Rules\Enum' => 'Jenis pengajuan tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'reason.required' => 'Alasan wajib diisi.',
            'reason.max' => 'Alasan terlalu panjang (maksimal 1000 karakter).',
            'attachment.file' => 'Lampiran harus berupa file.',
            'attachment.mimes' => 'Format lampiran harus berupa PDF, JPG, JPEG, atau PNG.',
            'attachment.max' => 'Ukuran lampiran maksimal 5MB.',
        ];
    }
}
