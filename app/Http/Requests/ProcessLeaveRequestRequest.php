<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $leaveRequest = $this->route('leaveRequest');
        $action = $this->route()->getActionMethod(); // 'approve' or 'reject'
        
        return $this->user()->can($action, $leaveRequest);
    }

    public function rules(): array
    {
        $rules = [
            'approval_note' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->route()->getActionMethod() === 'reject') {
            $rules['approval_note'] = ['required', 'string', 'max:1000'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'approval_note.required' => 'Catatan penolakan wajib diisi.',
            'approval_note.max' => 'Catatan terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
