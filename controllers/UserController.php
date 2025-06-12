<?php
class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }
    
    /**
     * Get current logged in user
     */
    public function getCurrentUser() {
        if ($this->isAuthenticated() && isset($_SESSION['user_id'])) {
            return $this->userModel->getUserById($_SESSION['user_id']);
        }
        return null;
    }
    
    /**
     * Register new user
     */
    public function register($username, $email, $password, $confirmPassword, $fullName) {
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
            throw new Exception("All fields are required");
        }
        
        if (strlen($username) < 3) {
            throw new Exception("Username must be at least 3 characters long");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }
        
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match");
        }
        
        // Register user
        $userId = $this->userModel->register($username, $email, $password, $fullName);
        
        // Auto login after registration
        $this->loginUser($userId, $username, $fullName);
        
        return $userId;
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            throw new Exception("Username and password are required");
        }
        
        $user = $this->userModel->login($username, $password);
        if (!$user) {
            throw new Exception("Invalid username or password");
        }
        
        $this->loginUser($user['id'], $user['username'], $user['full_name']);
        return $user;
    }
    
    /**
     * Set user session
     */
    private function loginUser($userId, $username, $fullName) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['user_full_name'] = $fullName;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_full_name']);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($email, $fullName) {
        if (!$this->isAuthenticated()) {
            throw new Exception("User not logged in");
        }
        
        if (empty($email) || empty($fullName)) {
            throw new Exception("Email and full name are required");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        $userId = $_SESSION['user_id'];
        $result = $this->userModel->updateProfile($userId, $email, $fullName);
        
        // Update session
        $_SESSION['user_full_name'] = $fullName;
        
        return $result;
    }
    
    /**
     * Change password
     */
    public function changePassword($currentPassword, $newPassword, $confirmPassword) {
        if (!$this->isAuthenticated()) {
            throw new Exception("User not logged in");
        }
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            throw new Exception("All password fields are required");
        }
        
        if (strlen($newPassword) < 6) {
            throw new Exception("New password must be at least 6 characters long");
        }
        
        if ($newPassword !== $confirmPassword) {
            throw new Exception("New passwords do not match");
        }
        
        // Verify current password
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Current password is incorrect");
        }
        
        return $this->userModel->changePassword($_SESSION['user_id'], $newPassword);
    }
    
    /**
     * Get user's quiz results
     */
    public function getUserResults() {
        if (!$this->isAuthenticated()) {
            throw new Exception("User not logged in");
        }
        
        return $this->userModel->getUserResults($_SESSION['user_id']);
    }
}
?>