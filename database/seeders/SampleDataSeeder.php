<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\JobNote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the technician user (created in RoleAndPermissionSeeder)
        $technician = User::where('email', 'tech@example.com')->first();
        
        // Create another technician
        $secondTechnician = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);
        $secondTechnician->assignRole('technician');
        
        // Create sample customers
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-123-4567',
                'address' => '123 Main St, Anytown, USA'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'phone' => '555-234-5678',
                'address' => '456 Oak Ave, Somewhere, USA'
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael@example.com',
                'phone' => '555-345-6789',
                'address' => '789 Pine Rd, Nowhere, USA'
            ],
            [
                'name' => 'Emily Wilson',
                'email' => 'emily@example.com',
                'phone' => '555-456-7890',
                'address' => '321 Elm St, Elsewhere, USA'
            ],
            [
                'name' => 'Robert Martinez',
                'email' => 'robert@example.com',
                'phone' => '555-567-8901',
                'address' => '654 Maple Dr, Anywhere, USA'
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Get all customers for creating jobs
        $allCustomers = Customer::all();
        $technicians = User::role('technician')->get();

        // Sample laptop brands and models
        $laptopBrands = ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'Microsoft', 'Samsung'];
        $laptopModels = [
            'XPS 13', 'Inspiron 15', 'Latitude 7420',
            'Pavilion 15', 'Envy x360', 'Spectre',
            'ThinkPad X1', 'Legion 5', 'Yoga Slim 7',
            'MacBook Pro', 'MacBook Air',
            'ROG Zephyrus', 'VivoBook', 'ZenBook',
            'Aspire 5', 'Swift 3', 'Nitro 5',
            'Surface Laptop', 'Surface Book',
            'Galaxy Book Pro', 'Galaxy Book Flex'
        ];

        // Sample reported issues
        $reportedIssues = [
            'Device won\'t power on at all',
            'Screen has visible cracks and display issues',
            'Battery drains too quickly, only lasts about 30 minutes',
            'Keyboard has multiple unresponsive keys',
            'System running very slow, applications freezing frequently',
            'Blue screen errors occurring randomly',
            'Fan making loud noise and overheating',
            'Unable to connect to Wi-Fi networks',
            'Windows won\'t boot, stuck in a boot loop',
            'Liquid spill damage, keyboard and trackpad not working',
            'Hard drive clicking noise, unable to access data',
            'Screen flickering and displaying lines',
            'Touchpad not responding to gestures or clicks',
            'Audio not working through speakers or headphone jack',
            'USB ports not recognizing any devices'
        ];

        // Sample accessories
        $accessories = [
            'Charger, laptop bag',
            'Charger only',
            'Charger, mouse, external hard drive',
            'No accessories provided',
            'Charger, docking station',
            'Charger, wireless keyboard and mouse',
            'Laptop only, no accessories'
        ];

        // Create sample service jobs with different statuses
        $statuses = ['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'];
        
        for ($i = 0; $i < 25; $i++) {
            $customer = $allCustomers->random();
            $status = $statuses[array_rand($statuses)];
            $createdDays = rand(1, 45);  // Jobs created between 1 and 45 days ago
            $createdAt = now()->subDays($createdDays);
            
            // For completed jobs, add completion date
            $completionDate = null;
            if ($status === 'Repaired' || $status === 'Delivered') {
                $completionDays = rand(1, $createdDays - 1);  // Completed between 1 and (created days - 1) days ago
                $completionDate = now()->subDays($completionDays);
            }
            
            // Only assign technician to non-pending jobs
            $technicianId = ($status === 'Pending') ? null : $technicians->random()->id;
            
            // Set costs
            $estimatedCost = rand(50, 300);
            $finalCost = null;
            if ($status === 'Repaired' || $status === 'Delivered') {
                $finalCost = $estimatedCost + rand(-30, 50);  // Final cost might be slightly different
            }
            
            $deviceType = rand(1, 10) <= 8 ? 'Laptop' : (rand(1, 10) <= 5 ? 'Desktop' : 'Other');
            $brand = $deviceType === 'Laptop' ? $laptopBrands[array_rand($laptopBrands)] : ($deviceType === 'Desktop' ? $laptopBrands[array_rand($laptopBrands)] : '');
            $model = $deviceType === 'Laptop' ? $laptopModels[array_rand($laptopModels)] : '';
            
            // Generate a unique job ID based on the created_at date
            $date = Carbon::parse($createdAt)->format('Ymd');
            $randomPart = substr(md5(microtime()), 0, 3);
            $jobId = 'JOB-' . $date . '-' . $randomPart . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            
            $job = ServiceJob::create([
                'job_id' => $jobId,
                'customer_id' => $customer->id,
                'technician_id' => $technicianId,
                'device_type' => $deviceType,
                'brand' => $brand,
                'model' => $model,
                'serial_number' => strtoupper(substr(md5(rand()), 0, 10)),
                'reported_issues' => $reportedIssues[array_rand($reportedIssues)],
                'accessories' => $accessories[array_rand($accessories)],
                'status' => $status,
                'diagnosis' => $status !== 'Pending' ? 'Initial diagnosis shows ' . strtolower(collect(['hardware failure', 'software issue', 'malware infection', 'physical damage', 'component failure'])->random()) : null,
                'repair_notes' => in_array($status, ['Repaired', 'Delivered']) ? 'Repaired and tested. All systems functioning normally.' : null,
                'parts_used' => in_array($status, ['Repaired', 'Delivered']) ? collect(['replacement keyboard', 'new battery', 'display assembly', 'motherboard', 'power adapter', 'RAM upgrade', 'SSD replacement'])->random() : null,
                'estimated_cost' => $estimatedCost,
                'final_cost' => $finalCost,
                'completion_date' => $completionDate,
                'created_at' => $createdAt,
                'updated_at' => now()->subDays(rand(0, $createdDays)),
            ]);
            
            // Create notes for the job
            $noteCount = rand(0, 5);
            
            // For pending jobs, maybe have at most 1 note
            if ($status === 'Pending') {
                $noteCount = rand(0, 1);
            }
            
            $users = User::all();
            
            // Add intake note for all jobs
            JobNote::create([
                'service_job_id' => $job->id,
                'user_id' => 1, // Admin user
                'note' => 'Device received for service. Customer reports: ' . $job->reported_issues,
                'is_private' => false,
                'created_at' => $job->created_at,
                'updated_at' => $job->created_at,
            ]);
            
            for ($j = 0; $j < $noteCount; $j++) {
                // Notes are added over time after job creation
                $noteDays = rand(0, $createdDays - 1);
                $noteDate = now()->subDays($noteDays);
                
                // Different kinds of notes based on job status
                $noteTexts = [
                    'Pending' => ['Initial assessment scheduled.', 'Awaiting technician assignment.'],
                    'In Progress' => [
                        'Started diagnostics on the device.', 
                        'Running hardware tests.',
                        'Found potential issue with the ' . collect(['motherboard', 'hard drive', 'display', 'keyboard', 'battery'])->random() . '.',
                        'Diagnostic scan complete, evaluating repair options.'
                    ],
                    'Awaiting Parts' => [
                        'Ordered replacement ' . collect(['keyboard', 'screen', 'battery', 'power adapter', 'motherboard'])->random() . '.',
                        'Parts expected to arrive in ' . rand(2, 7) . ' days.',
                        'Checking with supplier about parts availability.',
                        'Parts have been backordered, updating customer.'
                    ],
                    'Repaired' => [
                        'Repairs completed successfully. All tests pass.',
                        'Device fixed and ready for pickup.',
                        'Final quality check completed.',
                        'Called customer to notify that repairs are complete.'
                    ],
                    'Delivered' => [
                        'Customer picked up the device.',
                        'Provided usage instructions to customer.',
                        'Customer confirmed device is working properly.'
                    ],
                    'Canceled' => [
                        'Customer decided not to proceed with repair.',
                        'Called customer about cancellation.',
                        'Processing refund for diagnostic fee.'
                    ]
                ];
                
                $noteText = isset($noteTexts[$status]) ? $noteTexts[$status][array_rand($noteTexts[$status])] : 'Status update on the repair.';
                $isPrivate = rand(0, 10) < 3; // 30% chance of being a private note
                
                JobNote::create([
                    'service_job_id' => $job->id,
                    'user_id' => $users->random()->id,
                    'note' => $noteText,
                    'is_private' => $isPrivate,
                    'created_at' => $noteDate,
                    'updated_at' => $noteDate,
                ]);
            }
        }
    }
} 