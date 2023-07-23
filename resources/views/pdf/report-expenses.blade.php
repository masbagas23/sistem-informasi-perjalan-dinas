<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Biaya Pengeluaran</title>
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
        <img height="100px" src="{{ env('APP_URL', 'https://perjalanandinas.bagasraga.my.id') . '/images/logo.png' }}">
    </div>
    <hr>
    <section style="padding-top:10px;">
        <h3 align="center" style="margin-bottom: 0px">Laporan Rekap Biaya Pengeluaran Periode {{ $month }}
            {{ $year }}
        </h3>
        <table align="center">
            <thead>
                <th>No.</th>
                <th>Perjalanan Dinas</th>
                <th>Kategori Biaya</th>
                <th>Nominal</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach ($data as $index=>$trip)
                    <tr>
                        <td style="text-align: center">{{$index+1}}.</td>
                        <td style="text-align: center">{{$trip->expense->application->code}} - {{$trip->expense->application->customer->name}}</td>
                        <td style="text-align: center">{{$trip->costCategory->name}}</td>
                        <td style="text-align: center">
                            {{ 'Rp ' . number_format($trip->nominal, 2, ',', '.') }}</td>
                        <td style="text-align: center">{{$trip->description ?? '-'}}</td>
                        <td style="text-align: center">
                            @if ($trip->status == 1)
                                Menunggu
                            @elseif ($trip->status == 2)
                                Valid
                            @else
                                Tidak Valid
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right"><b>Total</b></td>
                    <td style="text-align: center">
                        <b>{{ 'Rp ' . number_format($total, 2, ',', '.') }}</b></td>
                    <td style="border: 0px"></td>
                    <td style="border: 0px"></td>
                </tr>
            </tfoot>
        </table>
    </section>
    <small><i>* Laporan ini dibuat otomatis oleh sistem</i></small>
</body>

</html>
