<?php
require_once 'config.php';
require_once 'auth.php';

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    die('Admin access required');
}

// Check all candidates
$query = "SELECT c.*, e.title as election_title FROM candidates c JOIN elections e ON c.election_id = e.id ORDER BY c.created_at DESC LIMIT 10";
$result = $conn->query($query);

echo "<h2>All Candidates in Database:</h2>";
echo "<pre>";
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, Election: {$row['election_title']}, Name: {$row['name']}, Position: {$row['position']}\n";
    }
} else {
    echo "No candidates found in database\n";
}
echo "</pre>";

// Check active elections
$query2 = "SELECT id, title, status FROM elections ORDER BY created_at DESC";
$result2 = $conn->query($query2);

echo "<h2>All Elections:</h2>";
echo "<pre>";
if ($result2 && $result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
        echo "ID: {$row['id']}, Title: {$row['title']}, Status: {$row['status']}\n";
    }
} else {
    echo "No elections found\n";
}
echo "</pre>";

// Check candidates for each active election
$query3 = "SELECT * FROM elections WHERE status = 'active'";
$result3 = $conn->query($query3);

echo "<h2>Candidates for Active Elections:</h2>";
echo "<pre>";
if ($result3 && $result3->num_rows > 0) {
    while($election = $result3->fetch_assoc()) {
        echo "Election: {$election['title']} (ID: {$election['id']})\n";
        $cand_query = "SELECT * FROM candidates WHERE election_id = {$election['id']}";
        $cand_result = $conn->query($cand_query);
        if ($cand_result && $cand_result->num_rows > 0) {
            while($cand = $cand_result->fetch_assoc()) {
                echo "  - {$cand['name']} ({$cand['position']})\n";
            }
        } else {
            echo "  No candidates\n";
        }
    }
} else {
    echo "No active elections\n";
}
echo "</pre>";
?>
