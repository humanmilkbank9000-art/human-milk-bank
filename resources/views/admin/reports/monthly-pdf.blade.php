<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
    .inst { text-align:center; margin-bottom: 6px; }
    .center-line { font-size: 14px; font-weight: 800; }
    .center-addr { font-size: 12px; }
    .titlebar { display: table; width: 100%; margin-bottom: 8px; }
    .titlebar .cell { display: table-cell; vertical-align: middle; }
    .titlebar .left { width: 18%; text-align: right; }
    .titlebar .center { width: 64%; text-align: center; }
    .titlebar .right { width: 18%; text-align: left; }
    .titlebar img { height: 46px; object-fit: contain; }
    .center-title { font-size: 18px; font-weight: 700; }
    .header { text-align: center; margin-bottom: 8px; }
    .title { font-size: 16px; font-weight: 700; }
        .meta { font-size: 12px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 8px; }
        th { background: #f3f3f3; }
        tfoot td { font-weight: 700; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="inst">
        <div class="center-line">Cagayan de Oro City - Human Milk Bank & Lactation Support Center</div>
        <div class="center-addr">J.V. Seriña St. Carmen, Cagayan de Oro, Philippines</div>
    </div>
    <div class="titlebar">
        <div class="cell left">
            @if(!empty($logoLeftSrc))
                <img src="{{ $logoLeftSrc }}" alt="Logo">
            @endif
        </div>
        <div class="cell center">
            <div class="center-title">{{ $title }}</div>
        </div>
        <div class="cell right">
            @if(!empty($logoRightSrc))
                <img src="{{ $logoRightSrc }}" alt="Hospital Logo">
            @endif
        </div>
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
</body>
</html>
