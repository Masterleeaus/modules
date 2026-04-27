<?php

namespace Modules\HRCore\database\factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HRCore\app\Models\CompensatoryOff;

class CompensatoryOffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompensatoryOff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workedDate = $this->faker->dateTimeBetween('-30 days', '-1 days');
        $expiryDate = Carbon::parse($workedDate)->addDays(90);

        $statuses = ['pending', 'approved', 'rejected'];
        $status = $this->faker->randomElement($statuses);

        $data = [
            'user_id' => User::factory(),
            'worked_date' => $workedDate->format('Y-m-d'),
            'hours_worked' => $this->faker->randomFloat(2, 4, 12),
            'comp_off_days' => 1,
            'reason' => $this->faker->sentence(10),
            'status' => $status,
            'expiry_date' => $expiryDate->format('Y-m-d'),
            'is_used' => false,
            'created_at' => Carbon::parse($workedDate)->addDays(rand(1, 3)),
            'updated_at' => Carbon::parse($workedDate)->addDays(rand(1, 3)),
        ];

        // Add status-specific fields
        if ($status === 'approved') {
            $data['approved_by_id'] = User::factory();
            $data['approved_at'] = Carbon::parse($workedDate)->addDays(rand(1, 5));
        } elseif ($status === 'rejected') {
            $data['approved_by_id'] = User::factory();
            $data['approved_at'] = Carbon::parse($workedDate)->addDays(rand(1, 5));
            $data['approval_notes'] = $this->faker->sentence(8);
        }

        return $data;
    }

    /**
     * Indicate that the comp off is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by_id' => null,
            'approved_at' => null,
            'approval_notes' => null,
            'is_used' => false,
            'used_date' => null,
        ]);
    }

    /**
     * Indicate that the comp off is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by_id' => User::factory(),
            'approved_at' => now(),
            'approval_notes' => null,
            'is_used' => false,
        ]);
    }

    /**
     * Indicate that the comp off is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by_id' => User::factory(),
            'approved_at' => now(),
            'approval_notes' => $this->faker->sentence(8),
            'is_used' => false,
            'used_date' => null,
        ]);
    }

    /**
     * Indicate that the comp off is used.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by_id' => User::factory(),
            'approved_at' => now()->subDays(10),
            'is_used' => true,
            'used_date' => now()->format('Y-m-d'),
            'approval_notes' => null,
        ]);
    }

    /**
     * Indicate that the comp off is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'expiry_date' => now()->subDays(1)->format('Y-m-d'),
            'is_used' => false,
        ]);
    }
}
