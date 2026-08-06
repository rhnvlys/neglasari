<?php

namespace App\Data\Reports;

class AttendanceReportFilterData
{
    public function __construct(
        public readonly ?string $date = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
        public readonly ?int $month = null,
        public readonly ?int $year = null,
        public readonly ?int $employee_id = null,
        public readonly ?int $position_id = null,
        public readonly ?string $status = null,
        public readonly ?string $source = null,
        public readonly ?string $keyword = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            date: $validated['date'] ?? null,
            start_date: $validated['start_date'] ?? null,
            end_date: $validated['end_date'] ?? null,
            month: isset($validated['month']) ? (int) $validated['month'] : null,
            year: isset($validated['year']) ? (int) $validated['year'] : null,
            employee_id: isset($validated['employee_id']) ? (int) $validated['employee_id'] : null,
            position_id: isset($validated['position_id']) ? (int) $validated['position_id'] : null,
            status: $validated['status'] ?? null,
            source: $validated['source'] ?? null,
            keyword: $validated['keyword'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'date' => $this->date,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'month' => $this->month,
            'year' => $this->year,
            'employee_id' => $this->employee_id,
            'position_id' => $this->position_id,
            'status' => $this->status,
            'source' => $this->source,
            'keyword' => $this->keyword,
        ], fn($value) => $value !== null && $value !== '');
    }
}
