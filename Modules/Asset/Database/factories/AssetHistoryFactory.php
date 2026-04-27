<?php

namespace Modules\Asset\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Asset\Entities\AssetHistory;

class AssetHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date_given' => $given = now(),
            'return_date' => (clone $given)->addDays(rand(1, 90)),
            'date_of_return' => (clone $given)->addDays(rand(1, 120)),
        ];

    }
}
