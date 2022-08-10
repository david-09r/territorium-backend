<?php

namespace Database\Factories;

use App\Models\Formation;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormationTeacher>
 */
class FormationTeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'formation_id' => Formation::factory()->create(),
            'teacher_id' => Teacher::factory()->create()
        ];
    }
}
