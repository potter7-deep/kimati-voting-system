<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'voting.php';

if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$voting = new VotingSystem($conn);

$get_elections_query = "SELECT * FROM elections WHERE status IN ('active', 'closed') ORDER BY created_at DESC";
$elections_result = $conn->query($get_elections_query);
$elections = $elections_result->fetch_all(MYSQLI_ASSOC);

$selected_election = null;
$selected_results = null;
$total_votes = 0;

if (isset($_GET['election_id'])) {
    $election_id = intval($_GET['election_id']);
    $selected_election = $voting->getElectionById($election_id);
    if ($selected_election) {
        $selected_results = $voting->getResults($election_id);
        $total_votes = $voting->getTotalVotes($election_id);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - University Voting System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <img src="logo.png" alt="Kimathi Logo" class="navbar-logo">
                <div class="brand-text">
                    <div class="university-name">Dedan Kimathi University</div>
                    <span class="brand-name">Voting System</span>
                </div>
            </div>
            <div class="navbar-menu">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                    <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                <a href="index.php" class="nav-link">Home</a>
                <a href="vote.php" class="nav-link">Vote</a>
                <form method="POST" action="auth.php" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="nav-link logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <section class="results-section">
            <h1>Voting Results</h1>
            
            <?php if (!empty($elections)): ?>
                <div class="results-container">
                    <div class="elections-sidebar">
                        <h3>Elections</h3>
                        <div class="elections-list">
                            <?php foreach ($elections as $election): ?>
                                <a href="results.php?election_id=<?php echo $election['id']; ?>" 
                                   class="election-item <?php echo (isset($_GET['election_id']) && $_GET['election_id'] == $election['id']) ? 'active' : ''; ?>">
                                    <div class="election-item-header">
                                        <span class="election-title"><?php echo htmlspecialchars($election['title']); ?></span>
                                        <span class="election-status <?php echo $election['status']; ?>"><?php echo ucfirst($election['status']); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="results-content">
                        <?php if ($selected_election && $selected_results !== null): ?>
                            <div class="election-results">
                                <h2><?php echo htmlspecialchars($selected_election['title']); ?></h2>
                                <p class="election-description"><?php echo htmlspecialchars($selected_election['description']); ?></p>
                                
                                <div class="results-stats">
                                    <div class="stat">
                                        <span class="stat-label">Total Votes</span>
                                        <span class="stat-value"><?php echo $total_votes; ?></span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">Status</span>
                                        <span class="stat-value"><?php echo ucfirst($selected_election['status']); ?></span>
                                    </div>
                                </div>

                                <div class="coalitions-results">
                                    <?php if (!empty($selected_results)): ?>
                                        <?php foreach ($selected_results as $result): ?>
                                            <?php 
                                                $percentage = $total_votes > 0 ? ($result['vote_count'] / $total_votes) * 100 : 0;
                                            ?>
                                            <div class="coalition-result" style="border-color: <?php echo htmlspecialchars($result['color'] ?? '#10b981'); ?>;">
                                                <div class="result-header">
                                                    <h3><?php echo htmlspecialchars($result['name']); ?></h3>
                                                    <span class="vote-count"><?php echo $result['vote_count']; ?> votes</span>
                                                </div>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo htmlspecialchars($result['color'] ?? '#10b981'); ?>"></div>
                                                </div>
                                                <div class="percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <p>No voting data available for this election.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif (!isset($_GET['election_id'])): ?>
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                    <polyline points="13 2 13 9 20 9"></polyline>
                                </svg>
                                <h2>Select an Election</h2>
                                <p>Choose an election from the list to view its results</p>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <h2>Election Not Found</h2>
                                <p>The election you're looking for doesn't exist.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <h2>No Elections Available</h2>
                    <p>There are currently no completed or active elections to view results for.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 University Voting System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
