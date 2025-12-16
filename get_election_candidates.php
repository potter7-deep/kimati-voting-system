<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'voting.php';

if (!$auth->isLoggedIn() || !isset($_GET['election_id'])) {
    exit();
}

$voting = new VotingSystem($conn);
$election_id = intval($_GET['election_id']);
$election = $voting->getElectionById($election_id);
$coalitions = $voting->getElectionCoalitions($election_id);
$user_has_voted = $voting->userHasVoted($election_id, $_SESSION['user_id']);
?>

<div class="voting-modal-content">
    <h2><?php echo htmlspecialchars($election['title']); ?></h2>
    <p><?php echo htmlspecialchars($election['description']); ?></p>

    <?php if ($user_has_voted): ?>
        <div class="alert alert-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>You have already voted in this election.</span>
        </div>
    <?php else: ?>
        <p class="voting-instruction">Select a coalition to cast your vote:</p>
        <div class="coalitions-grid">
            <?php foreach ($coalitions as $coalition): ?>
                <div class="coalition-card" style="border-color: <?php echo htmlspecialchars($coalition['color'] ?? '#10b981'); ?>;">
                    <div class="coalition-header">
                        <h3><?php echo htmlspecialchars($coalition['name']); ?></h3>
                        <span class="coalition-members-count"><?php echo count($coalition['members'] ?? []); ?> Members</span>
                    </div>
                    
                    <div class="coalition-members">
                        <?php if (!empty($coalition['members'])): ?>
                            <?php foreach ($coalition['members'] as $member): ?>
                                <div class="member">
                                    <span class="member-position"><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($member['position']))); ?></span>
                                    <span class="member-name"><?php echo htmlspecialchars($member['name']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($coalition['members'])): ?>
                        <button class="btn btn-primary btn-block" onclick="castVote(<?php echo $election_id; ?>, <?php echo $coalition['id']; ?>)">Vote for this Coalition</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="voting-meta">
        <p>Election ends: <?php echo date('M d, Y H:i', strtotime($election['end_date'])); ?></p>
    </div>
</div>

<style>
.coalitions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.coalition-card {
    border: 2px solid;
    border-radius: 12px;
    padding: 1.5rem;
    background: var(--surface);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.coalition-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
}

.coalition-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 1rem;
}

.coalition-header h3 {
    font-size: 1.3rem;
    color: var(--text-primary);
    margin: 0;
    flex: 1;
}

.coalition-members-count {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    background: var(--primary-color);
    color: white;
    border-radius: 20px;
    white-space: nowrap;
    font-weight: 600;
}

.coalition-members {
    flex: 1;
    margin: 1rem 0;
    max-height: 250px;
    overflow-y: auto;
}

.member {
    padding: 0.7rem;
    margin: 0.4rem 0;
    background: var(--surface-light);
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    border-left: 3px solid var(--primary-color);
}

.member-position {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
}

.member-name {
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 600;
}

.btn-block {
    width: 100%;
    margin-top: auto;
}

@media (max-width: 768px) {
    .coalitions-grid {
        grid-template-columns: 1fr;
    }
}
</style>
