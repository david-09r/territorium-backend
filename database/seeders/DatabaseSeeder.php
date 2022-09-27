<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Models\Center;
use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Publication;
use App\Models\Student;
use App\Models\StudentTask;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\User;
use App\Utils\Enum\EnumRoleOrPositions;
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

         $userStudent = User::factory()->create([
             'user_type' => EnumRoleOrPositions::STUDENT
         ]);

         $userTeacher = User::factory()->create([
             'user_type' => EnumRoleOrPositions::TEACHER
         ]);

         $student = Student::factory()->create([
             'user_id' => $userStudent->id
         ]);

         $teacher = Teacher::factory()->create([
             'user_id' => $userTeacher->id
         ]);

         $formation = Formation::factory()->create();

         FormationTeacher::factory()->create([
             'formation_id' => $formation->id,
             'teacher_id' => $teacher->id
         ]);

         FormationStudent::factory()->create([
             'formation_id' => $formation->id,
             'student_id' => $student->id
         ]);

         $task = Task::factory()->create();

         $publication = Publication::factory()->create([
             'formation_id' => $formation->id
         ]);

         StudentTask::factory()->create([
             'task_id' => $task->id,
             'student_id' => $student->id
         ]);
    }
}
