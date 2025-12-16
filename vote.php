<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'voting.php';

if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$voting = new VotingSystem($conn);
$active_elections = $voting->getActiveElections();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - University Voting System</title>
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
                <a href="results.php" class="nav-link">Results</a>
                <form method="POST" action="auth.php" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="nav-link logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <section class="voting-section">
            <h1>Active Elections</h1>
            
            <?php if (empty($active_elections)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <h2>No Active Elections</h2>
                    <p>There are currently no active elections. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="elections-grid">
                    <?php foreach ($active_elections as $election): ?>
                        <div class="election-card">
                            <div class="election-header">
                                <h2><?php echo htmlspecialchars($election['title']); ?></h2>
                                <span class="status-badge active">Active</span>
                            </div>
                            <p class="election-description"><?php echo htmlspecialchars($election['description']); ?></p>
                            <div class="election-meta">
                                <span>Ends: <?php echo date('M d, Y H:i', strtotime($election['end_date'])); ?></span>
                            </div>
                            <button class="btn btn-primary" onclick="openVotingModal(<?php echo $election['id']; ?>)">View Candidates & Vote</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Voting Modal -->
    <div id="votingModal" class="modal">
        <div class="modal-content modal-large">
            <span class="close" onclick="closeModal('votingModal')">&times;</span>
            <div id="votingContent"></div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 University Voting System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function openVotingModal(electionId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_election_candidates.php?election_id=' + electionId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('votingContent').innerHTML = xhr.responseText;
                    showModal('votingModal');
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
