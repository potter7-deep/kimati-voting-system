<?php
require_once 'config.php';

class Auth {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function register($name, $email, $password, $registration_number, $year) {
        if (empty($name) || empty($email) || empty($password) || empty($registration_number) || empty($year)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if ($year < 1 || $year > 4) {
            return ['success' => false, 'message' => 'Year must be between 1 and 4'];
        }
        
        $check_query = "SELECT id FROM users WHERE email = ? OR registration_number = ?";
        $stmt = $this->conn->prepare($check_query);
        $stmt->bind_param("ss", $email, $registration_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'Email or Registration Number already exists'];
        }
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $insert_query = "INSERT INTO users (name, email, password, registration_number, year) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insert_query);
        $stmt->bind_param("ssssi", $name, $email, $hashed_password, $registration_number, $year);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $query = "SELECT id, name, email, role, password FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                return ['success' => true, 'message' => 'Login successful', 'role' => $user['role']];
            } else {
                return ['success' => false, 'message' => 'Invalid password'];
            }
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

$auth = new Auth($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'register') {
        $response = $auth->register(
            $_POST['name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['registration_number'] ?? '',
            $_POST['year'] ?? ''
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'login') {
        $response = $auth->login(
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } elseif ($action === 'logout') {
        $auth->logout();
        header('Location: index.php');
        exit();
    }
}
?>
