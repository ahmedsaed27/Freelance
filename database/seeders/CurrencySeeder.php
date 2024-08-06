<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar'],
            ['name' => 'Euro'],
            ['name' => 'British Pound'],
            ['name' => 'Japanese Yen'],
            ['name' => 'Australian Dollar'],
            ['name' => 'Canadian Dollar'],
            ['name' => 'Swiss Franc'],
            ['name' => 'Chinese Yuan'],
            ['name' => 'Swedish Krona'],
            ['name' => 'New Zealand Dollar'],
            ['name' => 'Mexican Peso'],
            ['name' => 'Singapore Dollar'],
            ['name' => 'Hong Kong Dollar'],
            ['name' => 'Norwegian Krone'],
            ['name' => 'South Korean Won'],
            ['name' => 'Turkish Lira'],
            ['name' => 'Indian Rupee'],
            ['name' => 'Brazilian Real'],
            ['name' => 'South African Rand'],
            ['name' => 'Philippine Peso']
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
