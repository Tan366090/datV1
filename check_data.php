<?php
require_once __DIR__ . '/backend/src/config/database.php';
require_once __DIR__ . '/backend/src/models/User.php';
require_once __DIR__ . '/backend/src/models/UserProfile.php';

try {
    // Initialize models
    $userModel = new User();
    $profileModel = new UserProfile();
    
    // Check users
    $users = $userModel->getAll();
    echo "Total users: " . count($users) . "\n";
    if (count($users) > 0) {
        echo "Sample users:\n";
        foreach (array_slice($users, 0, 3) as $user) {
            echo "- User ID: {$user['user_id']}, Username: {$user['username']}, Email: {$user['email']}\n";
        }
    }
    
    // Check profiles
    $profiles = $profileModel->getAll();
    echo "\nTotal profiles: " . count($profiles) . "\n";
    if (count($profiles) > 0) {
        echo "Sample profiles:\n";
        foreach (array_slice($profiles, 0, 3) as $profile) {
            echo "- Profile ID: {$profile['profile_id']}, User ID: {$profile['user_id']}, Full Name: {$profile['full_name']}\n";
        }
    }
    
    // Check if all users have profiles
    $usersWithoutProfiles = [];
    foreach ($users as $user) {
        $profile = $profileModel->getProfileByUserId($user['user_id']);
        if (!$profile) {
            $usersWithoutProfiles[] = $user['username'];
        }
    }
    
    if (!empty($usersWithoutProfiles)) {
        echo "\nUsers without profiles:\n";
        foreach ($usersWithoutProfiles as $username) {
            echo "- $username\n";
        }
    } else {
        echo "\nAll users have profiles.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 