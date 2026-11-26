<script>
function buildRangeText(from, to, month, year, preset) {
    var parts = [];

    if (from || to) {
        parts.push('Date From: ' + (from || '—') + '   To: ' + (to || '—'));
    }

    if (month) {
        var months = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
        parts.push('Month: ' + months[parseInt(month)]);
    }

    if (year) {
        parts.push('Year: ' + year);
    }

    if (preset) {
        var presets = { today: 'Today', this_week: 'This Week', this_month: 'This Month' };
        parts.push('Range: ' + (presets[preset] || preset));
    }

    if (parts.length === 0) {
        return 'Date Range: All';
    }

    return parts.join('   |   ');
}

function printExcelStyle(opts) {
    var printWindow = window.open('', '', 'width=1100,height=750');
    printWindow.document.write(`
        <html>
            <head>
                <title>${opts.title}</title>
                <style>
                    * { box-sizing: border-box; }
                    body {
                        font-family: Arial, Helvetica, sans-serif;
                        padding: 20px;
                        color: #000;
                    }
                    h2 { margin: 0 0 4px 0; font-size: 18px; }
                    h4 { margin: 0 0 12px 0; font-size: 14px; font-weight: normal; color: #444; }
                    .meta { font-size: 12px; margin-bottom: 12px; }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    th, td {
                        border: 1px solid #000;
                        padding: 4px 8px;
                    }
                    th {
                        background: #f2f2f2;
                        font-weight: bold;
                        text-align: left;
                    }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                    .footer { margin-top: 16px; font-size: 11px; }

                    /* strip out app-specific decoration so it looks like a spreadsheet */
                    .badge { border: none !important; padding: 0 !important; background: none !important; color: #000 !important; }
                    .body-title-2 { font-weight: bold; }
                    .text-tiny { font-size: 11px; color: #555; }
                    a { color: #000; text-decoration: none; }
                </style>
            </head>
            <body>
                <h2>${opts.title}</h2>
                <h4>${opts.subtitle}</h4>
                <div class="meta">${opts.meta}</div>
                ${opts.tableHtml}
                <div class="footer">Printed on: {{ now()->format('d-m-Y H:i') }}</div>
                <script>
                    window.onload = function () {
                        window.print();
                        window.onafterprint = function () { window.close(); };
                    };
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}
</script>