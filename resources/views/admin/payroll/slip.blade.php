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
      border: 1px solid #ddd;
      padding: 24px;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 12px;
      margin-bottom: 20px;
    }
    .header h2 {
      margin: 0;
    }
    .company-info {
      font-size: 14px;
    }
    .info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 14px;
    }
    th, td {
      border: 1px solid #000;
      padding: 8px;
    }
    th {
      background: #eee;
      text-align: left;
    }
    .text-right {
      text-align: right;
    }
    .total {
      font-weight: bold;
    }
    .footer {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      font-size: 14px;
    }
    .signature {
      text-align: center;
      width: 200px;
    }
    @media print {
      body {
        background: none;
        padding: 0;
      }
      .slip {
        border: none;
      }
    }
  </style>
</head>
<body>
  <div class="slip">
    <div class="header">
      <h2>SLIP GAJI KARYAWAN</h2>
      <div class="company-info">
        <strong>PT Contoh Sejahtera</strong><br />
        Jl. Contoh No. 123, Jakarta
      </div>
    </div>

    <div class="info">
      <div>
        <strong>Nama Karyawan</strong> : Budi Santoso<br />
        <strong>Jabatan</strong> : Software Engineer<br />
        <strong>NIK</strong> : 1234567890
      </div>
      <div>
        <strong>Bulan</strong> : Agustus 2024<br />
        <strong>Tanggal Cetak</strong> : 31-08-2024
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th colspan="2">Pendapatan</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Gaji Pokok</td>
          <td class="text-right">Rp 5.000.000</td>
        </tr>
        <tr>
          <td>Tunjangan</td>
          <td class="text-right">Rp 1.000.000</td>
        </tr>
        <tr class="total">
          <td>Total Pendapatan</td>
          <td class="text-right">Rp 6.000.000</td>
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
        <tr>
          <td>BPJS Kesehatan</td>
          <td class="text-right">Rp 150.000</td>
        </tr>
        <tr>
          <td>BPJS Ketenagakerjaan</td>
          <td class="text-right">Rp 200.000</td>
        </tr>
        <tr class="total">
          <td>Total Potongan</td>
          <td class="text-right">Rp 350.000</td>
        </tr>
      </tbody>
    </table>

    <table>
      <tbody>
        <tr class="total">
          <td>Gaji Bersih (Take Home Pay)</td>
          <td class="text-right">Rp 5.650.000</td>
        </tr>
      </tbody>
    </table>

    <div class="footer">
      <div class="signature">
        Mengetahui,<br /><br /><br />
        (HRD)
      </div>
      <div class="signature">
        Diterima oleh,<br /><br /><br />
        (Karyawan)
      </div>
    </div>
  </div>
</body>
</html>
