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
        <td>Periode</td>
        <td>{{ $periodLabel }}</td>
      </tr>
      <tr>
        <td>Jabatan</td>
        <td>{{ $record->employee->posisi ?? '-' }}</td>
        <td>Tanggal Cetak</td>
        <td>{{ now()->format('d-m-Y') }}</td>
      </tr>
      <tr>
        <td>No. Induk</td>
        <td>{{ $record->employee->no_induk ?? '-' }}</td>
        <td>Status</td>
        <td>{{ ucfirst($record->status ?? '') }}</td>
      </tr>
    </table>

    @if(isset($penghasilan) && $penghasilan->isNotEmpty())
    <table style="table-layout:fixed;">
      <colgroup>
        <col style="width:60%" />
        <col style="width:20%" />
        <col style="width:20%" />
      </colgroup>
      <thead>
        <tr>
          <th>Penghasilan</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @forelse($penghasilan as $e)
          <tr>
            <td>{{ $e->description }}</td>
            <td class="text-right">
              @php $qty = $e->qty ?? 0; $qtyFloat = (float)$qty; $qtyIsWhole = floor($qtyFloat) == $qtyFloat; @endphp
              @if(isset($e->qty_type) && $e->qty_type === 'percent')
                @if($qtyIsWhole)
                  {{ (int)$qty }}% dari Rp {{ number_format($e->unit_value ?? 0, 0, ',', '.') }}
                @else
                  {{ number_format($qtyFloat, 2, ',', '.') }}% dari Rp {{ number_format($e->unit_value ?? 0, 0, ',', '.') }}
                @endif
              @else
                @if(isset($e->qty) && $qtyFloat !== 1.0)
                  {{ $e->qty }}
                @endif
              @endif
            </td>
            <td class="text-right">Rp {{ number_format($e->amount ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3">-</td></tr>
        @endforelse
        <tr class="total">
          <td colspan="2">Total Penghasilan</td>
          <td class="text-right">Rp {{ number_format($pengTotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>
    @endif

    @if(isset($fundraising) && $fundraising->isNotEmpty())
    <table style="table-layout:fixed;">
      <colgroup>
        <col style="width:60%" />
        <col style="width:20%" />
        <col style="width:20%" />
      </colgroup>
      <thead>
        <tr>
          <th>Fundraising</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @forelse($fundraising as $f)
          <tr>
            <td>{{ $f->description }}</td>
            <td class="text-right">
              @php $qty = $f->qty ?? 0; $qtyFloat = (float)$qty; $qtyIsWhole = floor($qtyFloat) == $qtyFloat; @endphp
              @if(isset($f->qty_type) && $f->qty_type === 'percent')
                @if($qtyIsWhole)
                  {{ (int)$qty }}% dari Rp {{ number_format($f->unit_value ?? 0, 0, ',', '.') }}
                @else
                  {{ number_format($qtyFloat, 2, ',', '.') }}% dari Rp {{ number_format($f->unit_value ?? 0, 0, ',', '.') }}
                @endif
              @else
                @if(isset($f->qty) && $qtyFloat !== 1.0)
                  {{ $f->qty }}
                @endif
              @endif
            </td>
            <td class="text-right">Rp {{ number_format($f->amount ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3">-</td></tr>
        @endforelse
        <tr class="total">
          <td colspan="2">Total Fundraising</td>
          <td class="text-right">Rp {{ number_format($fundTotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>
    @endif

    @if(isset($other) && $other->isNotEmpty())
    <table style="table-layout:fixed;">
      <colgroup>
        <col style="width:60%" />
        <col style="width:20%" />
        <col style="width:20%" />
      </colgroup>
      <thead>
        <tr>
          <th>Lain-lain</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @forelse($other as $o)
          <tr>
            <td>{{ $o->description }}</td>
            <td class="text-right">
              @php $qty = $o->qty ?? 0; $qtyFloat = (float)$qty; $qtyIsWhole = floor($qtyFloat) == $qtyFloat; @endphp
              @if(isset($o->qty_type) && $o->qty_type === 'percent')
                @if($qtyIsWhole)
                  {{ (int)$qty }}% dari Rp {{ number_format($o->unit_value ?? 0, 0, ',', '.') }}
                @else
                  {{ number_format($qtyFloat, 2, ',', '.') }}% dari Rp {{ number_format($o->unit_value ?? 0, 0, ',', '.') }}
                @endif
              @else
                @if(isset($o->qty) && $qtyFloat !== 1.0)
                  {{ $o->qty }}
                @endif
              @endif
            </td>
            <td class="text-right">Rp {{ number_format($o->amount ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3">-</td></tr>
        @endforelse
        <tr class="total">
          <td colspan="2">Total Lain-lain</td>
          <td class="text-right">Rp {{ number_format($otherTotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>
    @endif

    @if(isset($deductions) && $deductions->isNotEmpty())
    <table style="table-layout:fixed;">
      <colgroup>
        <col style="width:60%" />
        <col style="width:20%" />
        <col style="width:20%" />
      </colgroup>
      <thead>
        <tr>
          <th>Potongan</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @forelse($deductions as $d)
          <tr>
            <td>{{ $d->description }}</td>
            <td class="text-right">
              @php $qty = $d->qty ?? 0; $qtyFloat = (float)$qty; $qtyIsWhole = floor($qtyFloat) == $qtyFloat; @endphp
              @if(isset($d->qty_type) && $d->qty_type === 'percent')
                @if($qtyIsWhole)
                  {{ (int)$qty }}% dari Rp {{ number_format($d->unit_value ?? 0, 0, ',', '.') }}
                @else
                  {{ number_format($qtyFloat, 2, ',', '.') }}% dari Rp {{ number_format($d->unit_value ?? 0, 0, ',', '.') }}
                @endif
              @else
                @if(isset($d->qty) && $qtyFloat !== 1.0)
                  {{ $d->qty }}
                @endif
              @endif
            </td>
            <td class="text-right">Rp {{ number_format(abs($d->amount ?? 0), 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3">-</td></tr>
        @endforelse
        <tr class="total">
          <td colspan="2">Total Potongan</td>
          <td class="text-right">Rp {{ number_format($deductionsTotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>
    @endif

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