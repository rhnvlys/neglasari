<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pegawai Belum Check-out</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 11pt;
            text-transform: uppercase;
            font-weight: normal;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 8pt;
            color: #666;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .filters {
            margin-bottom: 15px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer-sig {
            margin-top: 50px;
            float: right;
            width: 250px;
            text-align: center;
        }
        .footer-sig .date {
            margin-bottom: 60px;
        }
        .footer-sig .name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pemerintah Kabupaten Tasikmalaya</h1>
        <h2>Kecamatan Salawu • Desa Neglasari</h2>
        <p>Alamat: Jl. Neglasari No. 1, Kode Pos 46471</p>
    </div>

    <div class="title">
        Laporan Pegawai Belum Check-out
    </div>

    <div class="filters">
        Periode: {{ $filter->date ?? ($filter->start_date && $filter->end_date ? $filter->start_date . ' s/d ' . $filter->end_date : 'Semua Periode') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="3%">No</th>
                <th width="10%">Tanggal</th>
                <th width="12%">No Pegawai</th>
                <th>Nama Pegawai</th>
                <th width="15%">Jabatan</th>
                <th class="text-center" width="8%">Jam Masuk</th>
                <th class="text-center" width="8%">Durasi (jam)</th>
                <th width="10%">Status Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $attendance)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $attendance->attendance_date->toDateString() }}</td>
                    <td>{{ $attendance->employee->employee_number }}</td>
                    <td>{{ $attendance->employee->full_name }}</td>
                    <td>{{ $attendance->employee->position->name }}</td>
                    <td class="text-center">{{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i:s') : '-' }}</td>
                    <td class="text-center">
                        {{ $attendance->check_in_at ? round(now()->diffInMinutes($attendance->check_in_at) / 60, 2) : 0 }}
                    </td>
                    <td>{{ $attendance->check_in_location_status ? $attendance->check_in_location_status->label() : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="date">Neglasari, {{ now()->translatedFormat('d F Y') }}</div>
        <div class="name">Kepala Desa Neglasari</div>
        <div style="margin-top: 5px;">NIP. 197805122005011002</div>
    </div>
</body>
</html>
