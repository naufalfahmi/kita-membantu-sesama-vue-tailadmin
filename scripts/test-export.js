import XLSX from 'xlsx';
import fs from 'fs';

function loadTemplate() {
  const tplPath = 'public/template/template-transaksi.xlsx';
  if (!fs.existsSync(tplPath)) throw new Error('Template not found: ' + tplPath);
  const wb = XLSX.readFile(tplPath, { cellDates: true });
  return wb;
}

function simulateExport(transaksiList) {
  const templateWb = loadTemplate();
  const sheetName = templateWb.SheetNames[0];
  const ws = templateWb.Sheets[sheetName];
  // Remove merges and autofilter to avoid invalid merge ranges causing recovery errors
  if (ws['!merges']) delete ws['!merges'];
  if (ws['!autofilter']) delete ws['!autofilter'];

  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1:A1');
  let cols = range.e.c + 1;

  const rowVal = (r, c) => {
    const cell = ws[XLSX.utils.encode_cell({ r, c })];
    return cell && cell.v !== undefined && cell.v !== null ? String(cell.v).trim() : null;
  };

  const colNames = [];
  for (let c = 0; c < cols; c++) {
    const top = rowVal(0, c);
    const mid = rowVal(1, c);
    const low = rowVal(2, c);
    let name = null;
    if (mid && low) name = `${mid} ${low}`;
    else if (mid) name = mid;
    else if (top) name = top;
    else if (low) name = low;
    colNames.push(name);
  }

  const findCol = (candidates) => {
    const lc = candidates.map((s) => s.toLowerCase());
    for (let i = 0; i < colNames.length; i++) {
      const v = (colNames[i] || '').toLowerCase();
      if (lc.some((c) => c && v.includes(c))) return i;
    }
    return -1;
  };

  const colIdx = {
    nama: findCol(['nama']),
    alamat: findCol(['alamat']),
    kelurahan: findCol(['kelurahan']),
    kecamatan: findCol(['kecamatan']),
    kota: findCol(['kota', 'kota/kab', 'kota/kab']),
    provinsi: findCol(['provinsi']),
    no_hp: findCol(['no hp', 'no hp', 'no.', 'hp']),
    penempatan: findCol(['penempatan']),
    pj: findCol(['pj']),
    status: findCol(['status']),
    total: findCol(['total']),
  };

  const groups = [];
  for (let i = 0; i < colNames.length; i++) {
    const n = (colNames[i] || '').toLowerCase();
    if (n.includes('tgl') || n === 'tgl') {
      const danaIdx = (() => {
        for (let j = i + 1; j < Math.min(i + 6, colNames.length); j++) {
          const nn = (colNames[j] || '').toLowerCase();
          if (nn.includes('dana') || nn.includes('amt') || nn.includes('dana ')) return j;
        }
        return -1;
      })();
      const ketIdx = (() => {
        for (let j = (danaIdx !== -1 ? danaIdx + 1 : i + 1); j < Math.min(i + 8, colNames.length); j++) {
          const nn = (colNames[j] || '').toLowerCase();
          if (nn.includes('keterangan') || nn.includes('ket') || nn.includes('keter')) return j;
        }
        return -1;
      })();
      if (danaIdx !== -1 && ketIdx !== -1) groups.push({ tgl: i, dana: danaIdx, ket: ketIdx });
    }
  }

  const monthMap = new Map();
  for (const t of transaksiList) {
    if (!t.tanggal_transaksi) continue;
    const d = new Date(t.tanggal_transaksi);
    if (isNaN(d.getTime())) continue;
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    if (!monthMap.has(key)) {
      const label = d.toLocaleString('en-GB', { month: 'long', year: 'numeric' });
      monthMap.set(key, { label, key });
    }
  }
  const months = Array.from(monthMap.values());

  if (groups.length === 0) throw new Error('No groups found');

  const headerTitleRow = 0;
  const headerMainRow = 1;
  const headerSubRow = 2;

  const sanitizeString = (s) => {
    return String(s || '').replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '')
  }

  const setCell = (r, c, value, type = 's') => {
    if (c < 0) return;
    const cellRef = XLSX.utils.encode_cell({ r, c });
    if (value === null || value === undefined || value === '') { delete ws[cellRef]; return; }
    if (type === 'n') {
      const num = Number(value);
      if (Number.isFinite(num)) ws[cellRef] = { v: num, t: 'n' };
      else delete ws[cellRef];
    } else {
      ws[cellRef] = { v: sanitizeString(String(value)), t: 's' };
    }
  };

  setCell(headerTitleRow, 0, 'Transaksi');
  const mainHeaders = ['No', 'Tgl', 'Nama', 'Alamat', 'Kelurahan', 'Kecamatan', 'Kota/Kab', 'Provinsi', 'No HP', 'Penempatan', 'PJ', 'Status'];
  const mainIdx = {};
  for (let i = 0; i < mainHeaders.length; i++) { setCell(headerMainRow, i, mainHeaders[i]); setCell(headerSubRow, i, ''); mainIdx[mainHeaders[i]] = i; }

  for (let i = 0; i < groups.length; i++) {
    const g = groups[i];
    if (i < months.length) {
      const m = months[i];
      setCell(headerMainRow, g.tgl, m.label);
      setCell(headerSubRow, g.tgl, 'tgl');
      setCell(headerSubRow, g.dana, 'dana');
      setCell(headerSubRow, g.ket, 'keterangan');
    } else {
      setCell(headerMainRow, g.tgl, ''); setCell(headerSubRow, g.tgl, ''); setCell(headerSubRow, g.dana, ''); setCell(headerSubRow, g.ket, '');
    }
  }

  let totalCol = colIdx.total;
  if (totalCol < 0) { totalCol = cols; setCell(headerMainRow, totalCol, 'Total'); setCell(headerSubRow, totalCol, ''); cols = totalCol + 1; }
  else { setCell(headerMainRow, totalCol, 'Total'); setCell(headerSubRow, totalCol, ''); }

  const startRow = 3;
  const safeClearTo = Math.max(range.e.r, startRow + transaksiList.length + 50);
  for (let r = startRow; r <= safeClearTo; r++) {
    for (const h of Object.values(mainIdx)) { delete ws[XLSX.utils.encode_cell({ r, c: h })]; }
    for (const g of groups) { delete ws[XLSX.utils.encode_cell({ r, c: g.tgl })]; delete ws[XLSX.utils.encode_cell({ r, c: g.dana })]; delete ws[XLSX.utils.encode_cell({ r, c: g.ket })]; }
    delete ws[XLSX.utils.encode_cell({ r, c: totalCol })];
  }

  // Build donor aggregates
  const donorMap = new Map();
  for (const t of transaksiList) {
    const key = t.donatur?.id || t.donatur?.nama || `unknown_${t.kode}`;
    if (!donorMap.has(key)) donorMap.set(key, { info: t.donatur || {}, months: new Map(), total: 0 });
    const rec = donorMap.get(key);
    const d = t.tanggal_transaksi ? new Date(t.tanggal_transaksi) : null;
    const tglISO = d && !isNaN(d.getTime()) ? `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}` : '';
    const tglDisplay = d && !isNaN(d.getTime()) ? d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
    const monthKey = d ? `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}` : 'unknown';
    const nominal = Number(t.nominal || 0);
    if (!rec.months.has(monthKey)) rec.months.set(monthKey, { tglISO, tglDisplay, dana: nominal, ket: t.keterangan || '' });
    else { const ex = rec.months.get(monthKey); if (ex.tglISO === '' || (tglISO !== '' && tglISO < ex.tglISO)) { ex.tglISO = tglISO; ex.tglDisplay = tglDisplay; } ex.dana = (ex.dana || 0) + nominal; ex.ket = ex.ket ? `${ex.ket}; ${t.keterangan || ''}` : (t.keterangan || ''); }
    rec.total += nominal;
  }

  const donors = Array.from(donorMap.entries()).map(([k, v]) => ({ key: k, info: v.info, months: v.months, total: v.total }));

  let writeRow = startRow;
  let counter = 1;
  for (const donor of donors) {
    setCell(writeRow, mainIdx['No'], counter, 'n');
    let earliestISO = ''; let earliestDisplay = '';
    for (const [mk, mv] of donor.months.entries()) { if (mv.tglISO && (earliestISO === '' || mv.tglISO < earliestISO)) { earliestISO = mv.tglISO; earliestDisplay = mv.tglDisplay; } }
    setCell(writeRow, mainIdx['Tgl'], earliestDisplay, 's');
    setCell(writeRow, mainIdx['Nama'], donor.info?.nama || '', 's');
    setCell(writeRow, mainIdx['Alamat'], donor.info?.alamat || '', 's');
    setCell(writeRow, mainIdx['Kelurahan'], donor.info?.kelurahan || '', 's');
    setCell(writeRow, mainIdx['Kecamatan'], donor.info?.kecamatan || '', 's');
    setCell(writeRow, mainIdx['Kota/Kab'], donor.info?.kota || donor.info?.kota_kab || '', 's');
    setCell(writeRow, mainIdx['Provinsi'], donor.info?.provinsi || '', 's');
    setCell(writeRow, mainIdx['No HP'], donor.info?.no_hp || '', 's');
    setCell(writeRow, mainIdx['Penempatan'], donor.info?.penempatan || '', 's');
    setCell(writeRow, mainIdx['PJ'], donor.info?.pj || '', 's');
    setCell(writeRow, mainIdx['Status'], donor.info?.status || '', 's');

    for (let i = 0; i < groups.length; i++) {
      const g = groups[i];
      if (i < months.length) {
        const monthKey = months[i].key; const agg = donor.months.get(monthKey);
        if (agg) { setCell(writeRow, g.tgl, agg.tglDisplay, 's'); setCell(writeRow, g.dana, agg.dana, 'n'); setCell(writeRow, g.ket, agg.ket, 's'); }
        else { setCell(writeRow, g.tgl, '', 's'); setCell(writeRow, g.dana, '', 'n'); setCell(writeRow, g.ket, '', 's'); }
      } else { setCell(writeRow, g.tgl, '', 's'); setCell(writeRow, g.dana, '', 'n'); setCell(writeRow, g.ket, '', 's'); }
    }

    setCell(writeRow, totalCol, donor.total, 'n');
    writeRow++; counter++;
  }

  const newRef = `A1:${XLSX.utils.encode_col(cols - 1)}${Math.max(writeRow - 1, headerSubRow + 1)}`;
  ws['!ref'] = newRef;

  const outDir = 'tmp';
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir);
  const out = `tmp/transaksi_test_out.xlsx`;
  XLSX.writeFile(templateWb, out);
  console.log('Wrote', out);

  // Try to read back
  try {
    const wb2 = XLSX.readFile(out, { cellDates: true });
    console.log('Re-read success, sheets:', wb2.SheetNames);
  } catch (err) {
    console.error('Read back failed:', err);
  }
}

// Create sample transaksi with date values including older ones
const sample = [
  { kode: 'TRX1', donatur: { id: 'd1', nama: 'Endry Dendi Rudia' }, nominal: 10000, tanggal_transaksi: '2025-03-01', keterangan: 'donasi' },
  { kode: 'TRX2', donatur: { id: 'd2', nama: 'Mamduh abi Darda' }, nominal: 20000, tanggal_transaksi: '2026-01-01', keterangan: 'donasi' },
  { kode: 'TRX3', donatur: { id: 'd3', nama: 'Puspa' }, nominal: 15000, tanggal_transaksi: '2023-05-10', keterangan: 'donasi' },
  // dates that could be parsed differently
  { kode: 'TRX4', donatur: { id: 'd4', nama: 'Afandi (aexon Fc)' }, nominal: 12345, tanggal_transaksi: '2022-10-24', keterangan: 'donasi' },
];

simulateExport(sample);
