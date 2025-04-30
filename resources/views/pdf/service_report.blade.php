<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .report-info {
            margin-bottom: 20px;
        }
        .report-info table {
            width: 100%;
        }
        .report-info th {
            text-align: left;
            font-weight: normal;
            color: #666;
        }
        .report-info td {
            font-weight: bold;
        }
        table.jobs {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.jobs th {
            background-color: #f3f4f6;
            border-bottom: 2px solid #d1d5db;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table.jobs td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
        }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in-progress { background-color: #dbeafe; color: #1e40af; }
        .status-awaiting-parts { background-color: #ede9fe; color: #5b21b6; }
        .status-repaired { background-color: #d1fae5; color: #065f46; }
        .status-delivered { background-color: #f3f4f6; color: #1f2937; }
        .status-canceled { background-color: #fee2e2; color: #991b1b; }
        .summary {
            margin-top: 30px;
        }
        .summary h3 {
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary th {
            text-align: left;
            width: 70%;
            padding: 5px;
        }
        .summary td {
            text-align: right;
            padding: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laptop Service Center</h1>
            <p>Service Jobs Report</p>
        </div>
        
        <!-- Report Information -->
        <div class="report-info">
            <table>
                <tr>
                    <th>Date Range:</th>
                    <td>
                        @if($dateFrom && $dateTo)
                            {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                        @elseif($dateFrom)
                            From {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                        @elseif($dateTo)
                            Until {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                        @else
                            All Time
                        @endif
                    </td>
                </tr>
                @if($status)
                <tr>
                    <th>Status Filter:</th>
                    <td>{{ $status }}</td>
                </tr>
                @endif
                <tr>
                    <th>Total Jobs:</th>
                    <td>{{ $jobs->count() }}</td>
                </tr>
                <tr>
                    <th>Generated On:</th>
                    <td>{{ now()->format('M d, Y h:i A') }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Jobs Table -->
        <table class="jobs">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Customer</th>
                    <th>Device Info</th>
                    <th>Technician</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Est. Cost</th>
                    <th>Final Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->job_id }}</td>
                    <td>{{ $job->customer->name }}</td>
                    <td>
                        {{ $job->device_type }}
                        @if($job->brand || $job->model)
                            <br><small>{{ $job->brand }} {{ $job->model }}</small>
                        @endif
                    </td>
                    <td>{{ $job->technician->name ?? 'Unassigned' }}</td>
                    <td>
                        @php
                            $statusClass = [
                                'Pending' => 'status-pending',
                                'In Progress' => 'status-in-progress',
                                'Awaiting Parts' => 'status-awaiting-parts',
                                'Repaired' => 'status-repaired',
                                'Delivered' => 'status-delivered',
                                'Canceled' => 'status-canceled',
                            ][$job->status] ?? 'status-pending';
                        @endphp
                        <span class="status {{ $statusClass }}">{{ $job->status }}</span>
                    </td>
                    <td>{{ $job->created_at->format('M d, Y') }}</td>
                    <td>LKR {{ number_format($job->estimated_cost ?? 0, 2) }}</td>
                    <td>LKR {{ number_format($job->final_cost ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <h3>Summary</h3>
            
            <!-- Status Summary -->
            <table>
                <tr>
                    <th colspan="2">Jobs by Status</th>
                </tr>
                @php
                    $statusCounts = [];
                    foreach(['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'] as $status) {
                        $statusCounts[$status] = $jobs->where('status', $status)->count();
                    }
                @endphp
                
                @foreach($statusCounts as $status => $count)
                    @if($count > 0)
                    <tr>
                        <th>{{ $status }}</th>
                        <td>{{ $count }}</td>
                    </tr>
                    @endif
                @endforeach
            </table>
            
            <!-- Financial Summary -->
            <table style="margin-top: 20px;">
                <tr>
                    <th colspan="2">Financial Summary</th>
                </tr>
                <tr>
                    <th>Total Estimated Cost</th>
                    <td>LKR {{ number_format($jobs->sum('estimated_cost') ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Final Cost</th>
                    <td>LKR {{ number_format($jobs->sum('final_cost') ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Average Final Cost per Job</th>
                    <td>LKR {{ number_format($jobs->count() > 0 ? $jobs->sum('final_cost') / $jobs->count() : 0, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Laptop Service Center - Service Jobs Report</p>
        </div>
    </div>
</body>
</html> 