<table>
    <thead>
        <tr>
            <th colspan="20" style="font-weight: bold; text-align: center; font-size: 14pt;">
                PEMERINTAH KABUPATEN TASIKMALAYA
            </th>
        </tr>
        <tr>
            <th colspan="20" style="font-weight: bold; text-align: center; font-size: 12pt;">
                KECAMATAN SALAWU DESA NEGLASARI
            </th>
        </tr>
        <tr>
            <th colspan="20" style="font-weight: bold; text-align: center; font-size: 11pt;">
                LAPORAN BULANAN ABSENSI PERANGKAT DESA
            </th>
        </tr>
        <tr>
            <th colspan="20" style="text-align: center; font-style: italic;">
                Periode: {{ $filter->month ? \Carbon\Carbon::create()->month($filter->month)->translatedFormat('F') : 'Semua Bulan' }} {{ $filter->year ?? 'Semua Tahun' }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #4CAF50; color: white; font-weight: bold;">
            <th>No</th>
            <th>No Pegawai</th>
            <th>Nama Pegawai</th>
            <th>Jabatan</th>
            <th>Hari Kerja Efektif</th>
            <th>Hadir Tepat Waktu</th>
            <th>Terlambat</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Cuti</th>
            <th>Dinas Luar</th>
            <th>Tugas Lapangan</th>
            <th>Alpa</th>
            <th>Belum Check-out</th>
            <th>Total Terlambat (m)</th>
            <th>Total Pulang Awal (m)</th>
            <th>Total Durasi Kerja (jam)</th>
            <th>Kehadiran Fisik</th>
            <th>Kehadiran Admin</th>
            <th>Tingkat Ketepatan Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['employee']->employee_number }}</td>
                <td>{{ $item['employee']->full_name }}</td>
                <td>{{ $item['employee']->position->name }}</td>
                <td>{{ $item['effective_days'] }}</td>
                <td>{{ $item['present_on_time'] }}</td>
                <td>{{ $item['late'] }}</td>
                <td>{{ $item['permission'] }}</td>
                <td>{{ $item['sick'] }}</td>
                <td>{{ $item['leave'] }}</td>
                <td>{{ $item['official_duty'] }}</td>
                <td>{{ $item['field_assignment'] }}</td>
                <td>{{ $item['absent'] }}</td>
                <td>{{ $item['missing_checkout'] }}</td>
                <td>{{ $item['total_late_minutes'] }}</td>
                <td>{{ $item['total_early_leave_minutes'] }}</td>
                <td>{{ round($item['total_work_duration'] / 60, 2) }}</td>
                <td>{{ $item['physical_present_percent'] }}%</td>
                <td>{{ $item['admin_present_percent'] }}%</td>
                <td>{{ $item['punctuality_percent'] }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>
