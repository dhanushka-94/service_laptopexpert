<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Note - {{ $job->job_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
        }
        .job-id {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            color: #1f2937;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }
        .section-content {
            padding-left: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 3px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 3px 0;
            vertical-align: top;
        }
        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in-progress { background-color: #dbeafe; color: #1e40af; }
        .status-awaiting-parts { background-color: #ede9fe; color: #5b21b6; }
        .status-repaired { background-color: #d1fae5; color: #065f46; }
        .status-delivered { background-color: #f3f4f6; color: #1f2937; }
        .status-canceled { background-color: #fee2e2; color: #991b1b; }
        .divider {
            border-top: 1px dashed #ddd;
            margin: 20px 0;
        }
        .signatures {
            margin-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 40px auto 10px;
        }
        .signature-label {
            text-align: center;
            font-size: 12px;
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
            <p>123 Tech Street, Tech City | Phone: (123) 456-7890</p>
            <p>Email: service@laptopservicecenter.com</p>
        </div>
        
        <!-- Job ID -->
        <div class="job-id">
            Job ID: {{ $job->job_id }}
        </div>
        
        <!-- Status -->
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
        <div class="status {{ $statusClass }}">
            {{ $job->status }}
        </div>
        
        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $job->customer->name }}</div>
                    </div>
                    @if($job->customer->phone)
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $job->customer->phone }}</div>
                    </div>
                    @endif
                    @if($job->customer->email)
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $job->customer->email }}</div>
                    </div>
                    @endif
                    @if($job->customer->address)
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value">{{ $job->customer->address }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Device Information -->
        <div class="section">
            <div class="section-title">Device Information</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Device Type:</div>
                        <div class="info-value">{{ $job->device_type }}</div>
                    </div>
                    @if($job->brand)
                    <div class="info-row">
                        <div class="info-label">Brand:</div>
                        <div class="info-value">{{ $job->brand }}</div>
                    </div>
                    @endif
                    @if($job->model)
                    <div class="info-row">
                        <div class="info-label">Model:</div>
                        <div class="info-value">{{ $job->model }}</div>
                    </div>
                    @endif
                    @if($job->serial_number)
                    <div class="info-row">
                        <div class="info-label">Serial Number:</div>
                        <div class="info-value">{{ $job->serial_number }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Service Information -->
        <div class="section">
            <div class="section-title">Service Information</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Date Received:</div>
                        <div class="info-value">{{ $job->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($job->completion_date)
                    <div class="info-row">
                        <div class="info-label">Completion Date:</div>
                        <div class="info-value">{{ $job->completion_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                    @if($job->technician)
                    <div class="info-row">
                        <div class="info-label">Technician:</div>
                        <div class="info-value">{{ $job->technician->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Issues and Diagnosis -->
        <div class="section">
            <div class="section-title">Reported Issues</div>
            <div class="section-content">
                {{ $job->reported_issues }}
            </div>
        </div>
        
        @if($job->accessories)
        <div class="section">
            <div class="section-title">Accessories Received</div>
            <div class="section-content">
                {{ $job->accessories }}
            </div>
        </div>
        @endif
        
        @if($job->diagnosis)
        <div class="section">
            <div class="section-title">Diagnosis</div>
            <div class="section-content">
                {{ $job->diagnosis }}
            </div>
        </div>
        @endif
        
        @if($job->repair_notes)
        <div class="section">
            <div class="section-title">Repair Notes</div>
            <div class="section-content">
                {{ $job->repair_notes }}
            </div>
        </div>
        @endif
        
        @if($job->parts_used)
        <div class="section">
            <div class="section-title">Parts Used</div>
            <div class="section-content">
                {{ $job->parts_used }}
            </div>
        </div>
        @endif
        
        <!-- Cost Information -->
        <div class="section">
            <div class="section-title">Cost Information</div>
            <div class="section-content">
                <div class="info-grid">
                    @if($job->estimated_cost)
                    <div class="info-row">
                        <div class="info-label">Estimated Cost:</div>
                        <div class="info-value">LKR {{ number_format($job->estimated_cost, 2) }}</div>
                    </div>
                    @endif
                    @if($job->final_cost)
                    <div class="info-row">
                        <div class="info-label">Final Cost:</div>
                        <div class="info-value">LKR {{ number_format($job->final_cost, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <!-- Notes -->
        @if($job->notes->count() > 0)
        <div class="section">
            <div class="section-title">Service Notes</div>
            <div class="section-content">
                @foreach($job->notes->where('is_private', false) as $note)
                <p><strong>{{ $note->created_at->format('M d, Y') }}:</strong> {{ $note->note }}</p>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Signatures -->
        <div class="signatures">
            <table width="100%">
                <tr>
                    <td width="45%">
                        <div class="signature-line"></div>
                        <div class="signature-label">Technician Signature</div>
                    </td>
                    <td width="10%">&nbsp;</td>
                    <td width="45%">
                        <div class="signature-line"></div>
                        <div class="signature-label">Customer Signature</div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing Laptop Service Center. We appreciate your business!</p>
            <p>This document was generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html> 