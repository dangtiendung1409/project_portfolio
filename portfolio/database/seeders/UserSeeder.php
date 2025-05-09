<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = ['Hanoi, Vietnam', 'Ho Chi Minh City, Vietnam', 'Da Nang, Vietnam', 'New York, USA', 'Los Angeles, USA', 'London, UK', 'Paris, France', 'Tokyo, Japan'];
        $firstNames = ['John', 'Jane', 'Mike', 'Emily', 'Chris', 'Anna', 'Tom', 'Sophia', 'David', 'Olivia'];
        $lastNames = ['Smith', 'Johnson', 'Brown', 'Williams', 'Jones', 'Miller', 'Davis', 'Garcia', 'Martinez', 'Wilson'];
        $profilePictures = range(1, 100);
        $coverPhotos = range(1, 20);
        $bios = [
            'Passionate about technology and innovation.',
            'Avid traveler and photographer.',
            'Food lover who enjoys cooking and trying new cuisines.',
            'Fitness enthusiast and yoga practitioner.',
            'Gamer and esports fan.',
            'Music addict who loves attending live concerts.',
            'Bookworm who enjoys historical fiction.',
            'Nature lover and hiking explorer.',
            'Tech geek with a love for AI and machine learning.',
            'Entrepreneur with a startup mindset.',
            'Coffee enthusiast who enjoys discovering new cafes.',
            'Cyclist and marathon runner.',
            'Art lover and aspiring painter.',
            'Passionate about space exploration and astronomy.',
            'DIY enthusiast who loves home improvement projects.',
            'Lifelong learner exploring different cultures.',
            'Fan of classic cinema and vintage fashion.',
            'Animal lover and rescue volunteer.',
            'Aspiring writer working on a novel.',
            'Tea connoisseur who enjoys quiet evenings with a book.'
        ];

        shuffle($profilePictures); // Trộn ngẫu nhiên danh sách ảnh đại diện
        shuffle($coverPhotos); // Trộn ngẫu nhiên danh sách ảnh bìa

        $users = [
            [
                'username' => 'admin',
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => '/images/avatars/user_0.jpeg',
                'location' => 'Hanoi, Vietnam',
                'cover_photo' => '/images/covers/covers_0.jpeg',
                'bio' => 'I love animals and beautiful scenery.',
                'created_at' => now(),
                'role_id' => 1,
            ]
        ];

        $usedUsernames = ['admin'];
        $usedEmails = ['admin@gmail.com'];

        for ($i = 0; $i < 200; $i++) {
            do {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $username = strtolower($firstName . '_' . $lastName . rand(100, 999));
            } while (in_array($username, $usedUsernames));

            do {
                $email = strtolower($firstName . '.' . $lastName . rand(100, 999) . '@gmail.com');
            } while (in_array($email, $usedEmails));

            $usedUsernames[] = $username;
            $usedEmails[] = $email;

            // Lấy ngẫu nhiên ảnh đại diện, nếu hết danh sách thì lấy lại từ đầu
            if (empty($profilePictures)) {
                $profilePictures = range(1, 100);
                shuffle($profilePictures);
            }
            $profilePicture = array_pop($profilePictures);

            // Lấy ngẫu nhiên ảnh bìa, nếu hết danh sách thì lấy lại từ đầu
            if (empty($coverPhotos)) {
                $coverPhotos = range(1, 20);
                shuffle($coverPhotos);
            }
            $coverPhoto = array_pop($coverPhotos);

            $users[] = [
                'username' => $username,
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'password' => Hash::make('12345'),
                'profile_picture' => "/images/avatars/user_{$profilePicture}.jpeg",
                'location' => $locations[array_rand($locations)],
                'cover_photo' => "/images/covers/covers_{$coverPhoto}.jpeg",
                'bio' => $bios[array_rand($bios)],
                'created_at' => now(),
                'role_id' => 2,
            ];
        }

        DB::table('users')->insert($users);
    }
}
