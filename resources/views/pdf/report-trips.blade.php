<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Perjalanan Dinas</title>
    <style>
        table {
            font-size: 14px;
            padding-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #c6c6c6;
            /* color: white; */
        }

        th,
        td {
            border: 1px solid;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <img height="100px" src="{{ env('APP_URL', 'http://localhost') . '/images/logo.png' }}">
    </div>
    <hr>
    <section style="padding-top:10px;">
        <h3 align="center" style="margin-bottom: 0px">Laporan Rekap Perjalan Dinas Periode {{ $month }}
            {{ $year }}
        </h3>
        <table align="center">
            <thead>
                <th>No.</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Pekerjaan</th>
                <th>Tanggal</th>
                <th>Koordinator</th>
                <th>Total Pengeluaran</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach ($data as $index=>$trip)
                    <tr>
                        <td style="text-align: center">{{$index+1}}.</td>
                        <td style="text-align: center">{{ $trip->code }}</td>
                        <td style="text-align: center">{{ $trip->customer->name }}</td>
                        <td style="text-align: center">{{ $trip->jobCategory->name }}</td>
                        <td style="text-align: center">
                            @if ($trip->total_day > 1)
                                {{ Carbon\Carbon::parse($trip->start_date)->translatedFormat('d F Y') }} -
                                {{ Carbon\Carbon::parse($trip->end_date)->translatedFormat('d F Y') }}
                            @else
                                {{ Carbon\Carbon::parse($trip->start_date)->translatedFormat('d F Y') }}
                            @endif
                        </td>
                        <td style="text-align: center">{{ $trip->users[0]->user->first_name }}
                            {{ $trip->users[0]->user->last_name }}</td>
                        <td style="text-align: center">
                            {{ 'Rp ' . number_format($trip->expenses_sum_total_nominal, 2, ',', '.') }}</td>
                        <td style="text-align: center">
                            @if ($trip->result == 1)
                                Menunggu
                            @elseif ($trip->result == 2)
                                Selesai
                            @elseif ($trip->result == 3)
                                Tertunda
                            @else
                                Jadwal Ulang
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right"><b>Total</b></td>
                    <td style="text-align: center">
                        <b>{{ 'Rp ' . number_format($trip->expenses_sum_total_nominal, 2, ',', '.') }}</b></td>
                    <td style="border: 0px"></td>
                </tr>
            </tfoot>
        </table>
    </section>
    <small><i>* Laporan ini dibuat otomatis oleh sistem</i></small>
</body>

</html>
