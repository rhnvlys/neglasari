<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center; font-size: 14pt;">
                PEMERINTAH KABUPATEN TASIKMALAYA
            </th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center; font-size: 12pt;">
                KECAMATAN SALAWU DESA NEGLASARI
            </th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center; font-size: 11pt;">
                LAPORAN PEGAWAI BELUM CHECK-OUT
            </th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: center; font-style: italic;">
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
            <th>Jam Masuk</th>
            <th>Durasi (jam)</th>
            <th>Status Lokasi</th>
            <th>Jarak Check-in (m)</th>
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
                <td>{{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i:s') : '-' }}</td>
                <td>
                    {{ $attendance->check_in_at ? round(now()->diffInMinutes($attendance->check_in_at) / 60, 2) : 0 }}
                </td>
                <td>{{ $attendance->check_in_location_status ? $attendance->check_in_location_status->label() : '-' }}</td>
                <td>{{ $attendance->check_in_distance ?? 0 }}</td>
                <td>{{ $attendance->notes ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
