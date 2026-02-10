<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealFacilitatorSeeder extends Seeder
{
    public function run()
    {
        $facilitators = [
            ["name" => "Muhammad Qawiem Mustaqim bin Kamrizal", "phone" => "1124267973", "password" => "12346578"],
            ["name" => "Muhamad Zulhairie in Iskandar", "phone" => "1171177029", "password" => "12346578"],
            ["name" => "Muhammad Adam Faris bin Mohd Nizam", "phone" => "192026474", "password" => "12346578"],
            ["name" => "Muhammad Shafiq Shauqi bin Mohd Kamar", "phone" => "182719341", "password" => "12346578"],
            ["name" => "Muhammad Akmal bin Mohd Desa", "phone" => "1121023490", "password" => "12346578"],
            ["name" => "Nur Syahirah binti Ahmad", "phone" => "1123791738", "password" => "12346578"],
            ["name" => "Muhammad Azrul Hakim bin Azhari", "phone" => "1121380759", "password" => "12346578"],
            ["name" => "Zamzikri bin Zamzuri", "phone" => "104055638", "password" => "12346578"],
            ["name" => "Muhammad Asyraf bin Ahmad Sahimi", "phone" => "147203480", "password" => "12346578"],
            ["name" => "Muhammad Adibhakimi bin Mohd Khairie", "phone" => "1111587908", "password" => "12346578"],
            ["name" => "Muhammad Adzri bin Azhar", "phone" => "107104759", "password" => "12346578"],
            ["name" => "Muhammad Aiman Haziq ", "phone" => "198821895", "password" => "12346578"],
            ["name" => "Ajwad Izzlan", "phone" => "136725047", "password" => "12346578"],
            ["name" => "Iskandar Fahmi bin Abdul Halim", "phone" => "108237806", "password" => "12346578"],
            ["name" => "Muhammad Alif bin Aminuddin", "phone" => "1260693034", "password" => "12346578"],
            ["name" => "Muhammad Amir Haziq", "phone" => "103147374", "password" => "12346578"],
            ["name" => "Amirul Aiman", "phone" => "192773645", "password" => "12346578"],
            ["name" => "Arief Fudhail", "phone" => "182757745", "password" => "12346578"],
            ["name" => "Ahmad Azamuddin bin Musa", "phone" => "182427339", "password" => "12346578"],
            ["name" => "Muhammad Danial bin Hamzah", "phone" => "137974275", "password" => "12346578"],
            ["name" => "Muhammad Firdaus bin Udin", "phone" => "183299846", "password" => "12346578"],
            ["name" => "Muhammad Dinie Rusydi", "phone" => "134810957", "password" => "12346578"],
            ["name" => "Zulhisyam bin Iskandar", "phone" => "172853921", "password" => "12346578"],
            ["name" => "Muhammad Edham bin Shamsuri", "phone" => "169131612", "password" => "12346578"],
            ["name" => "Haikal Akmal bin Halim", "phone" => "1128117541", "password" => "12346578"],
            ["name" => "Muhammad Haikal Afiq", "phone" => "134520304", "password" => "12346578"],
            ["name" => "Rariqul Aiman bin Zuha", "phone" => "188723577", "password" => "12346578"],
            ["name" => "Ezuan bin Zakaria", "phone" => "137573776", "password" => "12346578"],
            ["name" => "Fahmi bin Nordin", "phone" => "182846068", "password" => "12346578"],
            ["name" => "Muhammad Fared Hakimi", "phone" => "1139979710", "password" => "12346578"],
            ["name" => "Ammar Firdaus", "phone" => "133280272", "password" => "12346578"],
            ["name" => "Muhammad Hafiy Nurmuzammil", "phone" => "198404125", "password" => "12346578"],
            ["name" => "Muhammad Haikal bin Hashim", "phone" => "103575383", "password" => "12346578"],
            ["name" => "Haiqal Farez Zuhairy bin Azhari", "phone" => "182957105", "password" => "12346578"],
            ["name" => "Muhammad Zulhairie bin Iskandar", "phone" => "1171177029", "password" => "12346578"],
            ["name" => "Iman Hanafi bin Eddy Kesumajaya", "phone" => "173067560", "password" => "12346578"],
            ["name" => "Muhammad Irfan", "phone" => "1165236979", "password" => "12346578"],
            ["name" => "Muhammad Izrin Syafiq bin Mohd Esra", "phone" => "198626878", "password" => "12346578"],
            ["name" => "Muhammad Zulhelmi bin Mohd Salam", "phone" => "146980845", "password" => "12346578"],
            ["name" => "Muhammad Haziq bin Mohd Aris", "phone" => "146175548", "password" => "12346578"],
            ["name" => "Nafisah Nazirah binti Nazri", "phone" => "166211730", "password" => "12346578"],
            ["name" => "Nazarul Hakimie bin Mohd Nazim", "phone" => "182265782", "password" => "12346578"],
            ["name" => "Farihin Nublan bin Rosli", "phone" => "175862371", "password" => "12346578"],
            ["name" => "Muhammad Saeeb bin Subre", "phone" => "168766547", "password" => "12346578"],
            ["name" => "Siti Asmidar bin Serkan", "phone" => "172591371", "password" => "12346578"],
            ["name" => "Noor Syahirah binti Ahmad", "phone" => "1123791738", "password" => "12346578"],
            ["name" => "Muhammad Zailan bin Zailani", "phone" => "182339426", "password" => "12346578"],
            ["name" => "Muhammad Adam Irfan", "phone" => "178784038", "password" => "12346578"],
            ["name" => "Nur Nabilah binti Mohd Sharif", "phone" => "1139036137", "password" => "12346578"],
            ["name" => "Mahadzir bin Mohamad", "phone" => "102330085", "password" => "12346578"]
        ];

        $skills = [
            'Speaking', 'Medic', 'Leadership', 'Facilitating', 'Public Speaking', 
            'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 
            'Logistics', 'Teaching', 'Archery', 'Time Management', 
            'Organization Management', 'Swimming'
        ];

        foreach ($facilitators as $data) {
            $firstName = explode(' ', trim($data['name']))[0];
            $email = strtolower(str_replace(' ', '', $firstName)) . rand(100, 999) . '@falconwfm.com';
            
            // Check if user exists to avoid duplicates
            if (User::where('email', $email)->exists()) continue;

            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'facilitator',
                
                'bankName' => 'Maybank',
                'bankAccountNumber' => rand(1000000000, 9999999999),
                'phoneNumber' => $data['phone'],
                'experience' => rand(0, 6),
                'joinDate' => now()->subMonths(rand(1, 48)),
                'averageRating' => rand(35, 50) / 10
            ]);

            // Assign Random Skills
            $randomSkills = collect($skills)->random(rand(3, 6));
            foreach ($randomSkills as $skillName) {
                $skill = \App\Models\Skill::firstOrCreate(['skillName' => $skillName]);
                $user->skills()->attach($skill->skillID);
            }
        }
    }
}
