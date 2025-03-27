<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidate_id = $_POST['candidate_id'];

    // Check if the user has already voted
    $stmt = $conn->prepare("SELECT * FROM votes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing_vote = $stmt->fetch();

    if ($existing_vote) {
        echo "❌ You have already voted!";
    } else {
        // Insert the vote since the user has not voted yet
        $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
        if ($stmt->execute([$user_id, $candidate_id])) {
            echo "✅ Vote cast successfully!";
        } else {
            echo "❌ Error casting vote!";
        }
    }
}

// Fetch candidates
$candidates = $conn->query("SELECT * FROM candidates")->fetchAll();
?>
<h1> Welcome to the Voting Page </h1>
<form method="POST">
    <h2>Select a Candidate:</h2>
    <?php foreach ($candidates as $candidate) { ?>
        <input type="radio" name="candidate_id" value="<?= $candidate['id'] ?>" required>
        <?= $candidate['name'] ?> (<?= $candidate['party'] ?>)<br>
    <?php } ?>
    <button type="submit">Vote</button>
</form>
