<?php
require_once 'config.php';
require_once 'auth.php';

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: index.php');
    exit();
}

class AdminPanel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function createElection($title, $description, $start_date, $end_date, $user_id) {
        $query = "INSERT INTO elections (title, description, start_date, end_date, created_by) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $title, $description, $start_date, $end_date, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Election created successfully', 'election_id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'message' => 'Failed to create election'];
        }
    }
    
    public function addCoalition($election_id, $name, $symbol, $color) {
        $query = "INSERT INTO coalitions (election_id, name, symbol, color) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $election_id, $name, $symbol, $color);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Coalition added successfully', 'coalition_id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'message' => 'Failed to add coalition'];
        }
    }
    
    public function deleteCoalition($coalition_id) {
        $query = "DELETE FROM coalitions WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $coalition_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Coalition removed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to remove coalition'];
        }
    }
    
    public function addCoalitionMember($coalition_id, $election_id, $name, $position, $bio) {
        $query = "INSERT INTO candidates (coalition_id, election_id, name, position, bio) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisss", $coalition_id, $election_id, $name, $position, $bio);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Member added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add member'];
        }
    }
    
    public function removeMember($candidate_id) {
        $query = "DELETE FROM candidates WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $candidate_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Member removed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to remove member'];
        }
    }
    
    public function updateElectionStatus($election_id, $status) {
        $query = "UPDATE elections SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $election_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Election status updated'];
        } else {
            return ['success' => false, 'message' => 'Failed to update election status'];
        }
    }
    
    public function getAllElections() {
        $query = "SELECT e.*, (SELECT COUNT(*) FROM votes WHERE election_id = e.id) as total_votes 
                  FROM elections e 
                  ORDER BY e.created_at DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getElectionResults($election_id) {
        $query = "SELECT c.id, c.name, COUNT(v.id) as vote_count 
                  FROM coalitions c 
                  LEFT JOIN votes v ON c.id = v.coalition_id 
                  WHERE c.election_id = ? 
                  GROUP BY c.id 
                  ORDER BY vote_count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deleteElection($election_id) {
        $query = "DELETE FROM elections WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $election_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Election deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete election'];
        }
    }
    
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'voter'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getTotalElections() {
        $query = "SELECT COUNT(*) as total FROM elections";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getTotalVotes() {
        $query = "SELECT COUNT(*) as total FROM votes";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}

$admin = new AdminPanel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'create_election') {
        $response = $admin->createElection(
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            $_POST['start_date'] ?? '',
            $_POST['end_date'] ?? '',
            $_SESSION['user_id']
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'add_coalition') {
        $response = $admin->addCoalition(
            $_POST['election_id'] ?? 0,
            $_POST['name'] ?? '',
            $_POST['symbol'] ?? '',
            $_POST['color'] ?? '#10b981'
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'delete_coalition') {
        $response = $admin->deleteCoalition($_POST['coalition_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'add_member') {
        $response = $admin->addCoalitionMember(
            $_POST['coalition_id'] ?? 0,
            $_POST['election_id'] ?? 0,
            $_POST['name'] ?? '',
            $_POST['position'] ?? '',
            $_POST['bio'] ?? ''
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'update_status') {
        $response = $admin->updateElectionStatus(
            $_POST['election_id'] ?? 0,
            $_POST['status'] ?? ''
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'delete_election') {
        $response = $admin->deleteElection($_POST['election_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'remove_member') {
        $response = $admin->removeMember($_POST['candidate_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
