<?php

namespace Database\Factories;

use App\Models\Formation;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormationStudent>
 */
class FormationStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'formation_id' => Formation::factory(),
            'student_id' => Student::factory()
        ];
    }
}
