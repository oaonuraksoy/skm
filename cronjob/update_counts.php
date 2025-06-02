<?php
require_once 'Database.php';

try {
    $db = Database::getInstance();
    
    // Check if count_stats is empty
    $result = $db->query("SELECT COUNT(*) as count FROM count_stats");
    $count = $result->fetch()['count'];
    
    if ($count == 0) {
        // Insert initial record
        $db->query("INSERT INTO count_stats (total_kavram, total_meal) VALUES (0, 0)");
        echo "Initial record created.\n";
    }
    
    // Update count_stats
    $query = "UPDATE count_stats SET 
        total_kavram = (SELECT COUNT(*) FROM kavramlar_sifatlar),
        total_meal = (SELECT COUNT(*) FROM meal),
        last_updated = CURRENT_TIMESTAMP";
    
    $db->query($query);
    
    // Get current counts
    $result = $db->query("SELECT * FROM count_stats");
    $stats = $result->fetch();
    
    echo "Count stats updated successfully:\n";
    echo "Total Kavram: " . $stats['total_kavram'] . "\n";
    echo "Total Meal: " . $stats['total_meal'] . "\n";
    echo "Last Updated: " . $stats['last_updated'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 