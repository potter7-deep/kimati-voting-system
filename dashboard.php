<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'admin.php';

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: index.php');
    exit();
}

$admin_panel = new AdminPanel($conn);
$all_elections = $admin_panel->getAllElections();
$total_users = $admin_panel->getTotalUsers();
$total_elections = $admin_panel->getTotalElections();
$total_votes = $admin_panel->getTotalVotes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University Voting System</title>
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
                <form method="POST" action="auth.php" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="nav-link logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <section class="admin-dashboard">
            <h1>Admin Dashboard</h1>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Users</span>
                        <span class="stat-value"><?php echo $total_users; ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Elections</span>
                        <span class="stat-value"><?php echo $total_elections; ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Votes</span>
                        <span class="stat-value"><?php echo $total_votes; ?></span>
                    </div>
                </div>
            </div>

            <div class="admin-section">
                <div class="section-header">
                    <h2>Elections Management</h2>
                    <button class="btn btn-primary" onclick="showModal('createElectionModal')">Create Election</button>
                </div>

                <div class="alert alert-info" style="margin-bottom: 2rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span><strong>Remember:</strong> Newly created elections start as "Upcoming". Change the status to "Active" in the Manage section to make them visible to voters.</span>
                </div>

                <?php if (!empty($all_elections)): ?>
                    <div class="elections-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Votes</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_elections as $election): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($election['title']); ?></td>
                                        <td><span class="status-badge <?php echo $election['status']; ?>"><?php echo ucfirst($election['status']); ?></span></td>
                                        <td><?php echo $election['total_votes']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($election['start_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($election['end_date'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary" onclick="openElectionModal(<?php echo $election['id']; ?>)">Manage</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteElection(<?php echo $election['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No elections created yet. Create one to get started.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Create Election Modal -->
    <div id="createElectionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createElectionModal')">&times;</span>
            <h2>Create Election</h2>
            <form id="createElectionForm">
                <input type="hidden" name="action" value="create_election">
                <div class="form-group">
                    <label for="electionTitle">Title</label>
                    <input type="text" id="electionTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="electionDescription">Description</label>
                    <textarea id="electionDescription" name="description" rows="4"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="electionStart">Start Date</label>
                        <input type="datetime-local" id="electionStart" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="electionEnd">End Date</label>
                        <input type="datetime-local" id="electionEnd" name="end_date" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Election</button>
            </form>
        </div>
    </div>

    <!-- Election Management Modal -->
    <div id="electionModal" class="modal">
        <div class="modal-content modal-large">
            <span class="close" onclick="closeModal('electionModal')">&times;</span>
            <div id="electionModalContent"></div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 University Voting System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        document.getElementById('createElectionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function openElectionModal(electionId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_election_management.php?election_id=' + electionId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('electionModalContent').innerHTML = xhr.responseText;
                    showModal('electionModal');
                }
            };
            xhr.send();
        }

        function deleteElection(electionId) {
            if (confirm('Are you sure you want to delete this election?')) {
                const formData = new FormData();
                formData.append('action', 'delete_election');
                formData.append('election_id', electionId);
                
                fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>
