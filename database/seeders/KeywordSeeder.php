<?php

namespace Database\Seeders;

use App\Models\keyword;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     // List of keywords related to different professions
     $keywords = [
        // Keywords for Translator
        ['word' => 'Bilingual'],
        ['word' => 'Translation'],
        ['word' => 'Localization'],
        ['word' => 'Interpretation'],
        ['word' => 'Fluency'],

        // Keywords for Lawyer
        ['word' => 'Legal'],
        ['word' => 'Litigation'],
        ['word' => 'Counsel'],
        ['word' => 'Advocacy'],
        ['word' => 'Compliance'],

        // Keywords for Accountant
        ['word' => 'Accounting'],
        ['word' => 'Auditing'],
        ['word' => 'Tax'],
        ['word' => 'Financial'],
        ['word' => 'Bookkeeping'],

        // Keywords for Human Resources
        ['word' => 'Recruitment'],
        ['word' => 'Employee'],
        ['word' => 'Onboarding'],
        ['word' => 'Training'],
        ['word' => 'Payroll']
    ];

    // Insert each keyword into the database
    foreach ($keywords as $keyword) {
        keyword::create($keyword);
    }
    }
}
