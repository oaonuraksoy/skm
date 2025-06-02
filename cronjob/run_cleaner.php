<?php
require_once 'JsonCleaner.php';

try {
    $cleaner = new JsonCleaner();
    
    // Clean meal.json and save to database
    $mealOutput = $cleaner->cleanJson('meal');
    echo "Cleaned meal data saved to: $mealOutput\n";
    
    // Clean kavramlar_sifatlar.json and save to database
    $kavramOutput = $cleaner->cleanJson('kavramlar_sifatlar');
    echo "Cleaned kavramlar_sifatlar data saved to: $kavramOutput\n";
    
    // Update count stats after all updates are complete
    $cleaner->updateCountStats();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 