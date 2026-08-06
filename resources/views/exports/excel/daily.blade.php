<table>
    <thead>
        <tr>
            <th colspan="19" style="font-weight: bold; text-align: center; font-size: 14pt;">
                PEMERINTAH KABUPATEN TASIKMALAYA
            </th>
        </tr>
        <tr>
            <th colspan="19" style="font-weight: bold; text-align: center; font-size: 12pt;">
                KECAMATAN KLASTER DESA NEGLASARI
            </th>
        </tr>
        <tr>
            <th colspan="19" style="font-weight: bold; text-align: center; font-size: 11pt;">
                LAPORAN HARIAN ABSENSI PERANGKAT DESA
            </th>
        </tr>
        <tr>
            <th colspan="19" style="text-align: center; font-style: italic;">
                Periode: {{ $filter->date ?? ($filter->start_date && $filter->end_date ? $filter->start_date . ' s/d ' . $filter->end_date : 'Semua Periode') }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #4CAF50; color: white; font-weight: bold;">
            <th>No</th>
            <th>Tanggal</th>
            <th>No Pegawai</th>
            <th>Nama Pegawai</th>
            <th>Jabatan</th>
            <th>Jadwal Masuk</th>
            <th>Jam Masuk</th>
            <th>Jadwal Pulang</th>
            <th>Jam Pulang</th>
            <th>Status Kehadiran</th>
            <th>Sumber Absensi</th>
            <th>Menit Terlambat</th>
            <th>Menit Pulang Awal</th>
            <th>Durasi Kerja (Jam)</th>
            <th>Jarak Check-in (m)</th>
            <th>Jarak Check-out (m)</th>
            <th>Status Lokasi</th>
            <th>Status Check-out</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->attendance_date->toDateString() }}</td>
                <td>{{ $attendance->employee->employee_number }}</td>
                <td>{{ $attendance->employee->full_name }}</td>
                <td>{{ $attendance->employee->position->name }}</td>
                <td>{{ $attendance->workSchedule->check_in_time ?? '-' }}</td>
                <td>{{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i:s') : '-' }}</td>
                <td>{{ $attendance->workSchedule->check_out_time ?? '-' }}</td>
                <td>{{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i:s') : '-' }}</td>
                <td>{{ $attendance->attendance_status->label() }}</td>
                <td>{{ $attendance->source }}</td>
                <td>{{ $attendance->late_minutes ?? 0 }}</td>
                <td>{{ $attendance->early_leave_minutes ?? 0 }}</td>
                <td>{{ $attendance->work_duration_minutes ? round($attendance->work_duration_minutes / 60, 2) : 0 }}</td>
                <td>{{ $attendance->check_in_distance ?? 0 }}</td>
                <td>{{ $attendance->check_out_distance ?? 0 }}</td>
                <td>{{ $attendance->check_in_location_status ? $attendance->check_in_location_status->label() : '-' }}</td>
                <td>{{ $attendance->check_out_status ? $attendance->check_out_status->label() : '-' }}</td>
                <td>{{ $attendance->notes ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
