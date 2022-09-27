<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentTask>
 */
class StudentTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'task_id' => Task::factory(),
            'student_id' => Student::factory(),
            'answer' => $this->faker->title
        ];
    }
}
