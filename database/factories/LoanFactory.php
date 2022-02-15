<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $loan_term = $this->faker->randomDigit();
        $loan_amount = $this->faker->numberBetween($min = 1000, $max = 9000);
        return [
            'user_id' => User::factory(),
            'loan_term' => $loan_term,
            'loan_term_remaining' => $loan_term,
            'amount_required' => $loan_amount,
            'amount_balance' => $loan_amount,
            'loan_start_date' => $this->faker->date($format = 'Y-m-d')
        ];
    }
}
