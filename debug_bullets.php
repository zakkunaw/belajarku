<?php

$text = "* Task 1: Setup\n* Task 2: Learn";

echo "Original: " . json_encode($text) . "\n";

// Test the regex
$result = preg_replace('/^\*\s+(.+)$/m', '• $1', $text);
echo "After bullet regex: " . json_encode($result) . "\n";

// Test with different spacing
$text2 = "* Task 1: Setup\n* Task 2: Learn";
$result2 = preg_replace('/^\*\s(.+)$/m', '• $1', $text2);
echo "With single space regex: " . json_encode($result2) . "\n";
