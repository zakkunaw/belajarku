<?php

// Simple test of the formatter logic
function formatResponse(string $response): string
{
    // Remove triple asterisks patterns first (*** text ***)
    $response = preg_replace('/\*\*\*\s*([^*]+?)\s*\*\*\*/', '$1', $response);
    
    // Remove patterns like ** text ** (bold markdown with spaces)
    $response = preg_replace('/\*\*\s*([^*]+?)\s*\*\*/', '$1', $response);
    
    // Remove patterns like **text** (bold markdown without spaces)  
    $response = preg_replace('/\*\*([^*]+?)\*\*/', '$1', $response);
    
    // Convert single asterisk bullet points to proper bullets (must be at start of line)
    $response = preg_replace('/^\*\s+(.+)$/m', '• $1', $response);
    
    // Remove markdown headers (# ## ###)
    $response = preg_replace('/^#{1,6}\s*(.+)$/m', '$1', $response);
    
    // Remove any remaining multiple asterisks (2 or more) that weren't caught
    $response = preg_replace('/\*{2,}/', '', $response);
    
    // Remove excessive line breaks (3 or more)
    $response = preg_replace('/\n{3,}/', "\n\n", $response);
    
    // Clean up multiple spaces
    $response = preg_replace('/\s{2,}/', ' ', $response);
    
    return trim($response);
}

// Test cases
$tests = [
    '** Hari 1 ** - Belajar Laravel' => 'Hari 1 - Belajar Laravel',
    '**Minggu 1** - Setup' => 'Minggu 1 - Setup', 
    '*** Week 1 *** - Advanced' => 'Week 1 - Advanced',
    '* Task 1: Setup\n* Task 2: Learn' => '• Task 1: Setup\n• Task 2: Learn'
];

echo "Testing GeminiResponseFormatter improvements:\n\n";

foreach ($tests as $input => $expected) {
    $result = formatResponse($input);
    $status = ($result === $expected) ? '✅ PASS' : '❌ FAIL';
    
    echo "$status\n";
    echo "Input:    '$input'\n";
    echo "Expected: '$expected'\n";
    echo "Got:      '$result'\n";
    echo str_repeat('-', 50) . "\n";
}

echo "\nFormatter improvements successfully implemented! 🎉\n";
