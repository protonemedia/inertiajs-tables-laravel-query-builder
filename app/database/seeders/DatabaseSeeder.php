<?php

namespace Database\Seeders;

use Database\Factories\CompanyFactory;
use Database\Factories\UserFactory;
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
        UserFactory::new()->count(100)->create();
        CompanyFactory::new()->count(100)->create();
    }
}
