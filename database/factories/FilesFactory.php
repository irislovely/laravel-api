<?php

namespace Database\Factories;

use App\Models\Files;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FilesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Files::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(20),
            'filepath' => '/storage/uploads/'.Str::random(10).'.jpg',
            'thumb' => '/storage/uploads/'.Str::random(10).'.jpg',
            'type' => 'jpg'

        ];
    }
}
