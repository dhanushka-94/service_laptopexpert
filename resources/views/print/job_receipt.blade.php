<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Job Receipt - {{ $job->job_id }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 5mm;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }
        .container {
            max-width: 190mm;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .company-logo {
            max-height: 50px;
            margin-bottom: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #2563eb;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .receipt-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }
        .job-id {
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
            color: #2563eb;
        }
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
            color: #1f2937;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }
        .section-content {
            padding-left: 5px;
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
            padding: 2px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 2px 0;
            vertical-align: top;
        }
        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 10pt;
            font-weight: bold;
            margin: 5px 0;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in-progress { background-color: #dbeafe; color: #1e40af; }
        .status-awaiting-parts { background-color: #ede9fe; color: #5b21b6; }
        .status-repaired { background-color: #d1fae5; color: #065f46; }
        .status-delivered { background-color: #f3f4f6; color: #1f2937; }
        .status-canceled { background-color: #fee2e2; color: #991b1b; }
        .divider {
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }
        .columns {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .column {
            width: 48%;
        }
        .signatures {
            margin-top: 20px;
        }
        .signature-block {
            width: 48%;
            display: inline-block;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 90%;
            margin: 30px auto 5px;
        }
        .signature-label {
            text-align: center;
            font-size: 9pt;
        }
        .notice {
            margin-top: 10px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 3px;
            font-size: 8pt;
            text-align: justify;
        }
        .footer {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }
        .important {
            font-weight: bold;
            color: #dc2626;
        }
        .notice ol, .notice ul {
            margin: 3px 0;
            padding-left: 20px;
        }
        .notice li {
            margin-bottom: 2px;
        }
        .notice h4, .notice h5 {
            margin: 5px 0 3px 0;
        }
        .notice p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">Print Receipt</button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Company Logo" class="company-logo">
            <h1>Laptop Expert (Pvt) Ltd</h1>
            <p>296/3/B, Delpe Junction, Ragama | Phone: +94764442221 | Email: info@laptopexpert.lk</p>
        </div>
        
        <!-- Receipt Title and Job ID -->
        <div class="receipt-title">Service Job Receipt - Job ID: {{ $job->job_id }}</div>
        
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
        
        <div class="columns">
            <!-- Customer Information -->
            <div class="column">
                <div class="section">
                    <div class="section-title">Customer Information</div>
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Name:</div>
                                <div class="info-value">{{ $job->customer->name }}</div>
                            </div>
                            @if($job->customer->phone_1)
                            <div class="info-row">
                                <div class="info-label">Phone:</div>
                                <div class="info-value">{{ $job->customer->phone_1 }}</div>
                            </div>
                            @endif
                            @if($job->customer->email)
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value">{{ $job->customer->email }}</div>
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
            </div>
            
            <!-- Right Column -->
            <div class="column">
                <!-- Job Information -->
                <div class="section">
                    <div class="section-title">Job Information</div>
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Date Received:</div>
                                <div class="info-value">{{ $job->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Issues and Accessories -->
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
            </div>
        </div>
        
        <!-- Terms and Conditions - More compact version -->
        <div class="notice">
            <h4>Terms and Conditions</h4>
            <ol>
                <li>Repairs will only proceed after the customer provides verbal or written consent. If additional issues are discovered during the repair, you will be contacted immediately with an updated estimate and recommendations.</li>
                <li>Customers are responsible for backing up their data. The Laptop Expert Pvt Ltd is not liable for data loss during repair.</li>
                <li>Repairs may come with a limited warranty (e.g., 30–90 days) on parts and labor. Physical damage or liquid spills after repair void the warranty.</li>
                <li>Devices not collected within 30 days of repair completion may incur storage fees or be considered abandoned.</li>
                <li>Full payment is due upon completion of repairs. Devices will not be released until payment is made in full.</li>
                <li>Software issues, viruses, and customer-inflicted damage after repair are not covered under any repair warranty.</li>
                <li>Repair times and costs depend on the availability of replacement parts. In some cases, parts may need to be ordered, which may extend the repair timeline.</li>
                <li>Timeframes vary based on the issue and parts availability.</li>
                <li>By submitting your laptop for repair, you authorize us to perform necessary repairs based on the initial diagnosis.</li>
                <li>If the issue cannot be resolved (e.g., severe motherboard damage), we will inform you of the findings and suggest possible alternatives, including replacement options.</li>
            </ol>

            <h4>Important Notice</h4>
            <p>When a laptop is brought in with a "no power" issue, we are unable to assess the functionality of other components such as the keyboard, display, battery, or internal hardware until the device is powered on. Our first step is to restore power to the laptop. Once the laptop is operational, we will conduct a thorough diagnostic to evaluate the condition of all other components. If any additional issues are identified, we will inform you and provide recommendations for further repairs.</p>

            <h4>BitLocker Encryption</h4>
            <p>When your laptop is repaired (especially if it involves chip replacement or BIOS changes), BitLocker encryption may be activated. If this happens, you'll need your BitLocker recovery key to access your data. Without the BitLocker key, the data cannot be recovered.</p>

            <h5>Your Responsibility:</h5>
            <ul>
                <li>Keep your BitLocker recovery key safe before bringing your laptop in.</li>
                <li>If you lose the recovery key, data loss may occur.</li>
                <li>The Laptop Expert Pvt Ltd is not responsible for any data loss due to BitLocker encryption issues.</li>
                <li>Please ensure the key is backed up securely.</li>
            </ul>
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Customer Signature</div>
            </div>
            
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Store Representative</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing Laptop Expert (Pvt) Ltd for your repair needs. For status updates, call +94764442221.</p>
        </div>
    </div>
</body>
</html> 