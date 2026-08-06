<?php

namespace App\Http\Requests;

use App\Support\Reports\AttendanceReportType;
use Illuminate\Validation\Rules\Enum;

class AttendanceExportRequest extends AttendanceReportRequest
{
    public function authorize(): bool
    {
        $canExportAll = $this->user()->can('export attendance reports excel') || $this->user()->can('export attendance reports pdf');
        $canExportOwn = $this->user()->can('export own attendance report');
        
        return $canExportAll || $canExportOwn;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'type' => ['required', new Enum(AttendanceReportType::class)],
            'format' => ['required', 'string', 'in:xlsx,pdf'],
        ]);
    }
}
