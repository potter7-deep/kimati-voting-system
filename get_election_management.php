<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'admin.php';

if (!$auth->isLoggedIn() || !$auth->isAdmin() || !isset($_GET['election_id'])) {
    exit();
}

$admin = new AdminPanel($conn);
$election_id = intval($_GET['election_id']);

$query = "SELECT * FROM elections WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();

$coalitions_query = "SELECT * FROM coalitions WHERE election_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($coalitions_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$coalitions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$results = $admin->getElectionResults($election_id);
?>

<div class="election-management">
    <h2><?php echo htmlspecialchars($election['title']); ?></h2>
    
    <div class="management-tabs">
        <button class="tab-btn active" onclick="switchTab(event, 'coalitions-tab')">Coalitions</button>
        <button class="tab-btn" onclick="switchTab(event, 'results-tab')">Results</button>
        <button class="tab-btn" onclick="switchTab(event, 'settings-tab')">Settings</button>
    </div>

    <!-- Coalitions Tab -->
    <div id="coalitions-tab" class="tab-content active">
        <h3>Election Coalitions</h3>
        <button class="btn btn-primary" onclick="showModal('addCoalitionModal')">Add Coalition</button>
        
        <?php if (!empty($coalitions)): ?>
            <div class="coalitions-list">
                <?php foreach ($coalitions as $coalition): ?>
                    <div class="coalition-item">
                        <div>
                            <h4><?php echo htmlspecialchars($coalition['name']); ?></h4>
                            <p><?php echo htmlspecialchars($coalition['symbol'] ?? 'No symbol'); ?></p>
                            <button class="btn btn-secondary btn-sm" onclick="showModal('addMemberModal-' + <?php echo $coalition['id']; ?>)">Add Members</button>
                            <?php 
                                $members_query = "SELECT * FROM candidates WHERE coalition_id = ? ORDER BY FIELD(position, 'chairperson', 'vice_chair', 'secretary', 'sports_person', 'treasurer', 'gender_representative')";
                                $stmt = $conn->prepare($members_query);
                                $stmt->bind_param("i", $coalition['id']);
                                $stmt->execute();
                                $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>
                            <?php if (!empty($members)): ?>
                                <div class="members-list">
                                    <?php foreach ($members as $member): ?>
                                        <span class="member-badge"><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($member['position']))); ?>: <?php echo htmlspecialchars($member['name']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No members added yet</p>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="deleteCoalition(<?php echo $coalition['id']; ?>)">Remove</button>
                    </div>

                    <!-- Add Member Modal for this Coalition -->
                    <div id="addMemberModal-<?php echo $coalition['id']; ?>" class="modal" style="display: none;">
                        <div class="modal-content">
                            <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                            <h3>Add Member to <?php echo htmlspecialchars($coalition['name']); ?></h3>
                            <form id="addMemberForm-<?php echo $coalition['id']; ?>" class="addMemberForm">
                                <input type="hidden" name="action" value="add_member">
                                <input type="hidden" name="coalition_id" value="<?php echo $coalition['id']; ?>">
                                <div class="form-group">
                                    <label for="memberPosition">Position</label>
                                    <select name="position" required>
                                        <option value="">Select Position</option>
                                        <option value="chairperson">Chairperson/President</option>
                                        <option value="vice_chair">Vice Chair</option>
                                        <option value="secretary">Secretary</option>
                                        <option value="sports_person">Sports Person</option>
                                        <option value="treasurer">Treasurer</option>
                                        <option value="gender_representative">Gender Representative</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="memberName">Name</label>
                                    <input type="text" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="memberBio">Bio</label>
                                    <textarea name="bio" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Member</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No coalitions added yet.</p>
        <?php endif; ?>

        <div id="addCoalitionModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                <h3>Add Coalition</h3>
                <form id="addCoalitionForm">
                    <input type="hidden" name="action" value="add_coalition">
                    <input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
                    <div class="form-group">
                        <label for="coalitionName">Coalition Name</label>
                        <input type="text" id="coalitionName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="coalitionSymbol">Symbol</label>
                        <input type="text" id="coalitionSymbol" name="symbol">
                    </div>
                    <div class="form-group">
                        <label for="coalitionColor">Color (Hex)</label>
                        <input type="color" id="coalitionColor" name="color" value="#10b981">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Coalition</button>
                </form>
            </div>
        </div>

        <div id="quickAddMemberModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal('quickAddMemberModal')">&times;</span>
                <h3>Add Members to <span id="addMemberCoalitionName"></span></h3>
                <p class="text-muted">Add the 6 coalition members (one position per person)</p>
                
                <form id="addMemberForm">
                    <input type="hidden" name="action" value="add_member">
                    <input type="hidden" id="addMemberCoalitionId" name="coalition_id">
                    <input type="hidden" id="addMemberElectionId" name="election_id">
                    
                    <div class="form-group">
                        <label for="memberPosition">Position</label>
                        <select name="position" required>
                            <option value="">Select Position</option>
                            <option value="chairperson">Chairperson/President</option>
                            <option value="vice_chair">Vice Chair</option>
                            <option value="secretary">Secretary</option>
                            <option value="sports_person">Sports Person</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="gender_representative">Gender Representative</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="memberName">Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="memberBio">Bio (Optional)</label>
                        <textarea name="bio" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add This Member</button>
                </form>

                <div class="added-members">
                    <h4>Members Added:</h4>
                    <ul id="membersList" class="members-added-list"></ul>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="finishAddingMembers()">Done Adding Members</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Tab -->
    <div id="results-tab" class="tab-content">
        <h3>Voting Results</h3>
        <?php if (!empty($results)): ?>
            <div class="coalitions-results">
                <?php 
                    $total = 0;
                    foreach ($results as $result) {
                        $total += $result['vote_count'];
                    }
                ?>
                <?php foreach ($results as $result): ?>
                    <?php $percentage = $total > 0 ? ($result['vote_count'] / $total) * 100 : 0; ?>
                    <div class="coalition-result">
                        <h4><?php echo htmlspecialchars($result['name']); ?></h4>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <p><?php echo $result['vote_count']; ?> votes (<?php echo number_format($percentage, 1); ?>%)</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No voting data available yet.</p>
        <?php endif; ?>
    </div>

    <!-- Settings Tab -->
    <div id="settings-tab" class="tab-content">
        <h3>Election Settings</h3>
        <div class="settings-form">
            <div class="form-group">
                <label>Status</label>
                <select id="electionStatus" onchange="updateElectionStatus(<?php echo $election_id; ?>)">
                    <option value="upcoming" <?php echo $election['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="active" <?php echo $election['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="closed" <?php echo $election['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <p><?php echo htmlspecialchars($election['description']); ?></p>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <p><?php echo date('M d, Y H:i', strtotime($election['start_date'])); ?></p>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <p><?php echo date('M d, Y H:i', strtotime($election['end_date'])); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteCoalition(coalitionId) {
        if (confirm('Are you sure you want to delete this coalition?')) {
            const formData = new FormData();
            formData.append('action', 'delete_coalition');
            formData.append('coalition_id', coalitionId);
            
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
