<?php

require_once __DIR__ . '/app/Helpers/GeminiResponseFormatter.php';

use App\Helpers\GeminiResponseFormatter;

// Test cases for the formatter
$testCases = [
    [
        'input' => '** Hari 1 ** - Belajar dasar Laravel',
        'description' => 'Bold text with spaces'
    ],
    [
        'input' => '**Minggu 1** - Setup environment',
        'description' => 'Bold text without spaces'
    ],
    [
        'input' => '*** Week 1 *** - Advanced concepts',
        'description' => 'Triple asterisk pattern'
    ],
    [
        'input' => '* Task 1: Setup environment\n* Task 2: Learn basics',
        'description' => 'Bullet points with asterisks'
    ],
    [
        'input' => '## Week 1\n### Day 1\n**Bold text** and normal text',
        'description' => 'Mixed markdown patterns'
    ]
];

echo "=== Testing GeminiResponseFormatter ===\n\n";

foreach ($testCases as $index => $testCase) {
    echo "Test " . ($index + 1) . ": " . $testCase['description'] . "\n";
    echo "Input: " . json_encode($testCase['input']) . "\n";
    echo "Output: " . json_encode(GeminiResponseFormatter::formatResponse($testCase['input'])) . "\n";
    echo str_repeat("-", 50) . "\n";
}

echo "\n=== Testing Roadmap Parsing ===\n\n";

$roadmapExample = "
** Week 1 ** - Laravel Fundamentals
* Setup development environment
* Learn MVC architecture
* Create first controller

** Week 2 ** - Database Integration  
* Learn Eloquent ORM
* Create migrations
* Build relationships
";

echo "Roadmap Input:\n" . $roadmapExample . "\n\n";

$parsed = GeminiResponseFormatter::parseRoadmapResponse($roadmapExample);
echo "Parsed Output:\n" . json_encode($parsed, JSON_PRETTY_PRINT) . "\n";
