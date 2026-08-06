<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;

class AttendanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view attendance reports') || 
               $this->user()->can('view executive attendance reports') ||
               $this->user()->can('view own attendance report');
    }

    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'status' => ['nullable', new Enum(AttendanceStatus::class)],
            'source' => ['nullable', 'string', 'in:check_in,leave_request,manual_correction,system'],
            'keyword' => ['nullable', 'string', 'max:255'],
        ];
    }
    
    protected function prepareForValidation()
    {
        // Enforce employee_id restriction for non-admins (employees can only view their own)
        if (Auth::check() && !Auth::user()->can('view attendance reports') && !Auth::user()->can('view executive attendance reports')) {
            $this->merge([
                'employee_id' => Auth::user()->employee_id
            ]);
        }
    }
}
