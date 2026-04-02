<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $report['meta']['title'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .section { margin-top: 18px; }
    </style>
</head>
<body>
    <h1>{{ $report['meta']['title'] }}</h1>
    <div class="meta">Generated: {{ $report['meta']['generated_at'] }} | Filters: {{ $report['meta']['filters'] ?? '-' }}</div>

    <div class="section">
        <h3>Summary</h3>
        <table>
            <thead>
                <tr>
                    @foreach($report['summary_headers'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($report['summary_rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!empty($report['details_rows']))
    <div class="section">
        <h3>Details</h3>
        <table>
            <thead>
                <tr>
                    @foreach($report['details_headers'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($report['details_rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ is_array($cell) ? json_encode($cell) : $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
