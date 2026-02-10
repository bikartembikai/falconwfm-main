<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Assignment;

class PayrollTestSeeder extends Seeder
{
    /**
     * Seed payments for testing payroll management.
     */
    public function run(): void
    {
        // Get existing assignments
        $assignments = Assignment::with('event', 'user')->get();

        if ($assignments->isEmpty()) {
            $this->command->error('No assignments found. Please create some assignments first.');
            return;
        }

        $paymentTitles = [
            'salary' => [
                'Event Facilitation Fee',
                'Workshop Facilitation Payment',
                'Training Session Fee',
                'Full Day Event Payment',
            ],
            'allowance' => [
                'Transportation Allowance',
                'Meal Allowance',
                'Equipment Allowance',
                'Communication Allowance',
                'Overtime Allowance',
            ],
        ];

        $statuses = ['pending', 'approved', 'paid'];
        $count = 0;

        foreach ($assignments->take(10) as $assignment) {
            // Create a salary payment for each assignment
            $salaryAmount = rand(2000, 8000);
            Payment::create([
                'assignmentID' => $assignment->assignmentID,
                'title' => $paymentTitles['salary'][array_rand($paymentTitles['salary'])],
                'amount' => $salaryAmount,
                'paymentType' => 'salary',
                'paymentStatus' => $statuses[array_rand($statuses)],
                'paymentDate' => now()->subDays(rand(0, 30)),
            ]);
            $count++;

            // Create an allowance payment for some assignments (70% chance)
            if (rand(1, 10) <= 7) {
                $allowanceAmount = rand(200, 1000);
                Payment::create([
                    'assignmentID' => $assignment->assignmentID,
                    'title' => $paymentTitles['allowance'][array_rand($paymentTitles['allowance'])],
                    'amount' => $allowanceAmount,
                    'paymentType' => 'allowance',
                    'paymentStatus' => $statuses[array_rand($statuses)],
                    'paymentDate' => now()->subDays(rand(0, 30)),
                ]);
                $count++;
            }
        }

        $this->command->info("✓ Created {$count} payment records (salary and allowance types)");
    }
}
