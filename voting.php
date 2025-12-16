<?php
require_once 'config.php';
require_once 'auth.php';

if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

class VotingSystem {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getActiveElections() {
        $query = "SELECT * FROM elections WHERE status = 'active' ORDER BY start_date DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getElectionCoalitions($election_id) {
        $query = "SELECT c.*, 
                  (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', id, 'name', name, 'position', position, 'bio', bio)) 
                   FROM candidates 
                   WHERE coalition_id = c.id 
                   ORDER BY FIELD(position, 'chairperson', 'vice_chair', 'secretary', 'sports_person', 'treasurer', 'gender_representative')) as members
                  FROM coalitions c 
                  WHERE c.election_id = ? 
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($result as &$coalition) {
            if ($coalition['members']) {
                $coalition['members'] = json_decode($coalition['members'], true);
            } else {
                $coalition['members'] = [];
            }
        }
        return $result;
    }
    
    public function userHasVoted($election_id, $voter_id) {
        $query = "SELECT id FROM votes WHERE election_id = ? AND voter_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $election_id, $voter_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function castVote($election_id, $coalition_id, $voter_id) {
        if ($this->userHasVoted($election_id, $voter_id)) {
            return ['success' => false, 'message' => 'You have already voted in this election'];
        }
        
        $query = "INSERT INTO votes (election_id, coalition_id, voter_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $election_id, $coalition_id, $voter_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Vote recorded successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to record vote'];
        }
    }
    
    public function getResults($election_id) {
        $query = "SELECT c.id, c.name, c.color, COUNT(v.id) as vote_count 
                  FROM coalitions c 
                  LEFT JOIN votes v ON c.id = v.coalition_id 
                  WHERE c.election_id = ? 
                  GROUP BY c.id 
                  ORDER BY vote_count DESC, c.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getElectionById($id) {
        $query = "SELECT * FROM elections WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getTotalVotes($election_id) {
        $query = "SELECT COUNT(*) as total FROM votes WHERE election_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
    
    public function getCoalitionMembers($coalition_id) {
        $query = "SELECT * FROM candidates WHERE coalition_id = ? ORDER BY FIELD(position, 'chairperson', 'vice_chair', 'secretary', 'sports_person', 'treasurer', 'gender_representative')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $coalition_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$voting = new VotingSystem($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'vote') {
        $response = $voting->castVote(
            $_POST['election_id'] ?? 0,
            $_POST['coalition_id'] ?? 0,
            $_SESSION['user_id']
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
