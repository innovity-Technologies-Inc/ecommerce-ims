<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 10px 20px; border-bottom: 1px solid #dee2e6; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .footer { font-size: 12px; color: #6c757d; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Low Stock Alert</h2>
        </div>
        <p>The following items have reached their minimum stock threshold:</p>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Warehouse</th>
                    <th>Current</th>
                    <th>Min Threshold</th>
                    <th>Suggested Restock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockItems as $item)
                <tr>
                    <td>{{ $item['name'] }} @if($item['variant_name']) ({{ $item['variant_name'] }}) @endif</td>
                    <td>{{ $item['warehouse_name'] }}</td>
                    <td class="text-danger">{{ $item['current_quantity'] }}</td>
                    <td>{{ $item['min_stock'] }}</td>
                    <td style="color: #28a745; font-weight: bold;">{{ $item['suggested_restock'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p>Please review and restock these items as soon as possible.</p>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}.</p>
        </div>
    </div>
</body>
</html>
