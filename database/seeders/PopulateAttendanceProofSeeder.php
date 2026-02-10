<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class PopulateAttendanceProofSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // List of available images in public/storage/attendance_proofs
        // We act as if we scanned the directory, or use the list we got.
        // The path stored in DB is relative to the disk root (usually 'public').
        // So storage/attendance_proofs/filename.jpg -> attendance_proofs/filename.jpg
        
        $images = [
            'attendance_proofs/DSC01691.JPG',
            'attendance_proofs/DSC01800(1).JPG',
            'attendance_proofs/amirok2.jpg',
            'attendance_proofs/orangemankey.jpg',
            'attendance_proofs/uolnHawOtOnbYdu90WIe8iAt1hdzjLTElfGm5CfX.jpg',
        ];

        // Find assignments that have clocked in but have no proof
        $assignments = Assignment::whereNotNull('clockInTime')
                                 ->whereNull('imageProof')
                                 ->get();

        $count = 0;
        foreach ($assignments as $assignment) {
            // Pick a random image
            $randomImage = $images[array_rand($images)];
            
            $assignment->imageProof = $randomImage;
            $assignment->save();
            $count++;
        }

        $this->command->info("Updated {$count} assignments with random attendance proofs.");
    }
}
