<?php

use App\Models\Buddy;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuddyFactory extends Factory
{
    protected $model = Buddy::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'birth_date' => $this->faker->date,
            'address' => $this->faker->address,
            'emergency_contact' => $this->faker->name,
            'emergency_phone' => $this->faker->phoneNumber,
            'type' => $this->faker->randomElement(['buddy', 'peer_buddy']),
            'disability' => $this->faker->word,
        ];
    }

    public function buddy()
    {
        return $this->state([
            'type' => 'buddy',
            'disability' => $this->faker->word,
        ]);
    }

    public function peerBuddy()
    {
        return $this->state([
            'type' => 'peer_buddy',
            'disability' => null,
        ]);
    }
}