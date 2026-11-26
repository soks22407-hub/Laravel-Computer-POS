<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Purchase Request #{{ $pr->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 13px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 160px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th {
            background: #1e293b;
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        table.items td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        table.items tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge-pending {
            color: #92400e;
            background: #fef9c3;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .badge-approved {
            color: #065f46;
            background: #d1fae5;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .badge-rejected {
            color: #991b1b;
            background: #fee2e2;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
            color: #999;
        }

        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 4px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <h2>Purchase Request</h2>
    <p class="subtitle">Request #{{ $pr->id }} — {{ $pr->created_at->format('d/m/Y') }}</p>

    <table class="info-table">
        <tr>
            <td>Employee:</td>
            <td>{{ $pr->employee->name }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ $pr->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td>
                <span class="badge-{{ $pr->status }}">{{ strtoupper($pr->status) }}</span>
            </td>
        </tr>
        @if($pr->note)
        <tr>
            <td>Note:</td>
            <td>{{ $pr->note }}</td>
        </tr>
        @endif
        @if($pr->approvedBy)
        <tr>
            <td>Reviewed by:</td>
            <td>{{ $pr->approvedBy->name }} on {{ \Carbon\Carbon::parse($pr->approved_at)->format('d/m/Y H:i') }}</td>
        </tr>
        @endif
        @if($pr->reject_reason)
        <tr>
            <td>Reject Reason:</td>
            <td style="color:red;">{{ $pr->reject_reason }}</td>
        </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Variant</th>
                <th>Current Qty</th>
                <th>Requested Qty</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pr->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->variant ?? '—' }}</td>
                <td>{{ $item->current_quantity }}</td>
                <td><strong>{{ $item->requested_quantity }}</strong></td>
                <td>{{ $item->reason ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:60px; display:table; width:100%;">
        <div style="display:table-cell; text-align:center; width:50%;">
            <div style="border-top:1px solid #333; margin-top:50px; padding-top:4px;">
                Employee Signature<br>
                <strong>{{ $pr->employee->name }}</strong>
            </div>
        </div>
        <div style="display:table-cell; text-align:center; width:50%;">
            <div style="border-top:1px solid #333; margin-top:50px; padding-top:4px;">
                Approved By<br>
                <strong>
                    @if($pr->approvedBy)
                    {{ $pr->approvedBy->name }}
                    @endif
                </strong>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>