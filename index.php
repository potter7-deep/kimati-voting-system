<?php
require_once 'config.php';
require_once 'auth.php';

$is_logged_in = $auth->isLoggedIn();
$is_admin = $is_logged_in && $auth->isAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Voting System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <img src="K.png" alt="Kimathi Logo" class="navbar-logo">
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
                <?php if ($is_logged_in): ?>
                    <span class="user-greeting">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <?php if ($is_admin): ?>
                        <a href="dashboard.php" class="nav-link admin-link">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="vote.php" class="nav-link">Vote Now</a>
                    <?php endif; ?>
                    <a href="results.php" class="nav-link">Results</a>
                    <form method="POST" action="auth.php" style="display: inline;">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="nav-link logout-btn">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="#login" class="nav-link" onclick="showModal('loginModal')">Login</a>
                    <a href="#register" class="nav-link" onclick="showModal('registerModal')">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <?php if (!$is_logged_in): ?>
            <section class="hero">
                <div class="hero-content">
                    <div class="hero-header">
                        <h3 class="university-title">Dedan Kimathi University of Technology</h3>
                        <h1>Kimathi Voting System</h1>
                    </div>
                    <p>Secure, transparent, and efficient voting for your university community</p>
                    <div class="hero-buttons">
                        <button class="btn btn-primary" onclick="showModal('loginModal')">Login</button>
                        <button class="btn btn-secondary" onclick="showModal('registerModal')">Register</button>
                    </div>
                </div>
                <div class="hero-illustration">
                    <img src="KVS.png" alt="Kimathi Voting System Logo" class="hero-logo">
                </div>
            </section>

            <section class="features">
                <h2>Why Vote Here?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <h3>Secure & Safe</h3>
                        <p>Your vote is encrypted and protected with industry-standard security</p>
                    </div>
                    <div class="feature-card">
                        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3>Transparent</h3>
                        <p>Real-time results and voting statistics available to all users</p>
                    </div>
                    <div class="feature-card">
                        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                        </svg>
                        <h3>Easy to Use</h3>
                        <p>Simple and intuitive interface designed for all users</p>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="dashboard">
                <h1>Welcome to Your Voting Dashboard</h1>
                <?php if (!$is_admin): ?>
                    <div class="dashboard-cards">
                        <div class="info-card">
                            <h3>Ready to Vote?</h3>
                            <p>Participate in upcoming elections and make your voice heard</p>
                            <a href="vote.php" class="btn btn-primary">Go to Voting</a>
                        </div>
                        <div class="info-card">
                            <h3>Check Results</h3>
                            <p>View real-time voting results from ongoing and completed elections</p>
                            <a href="results.php" class="btn btn-secondary">View Results</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="admin-intro">
                        <p>As an admin, you have access to the voting system management panel</p>
                        <a href="dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h2>Login</h2>
            <form id="loginForm">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="form-switch">Don't have an account? <a href="#" onclick="switchModal('registerModal')">Register here</a></p>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('registerModal')">&times;</span>
            <h2>Register</h2>
            <form id="registerForm">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label for="registerName">Full Name</label>
                    <input type="text" id="registerName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="registerRegNum">Registration Number</label>
                    <input type="text" id="registerRegNum" name="registration_number" required>
                </div>
                <div class="form-group">
                    <label for="registerYear">Year of Study</label>
                    <select id="registerYear" name="year" required>
                        <option value="">Select Year</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                        <option value="4">Year 4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" id="registerPassword" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p class="form-switch">Already have an account? <a href="#" onclick="switchModal('loginModal')">Login here</a></p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 University Voting System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
