<?php

namespace Database\Factories;

use App\Models\Concert;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ConcertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Concert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'The Red Cord',
            'subtitle' => 'with Animosity and lethargy',
            'date' => Carbon::parse('+2 weeks'),
            'ticket_price' => 3250,
            'venu' => 'The emaple Theatre',
            'venu_address' => '123 Example Lane',
            'city' => 'Burlington',
            'state' => 'ON',
            'zip' => 'L89R7T',
            'additional' => 'Some sample additional information',
        ];
    }

    /**
     * Indicate that the concert is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
            'published_at' => Carbon::parse('-1 week'),
            ];
        });
    }

    /**
     * Indicate that the concert is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
            'published_at' => null,
            ];
        });
    }
}
