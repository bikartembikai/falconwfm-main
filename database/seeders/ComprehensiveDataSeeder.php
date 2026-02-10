<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Assignment;
use App\Models\PerformanceReview;
use App\Models\Leave;
use App\Models\Payment;
use App\Models\Skill;
use App\Models\EventRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    private $facilitatorsList = [];
    private $events = [];
    
    /**
     * Skill alias mapping for smart matching
     */
    private function getSkillAliases(): array
    {
        return [
            'Speaking' => ['Speaking', 'Public Speaking', 'Presentation'],
            'Public Speaking' => ['Public Speaking', 'Speaking', 'Presentation'],
            'Medic' => ['Medic', 'First Aid', 'Medical'],
            'Leadership' => ['Leadership', 'Team Leadership', 'Management', 'Organization Management'],
            'Teaching' => ['Teaching', 'Education', 'Training', 'Facilitating'],
            'Facilitating' => ['Facilitating', 'Teaching', 'Training'],
            'Survival' => ['Survival', 'Outdoor Skills', 'Camping', 'Hiking', 'Trekking'],
            'Hiking' => ['Hiking', 'Trekking', 'Outdoor Skills', 'Survival'],
            'Trekking' => ['Trekking', 'Hiking', 'Outdoor Skills', 'Survival'],
            'Swimming' => ['Swimming', 'Water Safety', 'Lifeguard'],
            'Logistics' => ['Logistics', 'Organization', 'Planning'],
            'Archery' => ['Archery', 'Shooting', 'Sports'],
            'Motivation' => ['Motivation', 'Inspirational', 'Leadership'],
            'Religious' => ['Religious', 'Islamic Studies', 'Spiritual'],
            'Time Management' => ['Time Management', 'Planning', 'Organization'],
            'Organization Management' => ['Organization Management', 'Leadership', 'Management'],
        ];
    }

    /**
     * Expand required skills to include aliases
     */
    private function expandSkills(array $required): array
    {
        $aliases = $this->getSkillAliases();
        $expanded = [];
        
        foreach ($required as $skill) {
            if (isset($aliases[$skill])) {
                $expanded = array_merge($expanded, $aliases[$skill]);
            } else {
                $expanded[] = $skill;
            }
        }
        
        return array_unique($expanded);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        PerformanceReview::truncate();
        Payment::truncate();
        Assignment::truncate();
        Leave::truncate();
        Event::truncate();
        DB::table('facilitator_skills')->truncate();
        User::where('role', 'facilitator')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('🚀 Starting Comprehensive Data Seeding...');
        
        // 1. Create facilitators
        $this->command->info('👥 Creating 50 facilitators...');
        $this->createFacilitators();
        
        // 2. Create events  
        $this->command->info('📅 Creating events...');
        $this->createEvents();
        
        // 3. Create assignments with attendance
        $this->command->info('🎯 Creating assignments with attendance data...');
        $this->createAssignmentsWithAttendance();
        
        // 4. Create performance reviews
        $this->command->info('⭐ Creating performance reviews...');
        $this->createPerformanceReviews();
        
        // 5. Update average ratings
        $this->command->info('📊 Calculating average ratings...');
        $this->updateAverageRatings();
        
        // 6. Create leave requests
        $this->command->info('🏖️ Creating leave requests...');
        $this->createLeaveRequests();
        
        // 7. Create payments
        $this->command->info('💰 Creating payment records...');
        $this->createPayments();
        
        $this->command->info('✅ Comprehensive seeding completed successfully!');
    }

    /**
     * Create 50 facilitators with logical experience levels
     */
    private function createFacilitators(): void
    {
        $names = [
            "Muhammad Qawiem Mustaqim bin Kamrizal",
            "Muhamad Zulhairie bin Iskandar",
            "Muhammad Adam Faris bin Mohd Nizam",
            "Muhammad Shafiq Shauqi bin Mohd Kamar",
            "Muhammad Akmal bin Mohd Desa",
            "Nur Syahirah binti Ahmad",
            "Muhammad Azrul Hakim bin Azhari",
            "Zamzikri bin Zamzuri",
            "Muhammad Asyraf bin Ahmad Sahimi",
            "Muhammad Adibhakimi bin Mohd Khairie",
            "Muhammad Adzri bin Azhar",
            "Muhammad Aiman Haziq bin Abdullah",
            "Ajwad Izzlan bin Mohd Azlan",
            "Iskandar Fahmi bin Abdul Halim",
            "Muhammad Alif bin Aminuddin",
            "Muhammad Amir Haziq bin Ahmad",
            "Amirul Aiman bin Hassan",
            "Arief Fudhail bin Ismail",
            "Ahmad Azamuddin bin Musa",
            "Muhammad Danial bin Hamzah",
            "Muhammad Firdaus bin Udin",
            "Muhammad Dinie Rusydi bin Rosli",
            "Zulhisyam bin Iskandar",
            "Muhammad Edham bin Shamsuri",
            "Haikal Akmal bin Halim",
            "Muhammad Haikal Afiq bin Razak",
            "Rariqul Aiman bin Zuha",
            "Ezuan bin Zakaria",
            "Fahmi bin Nordin",
            "Muhammad Fared Hakimi bin Farid",
            "Ammar Firdaus bin Amran",
            "Muhammad Hafiy Nurmuzammil bin Hafiz",
            "Muhammad Haikal bin Hashim",
            "Haiqal Farez Zuhairy bin Azhari",
            "Muhammad Hafiz bin Hassan",
            "Iman Hanafi bin Eddy Kesumajaya",
            "Muhammad Irfan bin Ibrahim",
            "Muhammad Izrin Syafiq bin Mohd Esra",
            "Muhammad Zulhelmi bin Mohd Salam",
            "Muhammad Haziq bin Mohd Aris",
            "Nafisah Nazirah binti Nazri",
            "Nazarul Hakimie bin Mohd Nazim",
            "Farihin Nublan bin Rosli",
            "Muhammad Saeeb bin Subre",
            "Siti Asmidar binti Serkan",
            "Noor Aisyah binti Ahmad Fauzi",
            "Muhammad Zailan bin Zailani",
            "Muhammad Adam Irfan bin Azman",
            "Nur Nabilah binti Mohd Sharif",
            "Mahadzir bin Mohamad"
        ];

        $allSkills = ['Speaking', 'Public Speaking', 'Medic', 'Leadership', 'Facilitating', 
                     'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 
                     'Logistics', 'Teaching', 'Archery', 'Time Management', 
                     'Organization Management', 'Swimming', 'Event Management',
                     'Project Management', 'Technical Leadership'];

        // Ensure all skills exist in database
        foreach ($allSkills as $skillName) {
            Skill::firstOrCreate(['skillName' => $skillName]);
        }

        // Experience distribution: most 2-5 years, some beginners, few veterans
        $experienceDistribution = array_merge(
            array_fill(0, 5, 0),      // 5 with 0 years
            array_fill(0, 8, 1),      // 8 with 1 year
            array_fill(0, 12, 2),     // 12 with 2 years
            array_fill(0, 10, 3),     // 10 with 3 years
            array_fill(0, 6, 4),      // 6 with 4 years
            array_fill(0, 4, 5),      // 4 with 5 years
            array_fill(0, 3, 7),      // 3 with 7 years
            array_fill(0, 2, 10)      // 2 with 10+ years
        );
        shuffle($experienceDistribution);

        foreach ($names as $index => $name) {
            $firstName = explode(' ', trim($name))[0];
            $email = strtolower(str_replace(' ', '', $firstName)) . ($index + 100) . '@falcon.test';
            
            $yearsExp = $experienceDistribution[$index] ?? rand(1, 5);
            
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'facilitator',
                'bankName' => ['Maybank', 'CIMB', 'Public Bank', 'RHB'][array_rand(['Maybank', 'CIMB', 'Public Bank', 'RHB'])],
                'bankAccountNumber' => rand(1000000000, 9999999999),
                'phoneNumber' => '01' . rand(10000000, 99999999),
                'experience' => $yearsExp . ' years experience in facilitation, training, and outdoor activities.',
                'joinDate' => now()->subMonths(rand($yearsExp * 2, max($yearsExp * 12, 1))),
                'averageRating' => 0, // Will be calculated later
            ]);

            // Assign skills (more skills for more experienced facilitators)
            $skillCount = min(3 + $yearsExp, 8); // 3-8 skills based on experience
            $userSkills = collect($allSkills)->random(max(3, $skillCount));
            foreach ($userSkills as $skillName) {
                $skill = Skill::where('skillName', $skillName)->first();
                $user->skills()->attach($skill->skillID);
            }

            $this->facilitatorsList[] = $user;
        }
    }

    /**
     * Create events matching Falcon Kingdom Academy programs
     */
    private function createEvents(): void
    {
        $selangorSchools = [
            'SMK Seksyen 7 Shah Alam',
            'SM Sains Selangor',
            'SMK USJ 4 Subang Jaya',
            'SMK Taman Petaling',
            'SMK Bandar Sunway',
            'SMK Seafield',
            'SK Taman Melawati',
            'SMK Damansara Jaya',
            'SMK Sultan Abdul Samad',
            'SMK Assunta Petaling Jaya',
            'Kolej Vokasional Shah Alam',
            'SMK Bukit Jelutang',
            'Sekolah Menengah Agama Klang',
            'SMK Seri Hartamas'
        ];

        $holidayLocations = [
            'Cameron Highland Resort',
            'Pulau Pangkor Beach Resort',
            'Pulau Langkawi Waterfront',
            'Melaka Historical City'
        ];

        $eventTemplates = [
            // TE AM BUILDING
            ['category' => 'TEAM BUILDING', 'name' => 'Syarikat Korporat Training', 'days' => 2, 'quota' => 12, 'participants' => 50],
            ['category' => 'TEAM BUILDING', 'name' => 'LADAP Guru Professional Development', 'days' => 1, 'quota' => 8, 'participants' => 40],
            ['category' => 'TEAM BUILDING', 'name' => 'Universiti Leadership Workshop', 'days' => 3, 'quota' => 15, 'participants' => 80],
            ['category' => 'TEAM BUILDING', 'name' => 'Institut Perguruan Team Building', 'days' => 2, 'quota' => 10, 'participants' => 60],
            ['category' => 'TEAM BUILDING', 'name' => 'NGO Capacity Building', 'days' => 1, 'quota' => 8, 'participants' => 30],
            ['category' => 'TEAM BUILDING', 'name' => 'Business Talk & Networking', 'days' => 1, 'quota' => 6, 'participants' => 100],
            
            // TALK (Ceramah)
            ['category' => 'TALK', 'name' => 'Ceramah Ibubapa', 'days' => 0.25, 'quota' => 4, 'participants' => 120],
            ['category' => 'TALK', 'name' => 'Motivasi Remaja', 'days' => 0.25, 'quota' => 3, 'participants' => 200],
            ['category' => 'TALK', 'name' => 'Kepimpinan Pelajar', 'days' => 0.25, 'quota' => 4, 'participants' => 150],
            ['category' => 'TALK', 'name' => 'Pengurusan Kewangan Islam', 'days' => 0.5, 'quota' => 5, 'participants' => 80],
            ['category' => 'TALK', 'name' => 'Jati Diri & Sahsiah', 'days' => 0.25, 'quota' => 3, 'participants' => 100],
            ['category' => 'TALK', 'name' => 'Moral & Akhlak', 'days' => 0.25, 'quota' => 4, 'participants' => 90],
            
            // CAMP (Kem)
            ['category' => 'CAMP', 'name' => 'Kem Kepimpinan Pelajar', 'days' => 4, 'quota' => 18, 'participants' => 100],
            ['category' => 'CAMP', 'name' => 'PRS Training Camp', 'days' => 3, 'quota' => 15, 'participants' => 80],
            ['category' => 'CAMP', 'name' => 'Kem Pengawas Sekolah', 'days' => 3, 'quota' => 12, 'participants' => 70],
            ['category' => 'CAMP', 'name' => 'Kendiri Remaja Program', 'days' => 2, 'quota' => 10, 'participants' => 60],
            ['category' => 'CAMP', 'name' => 'Perkhemahan Tahunan', 'days' => 5, 'quota' => 20, 'participants' => 120],
            
            // WORKSHOP (Kursus/Bengkel)
            ['category' => 'WORKSHOP', 'name' => 'Kursus PRS Asas', 'days' => 2, 'quota' => 8, 'participants' => 40],
            ['category' => 'WORKSHOP', 'name' => 'Pengurusan Stress Workshop', 'days' => 1, 'quota' => 6, 'participants' => 30],
            ['category' => 'WORKSHOP', 'name' => 'Ikhtiar Hidup Skills', 'days' => 1, 'quota' => 7, 'participants' => 35],
            ['category' => 'WORKSHOP', 'name' => 'Seminar Subjek Belajar', 'days' => 1, 'quota' => 5, 'participants' => 50],
            ['category' => 'WORKSHOP', 'name' => '

Kemahiran Belajar Technique', 'days' => 1, 'quota' => 6, 'participants' => 40],
            ['category' => 'WORKSHOP', 'name' => 'Asas Facilitator Training', 'days' => 2, 'quota' => 10, 'participants' => 25],
            
            // HOLIDAY (Percutian) 
            ['category' => 'HOLIDAY', 'name' => 'Family Day Cameron Highland', 'days' => 3, 'quota' => 8, 'participants' => 50],
            ['category' => 'HOLIDAY', 'name' => 'Lawatan Sambil Belajar Pulau Pangkor', 'days' => 2, 'quota' => 6, 'participants' => 40],
            ['category' => 'HOLIDAY', 'name' => 'Rombongan Team Bonding Pulau Langkawi', 'days' => 4, 'quota' => 10, 'participants' => 60],
            ['category' => 'HOLIDAY', 'name' => 'Jelajah Gua Melaka', 'days' => 2, 'quota' => 7, 'participants' => 35],
        ];

        foreach ($eventTemplates as $template) {
            // Create events spread over the next 6 months
            $startDate = Carbon::now()->addDays(rand(-30, 180));
            $endDate = $startDate->copy()->addDays($template['days']);
            
            // Select venue based on category
            if ($template['category'] === 'HOLIDAY') {
                $venue = $holidayLocations[array_rand($holidayLocations)];
            } else {
                $venue = $selangorSchools[array_rand($selangorSchools)];
            }
            
            // Determine status
            $status = 'Upcoming';
            if ($startDate->isPast()) {
                if ($endDate->isPast()) {
                    $status = 'Completed';
                } else {
                    $status = 'Ongoing';
                }
            }
            
            $event = Event::create([
                'eventName' => $template['name'],
                'venue' => $venue,
                'eventDescription' => 'Program ' . $template['name'] . ' anjuran Falcon Kingdom Academy untuk pembangunan kemahiran dan pengalaman peserta.',
                'eventCategory' => $template['category'],
                'status' => $status,
                'quota' => $template['quota'],
                'startDateTime' => $startDate,
                'endDateTime' => $endDate,
                'totalParticipants' => $template['participants'],
                'remark' => 'Seeded data'
            ]);
            
            $this->events[] = $event;
        }
    }

    /**
     * Create assignments with attendance data
     */
    private function createAssignmentsWithAttendance(): void
    {
        foreach ($this->events as $event) {
            $rule = EventRule::where('eventCategory', $event->eventCategory)->first();
            if (!$rule) continue;
            
            // Get expanded skill requirements
            $requiredSkills = is_array($rule->requiredSkill) ? $rule->requiredSkill : json_decode($rule->requiredSkill, true);
            $expandedSkills = $this->expandSkills($requiredSkills ?? []);
            
            // Find matching facilitators
            $candidates = User::where('role', 'facilitator')
                ->whereHas('skills', function ($q) use ($expandedSkills) {
                    $q->whereIn('skillName', $expandedSkills);
                })
                ->get()
                ->filter(function ($user) use ($rule) {
                    $exp = intval($user->experience);
                    return $exp >= $rule->minExperience && ($user->averageRating >= $rule->minRating || $user->averageRating == 0);
                });
            
            // If not enough, add more facilitators
            if ($candidates->count() < $event->quota) {
                $additional = User::where('role', 'facilitator')
                    ->whereNotIn('userID', $candidates->pluck('userID'))
                    ->take($event->quota - $candidates->count())
                    ->get();
                $candidates = $candidates->merge($additional);
            }
            
            // Assign facilitators
            $assigned = $candidates->shuffle()->take($event->quota);
            
            foreach ($assigned as $facilitator) {
                $assignmentStatus = $event->status === 'Completed' ? 'accepted' : (rand(1, 10) > 2 ? 'accepted' : 'pending');
                
                // Attendance data (only for accepted and past/ongoing events)
                $attendanceStatus = 'pending'; // Default for all
                $clockIn = null;
                $clockOut = null;
                $imageProof = null;
                
                if ($assignmentStatus === 'accepted' && ($event->status === 'Completed' || $event->status === 'Ongoing')) {
                    // 80% verified, 15% pending, 5% rejected
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $attendanceStatus = 'verified';
                    } elseif ($rand <= 95) {
                        $attendanceStatus = 'pending';
                    } else {
                        $attendanceStatus = 'rejected';
                    }
                    
                    $clockIn = $event->startDateTime;
                    $clockOut = $event->endDateTime;
                    $imageProof = 'attendance_proof.jpg';
                }
                
                Assignment::create([
                    'eventID' => $event->eventID,
                   'userID' => $facilitator->userID,
                    'status' => $assignmentStatus,
                    'dateAssigned' => now()->subDays(rand(1, 30)),
                    'clockInTime' => $clockIn,
                    'clockOutTime' => $clockOut,
                    'attendanceStatus' => $attendanceStatus,
                    'imageProof' => $imageProof
                ]);
            }
        }
    }

    /**
     * Create performance reviews only between co-facilitators
     */
    private function createPerformanceReviews(): void
    {
        $completedEvents = Event::where('status', 'Completed')->get();
        
        $positiveComments = [
            'Excellent leadership skills and very professional',
            'Great speaker, very engaging with participants',
            'Knowledgeable and well-prepared for all sessions',
            'Good at team management and coordination',
            'Patient and helpful throughout the program',
            'Very organized and punctual',
            'Strong religious knowledge and guidance',
            'Motivating and inspiring facilitator'
        ];
        
        $negativeComments = [
            'Sometimes arrives late to sessions',
            'Could be more organized with materials',
            'Needs improvement in time management',
            'Unprepared for some activities',
            'Lazy attitude during certain sessions',
            'Missing some important briefings'
        ];
        
        foreach ($completedEvents as $event) {
            $facilitators = $event->assignments()
                ->where('status', 'accepted')
                ->pluck('userID')
                ->toArray();
            
            if (count($facilitators) < 2) continue;
            
            // Each facilitator reviews 40% of co-facilitators
            foreach ($facilitators as $reviewerID) {
                $targets = array_diff($facilitators, [$reviewerID]);
                $reviewCount = max(1, (int)(count($targets) * 0.4));
                $selectedTargets = array_rand(array_flip($targets), min($reviewCount, count($targets)));
                
                if (!is_array($selectedTargets)) {
                    $selectedTargets = [$selectedTargets];
                }
                
                foreach ($selectedTargets as $targetID) {
                    $rating = rand(3, 5);
                    $comment = $rating >= 4 
                        ? $positiveComments[array_rand($positiveComments)]
                        : $negativeComments[array_rand($negativeComments)];
                    
                    PerformanceReview::create([
                        'userID' => $targetID,
                        'rating' => $rating,
                        'comments' => $comment,
                        'created_at' => $event->endDateTime->addDays(rand(1, 7))
                    ]);
                }
            }
        }
    }

    /**
     * Update average ratings based on reviews
     */
    private function updateAverageRatings(): void
    {
        foreach ($this->facilitatorsList as $facilitator) {
            $avgRating = PerformanceReview::where('userID', $facilitator->userID)
                ->avg('rating');
            
            $facilitator->averageRating = $avgRating ? round($avgRating, 1) : 0;
            $facilitator->save();
        }
    }

    /**
     * Create leave requests with Feb 11, 2026 conflicts
     */
    private function createLeaveRequests(): void
    {
        $presentationDate = Carbon::create(2026, 2, 11);
        
        // 3-5 facilitators with leave on presentation day
        $presentationLeaves = collect($this->facilitatorsList)
            ->random(rand(3, 5));
        
        foreach ($presentationLeaves as $facilitator) {
            $days = rand(1, 3);
            Leave::create([
                'userID' => $facilitator->userID,
                'startDate' => $presentationDate,
                'endDate' => $presentationDate->copy()->addDays($days),
                'reason' => ['Family Emergency', 'Medical Leave', 'Personal Matters'][array_rand(['Family Emergency', 'Medical Leave', 'Personal Matters'])],
                'status' => rand(1, 10) > 3 ? 'approved' : 'pending'
            ]);
        }
        
        // 7-12 other leave requests
        $otherLeaves = collect($this->facilitatorsList)
            ->diff($presentationLeaves)
            ->random(rand(7, 12));
        
        foreach ($otherLeaves as $facilitator) {
            $startDate = Carbon::now()->addDays(rand(-30, 90));
            $days = rand(1, 5);
            Leave::create([
                'userID' => $facilitator->userID,
                'startDate' => $startDate,
                'endDate' => $startDate->copy()->addDays($days),
                'reason' => ['Sick Leave', 'Annual Leave', 'Personal Matters', 'Family Emergency'][array_rand(['Sick Leave', 'Annual Leave', 'Personal Matters', 'Family Emergency'])],
                'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])]
            ]);
        }
    }

    /**
     * Create payment records for verified attendance
     */
    private function createPayments(): void
    {
        $verifiedAssignments = Assignment::where('attendanceStatus', 'verified')->get();
        
        foreach ($verifiedAssignments as $assignment) {
            $event = Event::find($assignment->eventID);
            
            // 70% paid, 20% approved, 10% pending
            $rand = rand(1, 100);
            if ($rand <= 70) {
                $paymentStatus = 'paid';
                $paymentProof = 'payment_proof_' . $assignment->assignmentID . '.jpg';
            } elseif ($rand <= 90) {
                $paymentStatus = 'approved';
                $paymentProof = null;
            } else {
                $paymentStatus = 'pending';
                $paymentProof = null;
            }
            
            Payment::create([
                'assignmentID' => $assignment->assignmentID,
                'title' => 'Event Completion Fee: ' . ($event ? $event->eventName : 'Unknown'),
                'amount' => rand(100, 500),
                'paymentDate' => $assignment->updated_at,
                'paymentStatus' => $paymentStatus,
                'paymentProof' => $paymentProof
            ]);
        }
    }
}
