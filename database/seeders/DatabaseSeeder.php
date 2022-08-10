<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Center;
use App\Models\Formation;
use App\Models\FormationTeacher;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

         \App\Models\User::factory()->create([
             'id' => 1,
             'email' => 'david@email.com',
             'password' => Hash::make('hola.1236'),
             'identification_type' => 'Cedula de ciudadania',
             'identification_number' => '1234567890',
             'birth_date' => '2020-08-20',
             'user_type' => 'ADMIN',
         ]);


        Student::factory(3)->create();

        Center::factory(3)->create();

        FormationTeacher::factory(6)->create();
    }
}
