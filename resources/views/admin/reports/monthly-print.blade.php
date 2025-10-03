<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; color:#333; }
        .container { max-width: 900px; margin: 20px auto; padding: 10px; }
    .inst { text-align:center; }
    .inst .center-line { font-size:16px; font-weight:800; }
    .inst .center-addr { font-size:13px; }
    .titlebar { display:flex; align-items:center; justify-content:space-between; gap:6px; margin:4px 0 8px; }
    .titlebar img { height:52px; object-fit:contain; }
    .titlebar .center { flex:1; text-align:center; font-size:18px; font-weight:700; }
    .header { text-align:center; margin: 8px 0; }
    .title { font-size: 20px; font-weight: 700; }
        .meta { color:#555; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; }
        th { background: #f8f9fa; text-align:left; }
        tfoot td { font-weight: 700; }
        .right { text-align: right; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
        .actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:10px; }
    .btn { padding:8px 12px; border-radius:6px; border:1px solid #ddd; background:#fff; cursor:pointer; }
    .btn-primary { background:#0d6efd; color:#fff; border-color:#0d6efd; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary:hover { background:#0b5ed7; border-color:#0a58ca; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:flex-end; margin-bottom:8px; gap:10px;" class="no-print">
            <button class="btn btn-primary" id="downloadPdfBtn" type="button" aria-label="Download PDF Report">
                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                <span>Download PDF Report</span>
            </button>
        </div>
        <!-- Print button removed: print initiated from parent tab -->
        <div class="inst">
            <div class="center-line">Cagayan de Oro City - Human Milk Bank & Lactation Support Center</div>
            <div class="center-addr">J.V. Seriña St. Carmen, Cagayan de Oro, Philippines</div>
        </div>
        <div class="titlebar">
            <img src="/logo.png" alt="Logo">
            <div class="center">{{ $title }}</div>
            <img src="/hospital logo.png" alt="Hospital Logo">
        </div>
        <div class="header">
            <div class="meta">Registered on: <strong>{{ $range }}</strong> &nbsp;&nbsp; Sort by: <strong>{{ $sortedBy }}</strong></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Requestor</th>
                    <th style="width:110px;">Date</th>
                    <th style="width:90px;">Time</th>
                    <th style="width:120px;" class="right">Total Volume (ml)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                <tr>
                    <td class="right">{{ $row['no'] }}</td>
                    <td>{{ $row['requestor'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['time'] }}</td>
                    <td class="right">{{ number_format($row['volume']) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;">No records</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Grand Total</strong></td>
                    <td class="right"><strong>{{ number_format($grandTotal) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        (function(){
            const btn = document.getElementById('downloadPdfBtn');
            if(!btn) return;
            btn.addEventListener('click', ()=>{
                // year & month are passed from controller as blade vars
                const year = {{ (int)$year }};
                const month = {{ (int)$month }};
                const url = `/admin/reports/monthly-export?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
                window.location.href = url;
            });
        })();
    </script>
</body>
</html>
