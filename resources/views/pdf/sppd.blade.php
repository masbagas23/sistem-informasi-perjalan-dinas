<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Perintah Perjalan Dinas</title>
    <style>
        .kop_surat {
            height: 100px;
            width: 100%;
            background-size: contain;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
        }
    </style>
</head>

<body>
    <header
        style="background-image: url('{{  config('app.url') }}/images/kop-surat.png');"
        class="kop_surat"></header>
    <section style="padding-top:10px;">
        <h2 align="center" style="margin-bottom: 0px"><u>Surat Perintah Dinas</u></h2>
        <p align="center" style="margin-top: 0px">No: {{ $business_trip->code_letter }}</p>
    </section>
    <section style="padding-top: 30px;line-height: 1.5;">
        {{-- <p>Yth. Bapak/Ibu Pimpinan {{$business_trip->customer->name}}<br>Di tempat</p> --}}
        <p>Yang bertanda tangan dibawah ini {{ $business_trip->approver->gender == 1 ? 'Bapak' : 'Ibu' }}
            {{ $business_trip->approver->first_name }} {{ $business_trip->approver->middle_name }}
            {{ $business_trip->approver->last_name }} selaku {{ $business_trip->approver->jobPosition->name }} dari PT
            Fusi Global Teknologi, dengan ini memberikan tanggung jawab sebagai koordinator kepada:</p>
    </section>

    <section style="line-height: 1.5;margin-left:50px">
        <table style="">
            <tr>
                <td style="width:70px"><b>Nama</b></td>
                <td>:</td>
                <td>{{ $business_trip->users[0]->user->first_name }} {{ $business_trip->users[0]->user->middle_name }}
                    {{ $business_trip->users[0]->user->last_name }}</td>
            </tr>
            <tr>
                <td style="width:70px"><b>NIP</b></td>
                <td>:</td>
                <td>{{ $business_trip->users[0]->user->nip }}</td>
            </tr>
            <tr>
                <td style="width:70px"><b>Jabatan</b></td>
                <td>:</td>
                <td>{{ $business_trip->users[0]->user->jobPosition->name }}</td>
            </tr>
            <tr>
                <td style="width:70px"><b>Telp/Hp</b></td>
                <td>:</td>
                <td>{{ $business_trip->users[0]->user->phone_number }}</td>
            </tr>
        </table>
    </section>
    <section style="line-height: 1.5">
        <p>Untuk {{ $business_trip->description }} pada tanggal
            {{ Carbon\Carbon::parse($business_trip->start_date)->translatedFormat('d F Y') }}
            @if ($business_trip->total_day > 1)
                s/d
                {{ Carbon\Carbon::parse($business_trip->end_date)->translatedFormat('d F Y') }}
                ({{ $business_trip->total_day }} Hari) 
            @endif
            ke {{ $business_trip->customer->name }} yang beralamat di:
        </p>
    </section>
    <section style="line-height: 1.5;margin-left:50px">
        <table style="vertical-align: top;">
            <tr>
                <td style="width:70px;vertical-align: top;"><b>Nama</b></td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $business_trip->customer->name }}</td>
            </tr>
            <tr>
                <td style="width:70px;vertical-align: top;"><b>Telepon</b></td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $business_trip->customer->telephone }}</td>
            </tr>
            <tr>
                <td style="width:70px;vertical-align: top;"><b>Alamat</b></td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $business_trip->customer->address }}</td>
            </tr>
        </table>
    </section>
    <section style="line-height: 1.5">
        <p>Demikian surat ini kami buat untuk dapat dilaksanakan dengan penuh rasa tanggung jawab.</p>

        <p style="margin-top:50px" align="right">
            Bandung, {{ Carbon\Carbon::parse($business_trip->created_date)->translatedFormat('d F Y') }}<br>
            {{ $business_trip->approver->jobPosition->name }}
        </p>
        <p align="right">
            <img height="80px"
                src="{{ config('app.url') . ($business_trip->approver->signature_url ? $business_trip->approver->signature_url : '/images/signature.png') }}">
            <br>
            {{ $business_trip->approver->first_name }} {{ $business_trip->approver->middle_name }}
            {{ $business_trip->approver->last_name }}
        </p>
    </section>
    <footer>
        <div style="background-color: #f5f5f5;width:100%;height:50px;color:#406587;font-size:12px;text-align:center">
            <div style="padding:10px">
                Office : Komplek Surapati Core (SUCORE) Blok M6 Jl.PHH Mustofa No. 39 Bandung 40124 Phone :
                +62-22-7308035 |
                Web : www.fusi.co.id | Email : info@fusi.co.id
            </div>
        </div>
        <div style="background-color: #406587;width:100%;height:10px">
            &nbsp;
        </div>
    </footer>
</body>

</html>
