<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an array of data to seed
        $professions = [
            ['title' => 'Translator', 'description' => 'Responsible for translating documents and conversations.'],
            ['title' => 'Lawyer', 'description' => 'Provides legal advice and representation.'],
            ['title' => 'Accountant', 'description' => 'Handles financial records and tax filings.'],
            ['title' => 'Human Resources', 'description' => 'Manages employee relations and company policies.'],
        ];

        // Insert each profession into the database
        foreach ($professions as $profession) {
            Type::create($profession);
        }
    }
}
