/**
 * Genera el Excel con las hojas Manual test case y Balder test case
 */

const XLSX = require('../node_modules/xlsx');
const fs   = require('fs');
const path = require('path');

const manual = JSON.parse(fs.readFileSync(path.join(__dirname, 'feenicia_test_results.json'),   'utf8'));
const balder = JSON.parse(fs.readFileSync(path.join(__dirname, 'feenicia_balder_results.json'), 'utf8'));

// ── Helpers ───────────────────────────────────────────────────────────────────
function prettyJson(obj) {
    try { return JSON.stringify(obj, null, 2); } catch { return String(obj); }
}

function buildSheet(titleRows, columnHeaders, dataRows) {
    const aoa = [...titleRows, [], columnHeaders, ...dataRows];
    const ws  = XLSX.utils.aoa_to_sheet(aoa);
    ws['!cols'] = [
        { wch: 28 },  // Name
        { wch: 45 },  // Description
        { wch: 55 },  // Execution comments
        { wch: 14 },  // Date
        { wch: 12 },  // Hour
        { wch: 55 },  // Description of result
        { wch: 20 },  // Result obtained
        { wch: 12 },  // Evidence
        { wch: 80 },  // Request
        { wch: 80 },  // Response
    ];
    const headerLine = titleRows.length + 2; // +1 blank, +1 header row (1-indexed)
    ws['!rows'] = [];
    for (let i = 0; i < headerLine; i++) ws['!rows'].push({ hpt: 16 });
    for (let i = 0; i < dataRows.length; i++) ws['!rows'].push({ hpt: 200 });
    return ws;
}

const COL_HEADERS = [
    'Name', 'Description', 'Execution comments',
    'Date of test', 'Hour of test', 'Description of result',
    'Result obtained', 'Evidence', 'Request', 'Response',
];

// ════════════════════════════════════════════════════════════════════════════
// HOJA 1 — Manual test case
// ════════════════════════════════════════════════════════════════════════════
const manualMeta = [
    { name: 'Generate Order Sale',
      description: 'Generation of an order sale, using the quantity, description and price of the product',
      execComments: 'To consume this service, it is not necessary cipher any field in the JSON, it is only necessary send the token "x-requested-with"',
      descResult: 'OrderId was generated' },
    { name: 'Signature Save',
      description: 'Sending data to make the transaction',
      execComments: 'It is necessary encrypt the following fields:\n• PAN\n• CVV\n• FechaExp\n• Cardholder Name',
      descResult: 'you must obtain the following data\n• transactionId\n• authNum' },
    { name: 'Manual Save',
      description: "Saving transaction's data",
      execComments: 'It is not necessary encrypt any data of the json in this service, only the data obtained in the previous services will be used. It is necessary encrypt all the json with aes.',
      descResult: 'If the responseCode is equal to "00", it means that the consumption of the service was done correctly.' },
    { name: 'Create Receipt',
      description: 'Generation of the receipt with the transaction data',
      execComments: 'It is not necessary to cipher any data of the json in this service. It is necessary to cipher all the json with aes.',
      descResult: 'The service responds a field named "receiptId".' },
    { name: 'Send Receipt',
      description: 'Sending receipt by mail',
      execComments: "It is necessary to cipher: Customer's mail. In which we use aesRequest and it is necessary cipher all the json with aes.",
      descResult: 'Generation of receipt in PDF format' },
];

const manualRows = manual.map((r, i) => {
    const m   = manualMeta[i] || {};
    const res = r.result === 'SUCCESS' ? 'SUCCESS' : `FAIL (${r.response?.responseCode ?? ''})`;
    return [m.name || r.name, m.description || '', m.execComments || '',
            r.date, r.hour, m.descResult || '', res, '', prettyJson(r.request), prettyJson(r.response)];
});

const manualSheet = buildSheet(
    [
        ['Test case'],
        ['Integration Name', 'Certification of Pasaporte a tu Salud'],
        ['Type', 'Manual sale'],
        ['Tested by', 'carlos.gonzalez@degestec.com'],
        [],
        ['Comments: All tests described in this document will be carried out based on the API document.'],
    ],
    COL_HEADERS,
    manualRows
);

// ════════════════════════════════════════════════════════════════════════════
// HOJA 2 — Balder test case
// ════════════════════════════════════════════════════════════════════════════
const balderRows = balder.map(r => {
    let res;
    if (r.result === 'N/A') {
        res = 'N/A — Requires Feenicia QA timeout endpoint';
    } else if (r.result === 'SUCCESS') {
        res = 'SUCCESS';
    } else {
        const code = r.response?.responseCode ?? r.response?.code ?? '';
        res = `FAIL (${code})`;
    }
    return [
        r.name, r.description, r.execComments,
        r.date, r.hour, r.descriptionResult,
        res, '',
        r.result === 'N/A' ? '' : prettyJson(r.request),
        r.result === 'N/A' ? '' : prettyJson(r.response),
    ];
});

const balderSheet = buildSheet(
    [
        ['Balder test case'],
        ['Integration Name', 'Certification of Pasaporte a tu Salud'],
        ['Type', 'Balder / Tokenization'],
        ['Tested by', 'carlos.gonzalez@degestec.com'],
        [],
        ['Comments: All tests described in this document will be carried out based on the API document.'],
        ['NOTE: Balder token endpoints (401) require tokenAplic credential from SERTI — not yet in .env'],
    ],
    COL_HEADERS,
    balderRows
);

// ════════════════════════════════════════════════════════════════════════════
// Workbook
// ════════════════════════════════════════════════════════════════════════════
const wb = XLSX.utils.book_new();
XLSX.utils.book_append_sheet(wb, manualSheet, 'Manual test case');
XLSX.utils.book_append_sheet(wb, balderSheet, 'Balder test case');

const outputPath = path.join(__dirname, '../FR-OP-021_Test_Matrix__Pasaporte_a_tu_salud_FILLED.xlsx');
XLSX.writeFile(wb, outputPath);

console.log(`Excel generado: ${outputPath}`);

// Resumen
const manSuccess = manual.filter(r => r.result === 'SUCCESS').length;
const balSuccess = balder.filter(r => r.result === 'SUCCESS').length;
const balNA      = balder.filter(r => r.result === 'N/A').length;
console.log(`\nManual test case: ${manSuccess}/${manual.length} SUCCESS`);
console.log(`Balder test case: ${balSuccess}/${balder.length - balNA} SUCCESS (${balNA} N/A)`);
