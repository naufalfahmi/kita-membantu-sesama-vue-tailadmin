<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Slip Gaji Karyawan</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f5f5f5;
      padding: 20px;
    }
    .slip {
      max-width: 800px;
      margin: auto;
      background: #fff;
      border: none; /* removed outer border */
      padding: 24px;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 12px;
      margin-bottom: 20px;
    }
    .header h2 { margin: 0 }
    .company-info { font-size: 14px }
    .info-table { width:100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px }
    .info-table td { padding: 6px 8px; vertical-align: top }
    .info-table td:nth-child(1), .info-table td:nth-child(3) { font-weight: 700; width: 22% }
    .info-table td:nth-child(2), .info-table td:nth-child(4) { width: 28% }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px }
    th, td { border: none; padding: 8px } /* removed table cell borders */
    th { background: #eee; text-align: left }    .text-right { text-align: right }
    .total { font-weight: bold }
    .footer { display: flex; justify-content: space-between; margin-top: 40px; font-size: 14px }
    .signature { text-align: center; width: 200px }
    @media print { body { background: none; padding: 0 } .slip { border: none } }
  </style>
</head>
<body>
  <div class="slip">
    <div class="header">
      <h2>SLIP GAJI KARYAWAN</h2>
      <div class="company-info">
        {{ config('app.name') }}
      </div>
    </div>

    @php
      $periodLabel = isset($record->period->month) && isset($record->period->year)
        ? \Carbon\Carbon::createFromDate($record->period->year, $record->period->month, 1)->format('F Y')
        : (($record->period->month ?? '') . '/' . ($record->period->year ?? ''));
    @endphp

    <table class="info-table">
      <tr>
        <td>Nama Karyawan</td>
        <td>{{ $record->employee->name ?? '-' }}</td>
        <td>Bulan</td>
        <td>{{ $periodLabel }}</td>
      </tr>
      <tr>
        <td>Jabatan</td>
        <td>{{ $record->employee->posisi ?? '-' }}</td>
        <td>Tanggal Cetak</td>
        <td>{{ now()->format('d-m-Y') }}</td>
      </tr>
      <tr>
        <td>NIK</td>
        <td>{{ $record->employee->no_induk ?? '-' }}</td>
        <td>Status</td>
        <td>{{ ucfirst($record->status ?? '') }}</td>
      </tr>
    </table>

    <table>
      <thead>
        <tr>
          <th colspan="2">Pendapatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($earnings as $e)
          <tr>
            <td>{{ $e->description }}</td>
            <td class="text-right">Rp {{ number_format($e->amount ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="2">-</td></tr>
        @endforelse
        <tr class="total">
          <td>Total Pendapatan</td>
          <td class="text-right">Rp {{ number_format($gross ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <table>
      <thead>
        <tr>
          <th colspan="2">Potongan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($deductions as $d)
          <tr>
            <td>{{ $d->description }}</td>
            <td class="text-right">Rp {{ number_format(abs($d->amount ?? 0), 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="2">-</td></tr>
        @endforelse
        <tr class="total">
          <td>Total Potongan</td>
          <td class="text-right">Rp {{ number_format($deductionsTotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <table>
      <tbody>
        <tr class="total">
          <td>Gaji Bersih (Take Home Pay)</td>
          <td class="text-right">Rp {{ number_format($net ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

  </div>
</body>
</html>