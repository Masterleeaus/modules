<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'invoice_id'      => Invoice::factory(),
            'recorded_by'     => User::factory(),
            'amount'          => fake()->randomFloat(2, 10, 500),
            'method'          => fake()->randomElement([
                Payment::METHOD_CASH,
                Payment::METHOD_CHECK,
                Payment::METHOD_CARD,
                Payment::METHOD_BANK_TRANSFER,
            ]),
            'status'          => 'completed',
            'paid_at'         => now(),
        ];
    }

    public function cash(): static
    {
        return $this->state(['method' => Payment::METHOD_CASH]);
    }

    public function check(?string $reference = null): static
    {
        return $this->state([
            'method'    => Payment::METHOD_CHECK,
            'reference' => $reference ?? fake()->numerify('####'),
        ]);
    }

    public function stripe(): static
    {
        return $this->state([
            'method'                    => Payment::METHOD_STRIPE,
            'stripe_payment_intent_id'  => 'pi_test_'.fake()->regexify('[A-Za-z0-9]{24}'),
        ]);
    }
}
