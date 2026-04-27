<?php

namespace Modules\WorksuiteWorkOrders\Database\Factories;

use Modules\WorksuiteWorkOrders\Entities\ServicePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePartFactory extends Factory
{
    protected $model = ServicePart::class;

    public function definition(): array
    {
        return [
            'sku' => 'PRT-'.strtoupper($this->faker->bothify('???###')),
            'name' => $this->faker->words(2, true),
            'sale_price' => $this->faker->randomFloat(2, 10, 250),
        ];
    }
}
