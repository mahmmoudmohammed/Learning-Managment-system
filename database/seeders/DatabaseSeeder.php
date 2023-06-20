<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionTableSeeder::class,
            SuperAdminSeeder::class,
            UserSeeder::class,
            CatogerySeeder::class,
            TopicSeeder::class,
            CertificateSeeder::class,
            QuestionSeeder::class,
            AnswerSeeder::class
        ]);
    }
}
