<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Facilitator;
use Illuminate\Support\Facades\DB;

class RecommendationVerificationSeeder extends Seeder
{
    public function run()
    {
        // Clear existing test data (Optional, but good for clean slate if running fresh)
        // User::truncate(); Event::truncate(); Facilitator::truncate(); // Be careful with truncate in production

        $domains = [
            'Tech' => ['PHP', 'Laravel', 'Java', 'Python', 'React', 'AWS', 'Docker', 'Kubernetes', 'Cybersecurity'],
            'Design' => ['UI/UX', 'Figma', 'Photoshop', 'Graphic Design', 'Prototyping', 'Branding'],
            'Business' => ['Leadership', 'public speaking', 'Project Management', 'Agile', 'Scrum', 'Finance', 'Marketing'],
            'Health' => ['First Aid', 'Mental Health', 'Nutrition', 'Yoga', 'Meditation'],
        ];

        // ----------------------------------------------------------------
        // 1. Create 20 Facilitators with diverse profiles
        // ----------------------------------------------------------------
        $facilitatorProfiles = [
            ['name' => 'Alice Backend', 'skills' => 'PHP Laravel MySQL Redis', 'bio' => 'Senior Backend Engineer specialized in scalable web apps.'],
            ['name' => 'Bob DevOps', 'skills' => 'AWS Docker Kubernetes Linux', 'bio' => 'DevOps specialist ensuring 99.9% uptime.'],
            ['name' => 'Charlie Design', 'skills' => 'Figma UI/UX Prototyping', 'bio' => 'Creative designer with a passion for user-centric interfaces.'],
            ['name' => 'Diana Lead', 'skills' => 'Leadership Agile Scrum Management', 'bio' => 'Project manager with 10 years of experience in agile teams.'],
            ['name' => 'Evan Security', 'skills' => 'Cybersecurity Python Ethial Hacking', 'bio' => 'Certified security expert.'],
            ['name' => 'Fiona Finance', 'skills' => 'Finance Accounting Excel', 'bio' => 'CPA with corporate finance background.'],
            ['name' => 'George Public', 'skills' => 'Public Speaking Communication Sales', 'bio' => 'Toastmaster and sales trainer.'],
            ['name' => 'Hannah Health', 'skills' => 'Yoga Meditation Mental Health', 'bio' => 'Wellness coach and yoga instructor.'],
            ['name' => 'Ian Java', 'skills' => 'Java Spring Microservices', 'bio' => 'Enterprise Java developer.'],
            ['name' => 'Julia Fullstack', 'skills' => 'React Node.js MongoDB Javascript', 'bio' => 'MERN stack developer.'],
            // Randoms
            ['name' => 'Kevin Junior', 'skills' => 'HTML CSS Basic JS', 'bio' => 'Junior web developer eager to learn.'],
            ['name' => 'Laura Marketing', 'skills' => 'SEO Marketing Content Writing', 'bio' => 'Digital marketing specialist.'],
            ['name' => 'Mike Data', 'skills' => 'Python Data Science Pandas', 'bio' => 'Data scientist loving big data.'],
            ['name' => 'Nina Network', 'skills' => 'Cisco Networking Security', 'bio' => 'Network engineer.'],
            ['name' => 'Oscar Ops', 'skills' => 'Linux Bash Scripting', 'bio' => 'Sysadmin and operations.'],
        ];

        foreach ($facilitatorProfiles as $profile) {
            $user = User::create([
                'name' => $profile['name'],
                'email' => strtolower(str_replace(' ', '.', $profile['name'])) . '@example.com',
                'password' => bcrypt('password'),
            ]);

            Facilitator::create([
                'user_id' => $user->id,
                'skills' => $profile['skills'],
                'experience' => $profile['bio'], // Use bio as experience for consistency
                'phone_number' => '555-' . rand(1000, 9999),
                'join_date' => now()->subMonths(rand(1, 24)),
            ]);
        }

        // ----------------------------------------------------------------
        // 2. Create 10 Various Events
        // ----------------------------------------------------------------
        $events = [
            ['name' => 'Adv. Laravel Bootcamp', 'skills' => 'PHP Laravel Backend'],
            ['name' => 'Cloud Summit 2025', 'skills' => 'AWS Cloud Docker'],
            ['name' => 'UI Design Workshop', 'skills' => 'Figma UI/UX Design'],
            ['name' => 'Agile Leadership', 'skills' => 'Leadership Agile Scrum'],
            ['name' => 'Cyber Defense Con', 'skills' => 'Cybersecurity Hacking'],
            ['name' => 'Corporate Wellness', 'skills' => 'Yoga Health Meditation'],
            ['name' => 'Java Enterprise', 'skills' => 'Java Spring'],
            ['name' => 'Frontend Masters', 'skills' => 'React Javascript Frontend'],
            ['name' => 'Financial Planning', 'skills' => 'Finance Excel'],
            ['name' => 'Public Speaking 101', 'skills' => 'Public Speaking Communication'],
        ];

        foreach ($events as $evt) {
            Event::create([
                'event_name' => $evt['name'],
                'venue' => 'Main Conference Hall',
                'required_skill_tag' => $evt['skills'],
                'start_date_time' => now()->addDays(rand(1, 30)),
                'quota' => 50
            ]);
        }

        $this->command->info("Seeded " . count($facilitatorProfiles) . " Facilitators and " . count($events) . " Events.");
    }
}
