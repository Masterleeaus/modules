<?php

namespace Modules\Asset\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Asset\Entities\Asset;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $faker = $this->faker;

        return [
            'name' => ucwords(implode(' ', $faker->words(2))),
            'serial_number' => ($faker->numberBetween(0, 5) == 0) ? null : $faker->swiftBicNumber,
            'description' => ($faker->numberBetween(0, 2) == 0) ? null : $faker->text(100),
            'status' => $faker->randomElement(['available', 'non-functional', 'lent']),
            'created_at' => now(),
        ];
    }
}
