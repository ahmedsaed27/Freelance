<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     // List of skills related to different professions
     $skills = [
        // Skills for Translator
        ['title' => 'Language Proficiency', 'description' => 'Fluent in multiple languages, both written and spoken.'],
        ['title' => 'Cultural Knowledge', 'description' => 'Understanding of cultural nuances and contexts.'],
        ['title' => 'Attention to Detail', 'description' => 'Ability to accurately translate text without losing meaning.'],

        // Skills for Lawyer
        ['title' => 'Legal Research', 'description' => 'Proficient in conducting thorough legal research.'],
        ['title' => 'Litigation Skills', 'description' => 'Experienced in courtroom procedures and advocacy.'],
        ['title' => 'Negotiation Skills', 'description' => 'Skilled in negotiating settlements and agreements.'],

        // Skills for Accountant
        ['title' => 'Financial Reporting', 'description' => 'Expertise in preparing and analyzing financial statements.'],
        ['title' => 'Tax Preparation', 'description' => 'Knowledgeable in tax laws and filing requirements.'],
        ['title' => 'Auditing', 'description' => 'Experience in conducting internal and external audits.'],

        // Skills for Human Resources
        ['title' => 'Recruitment', 'description' => 'Experienced in talent acquisition and candidate screening.'],
        ['title' => 'Employee Relations', 'description' => 'Skilled in managing employee grievances and disputes.'],
        ['title' => 'Compliance', 'description' => 'Knowledge of labor laws and regulatory compliance.'],
    ];

    // Insert each skill into the database
    foreach ($skills as $skill) {
        Skill::create($skill);
    }
    }
}
