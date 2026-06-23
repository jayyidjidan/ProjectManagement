<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skills;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Laravel',
            'PHP',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue.js',
            'Flutter',
            'Dart',
            'MySQL',
            'MongoDB',
            'UI/UX Design',
            'Figma',
            'Project Management',
            'Scrum',
            'DevOps',
            'Docker',
            'AWS',
            'Testing',
            'Quality Assurance',
            'API Development',
        ];

        foreach ($skills as $skill) {
            Skills::firstOrCreate([
                'skill_name' => $skill
            ]);
        }
    }
}