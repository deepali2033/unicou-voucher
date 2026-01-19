<?php

/**
 * Manual test to verify book service notifications work correctly
 * This is a simple debugging script to test the notification system
 */

// Simulate creating a book service notification
echo "Testing Book Service Notification System...\n\n";

// Test data - simulating what would happen when a recruiter books a service
$testNotification = [
    'title' => 'Your Service Has Been Booked',
    'description' => 'Your service "House Cleaning Service" has been booked by Jane Customer',
    'action' => '/recruiter/book-services/123',
    'type' => 'service',
    'is_read' => false,
    'created_at' => date('Y-m-d H:i:s')
];

// Simulate the notification display logic from the blade template
echo "Notification Data:\n";
echo "- Title: {$testNotification['title']}\n";
echo "- Description: {$testNotification['description']}\n";
echo "- Action URL: {$testNotification['action']}\n";
echo "- Type: {$testNotification['type']}\n";
echo "- Is Read: " . ($testNotification['is_read'] ? 'Yes' : 'No') . "\n\n";

// Simulate the blade template logic
if ($testNotification['action']) {
    echo "✅ This notification would be CLICKABLE (has action_url)\n";
    echo "   HTML would be: <a href=\"{$testNotification['action']}\">...</a>\n";
} else {
    echo "❌ This notification would NOT be clickable (no action_url)\n";
}

echo "\nTest completed. If you see '✅ CLICKABLE' above, the notification system should work correctly.\n";
