<?php
// database/factories/FriendshipFactory.php

use App\Models\Friendship;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    protected $model = Friendship::class;

    public function definition()
    {
        return [
            'status' => 'Emparejado',
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}