import JSZip from 'jszip'
import fs from 'fs'

const sanitizeString = (s) => String(s || '').replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '')
const csvEscape = (v) => {
  const s = sanitizeString(v === null || v === undefined ? '' : String(v))
  const needQuote = s.includes(';') || s.includes('"') || s.includes('\n') || s.includes('\r')
  const escaped = s.replace(/"/g, '""')
  return needQuote ? `"${escaped}"` : escaped
}

const sanitizeSheetName = (s) => {
  let name = String(s || '').replace(/[\\/*?:\[\]]+/g, '-').slice(0, 31).trim()
  if (!name) name = 'Sheet'
  return name
}

async function generateZip(transaksiList) {
  const fundMap = new Map()
  for (const t of transaksiList) {
    const f = t.fundraiser || (t.fundraiser_id ? { id: t.fundraiser_id } : null)
    const name = (f && (f.name || f.nama || f.label)) ? (f.name || f.nama || f.label) : 'Unassigned'
    if (!fundMap.has(name)) fundMap.set(name, [])
    fundMap.get(name).push(t)
  }

  const zip = new JSZip()

  for (const [fundName, fundTransaksi] of fundMap.entries()) {
    const monthSet = new Set()
    for (const t of fundTransaksi) {
      if (!t.tanggal_transaksi) continue
      const d = new Date(t.tanggal_transaksi)
      if (isNaN(d.getTime())) continue
      const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
      monthSet.add(key)
    }
    const months = Array.from(monthSet).sort()
    const monthMeta = months.map((m) => {
      const [y, mm] = m.split('-')
      const date = new Date(Number(y), Number(mm) - 1, 1)
      const label = date.toLocaleString('en-GB', { month: 'long', year: 'numeric' })
      return { key: m, label }
    })

    const donorMap = new Map()
    for (const t of fundTransaksi) {
      const key = t.donatur?.id || t.donatur?.nama || `unknown_${t.kode}`
      if (!donorMap.has(key)) donorMap.set(key, { info: t.donatur || {}, months: new Map() })
      const rec = donorMap.get(key)
      const d = t.tanggal_transaksi ? new Date(t.tanggal_transaksi) : null
      if (!d || isNaN(d.getTime())) continue
      const monthKey = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
      const tglISO = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
      const tglDisplay = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
      const nominal = Number(t.nominal || 0)
      const note = t.keterangan || ''
      if (!rec.months.has(monthKey)) {
        rec.months.set(monthKey, { tglISO, tglDisplay, nominal, ket: note })
      } else {
        const agg = rec.months.get(monthKey)
        if (!agg.tglISO || (tglISO && tglISO < agg.tglISO)) {
          agg.tglISO = tglISO
          agg.tglDisplay = tglDisplay
        }
        agg.nominal = (agg.nominal || 0) + nominal
        agg.ket = agg.ket ? `${agg.ket}; ${note}` : note
      }
    }

    const mainHeaders = ['No', 'Nama', 'Alamat', 'Kelurahan', 'Kecamatan', 'Kota/Kab', 'Provinsi', 'No HP', 'Penempatan', 'PJ', 'Status']
    const headerRow1 = [...mainHeaders]
    const headerRow2 = Array(mainHeaders.length).fill('')
    for (const meta of monthMeta) {
      headerRow1.push(meta.label, '', '')
      headerRow2.push('Tanggal', 'Nominal', 'Keterangan')
    }
    const rows = []
    rows.push(headerRow1.map(csvEscape).join(';'))
    rows.push(headerRow2.map(csvEscape).join(';'))

    const donors = Array.from(donorMap.entries()).map(([k, v]) => ({ key: k, info: v.info, months: v.months }))

    let idx = 1
    for (const donor of donors) {
      const rowArr = []
      rowArr.push(csvEscape(String(idx)))
      rowArr.push(csvEscape(donor.info?.nama || ''))
      rowArr.push(csvEscape(donor.info?.alamat || ''))
      rowArr.push(csvEscape(donor.info?.kelurahan || ''))
      rowArr.push(csvEscape(donor.info?.kecamatan || ''))
      rowArr.push(csvEscape(donor.info?.kota || donor.info?.kota_kab || ''))
      rowArr.push(csvEscape(donor.info?.provinsi || ''))
      rowArr.push(csvEscape(donor.info?.no_hp || ''))
      rowArr.push(csvEscape(donor.info?.penempatan || ''))
      rowArr.push(csvEscape(donor.info?.pj || ''))
      rowArr.push(csvEscape(donor.info?.status || ''))

      for (const meta of monthMeta) {
        const agg = donor.months.get(meta.key)
        if (!agg) {
          rowArr.push(csvEscape(''))
          rowArr.push(csvEscape(''))
          rowArr.push(csvEscape(''))
        } else {
          rowArr.push(csvEscape(agg.tglDisplay || ''))
          rowArr.push(csvEscape(String(agg.nominal || 0)))
          rowArr.push(csvEscape(agg.ket || ''))
        }
      }

      rows.push(rowArr.join(';'))
      idx++
    }

    const csvContent = '\uFEFF' + rows.join('\r\n')
    zip.file(`${sanitizeSheetName(fundName)}.csv`, csvContent)
  }

  const zipBlob = await zip.generateAsync({ type: 'nodebuffer' })
  const outDir = 'tmp'
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir)
  const out = `${outDir}/transaksi_csv_test.zip`
  fs.writeFileSync(out, zipBlob)
  console.log('Wrote', out)
}

const sample = [
  { kode: 'TRX1', donatur: { id: 'd1', nama: 'Endry Dendi Rudia' }, nominal: 10000, tanggal_transaksi: '2020-03-01', keterangan: 'donasi', fundraiser: { name: 'dian' } },
  { kode: 'TRX2', donatur: { id: 'd1', nama: 'Endry Dendi Rudia' }, nominal: 5000, tanggal_transaksi: '2020-03-10', keterangan: 'lunas', fundraiser: { name: 'dian' } },
  { kode: 'TRX3', donatur: { id: 'd2', nama: 'Mamduh abi Darda' }, nominal: 20000, tanggal_transaksi: '2020-04-05', keterangan: 'donasi', fundraiser: { name: 'dian' } },
  { kode: 'TRX4', donatur: { id: 'd3', nama: 'Puspa' }, nominal: 15000, tanggal_transaksi: '2021-05-10', keterangan: 'donasi', fundraiser: { name: 'another' } },
]

generateZip(sample)
