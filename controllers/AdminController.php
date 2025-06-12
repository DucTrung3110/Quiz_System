<?php
class AdminController {
    private $quizModel;
    private $questionModel;
    private $resultModel;
    
    public function __construct() {
        $this->quizModel = new Quiz();
        $this->questionModel = new Question();
        $this->resultModel = new Result();
    }
    
    /**
     * Check if user is authenticated as admin
     */
    public function isAuthenticated() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Authenticate admin login
     */
    public function login($username, $password) {
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            return true;
        }
        return false;
    }
    
    /**
     * Logout admin
     */
    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        // Don't destroy entire session, just remove admin keys to preserve user session
    }
    
    /**
     * Get dashboard data
     */
    public function getDashboardData() {
        $quizzes = $this->quizModel->getAllQuizzes();
        $recentResults = $this->resultModel->getRecentResults(5);
        
        return [
            'total_quizzes' => count($quizzes),
            'quizzes' => $quizzes,
            'recent_results' => $recentResults
        ];
    }
    
    /**
     * Get all quizzes for admin
     */
    public function getAllQuizzes() {
        return $this->quizModel->getAllQuizzes();
    }
    
    /**
     * Create quiz
     */
    public function createQuiz($title, $description, $timeLimit = null) {
        if (empty($title)) {
            throw new Exception("Quiz title is required");
        }
        return $this->quizModel->createQuiz($title, $description, $timeLimit);
    }
    
    /**
     * Update quiz
     */
    public function updateQuiz($id, $title, $description, $timeLimit = null) {
        if (empty($title)) {
            throw new Exception("Quiz title is required");
        }
        return $this->quizModel->updateQuiz($id, $title, $description, $timeLimit);
    }
    
    /**
     * Delete quiz
     */
    public function deleteQuiz($id) {
        return $this->quizModel->deleteQuiz($id);
    }
    
    /**
     * Get quiz questions
     */
    public function getQuizQuestions($quizId) {
        return $this->questionModel->getQuestionsByQuizId($quizId);
    }
    
    /**
     * Create question
     */
    public function createQuestion($quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        if (empty($question)) {
            throw new Exception("Question text is required");
        }
        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            throw new Exception("Correct answer must be A, B, C, or D");
        }
        return $this->questionModel->createQuestion($quizId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum);
    }
    
    /**
     * Update question
     */
    public function updateQuestion($id, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum = 0) {
        if (empty($question)) {
            throw new Exception("Question text is required");
        }
        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            throw new Exception("Correct answer must be A, B, C, or D");
        }
        return $this->questionModel->updateQuestion($id, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $orderNum);
    }
    
    /**
     * Delete question
     */
    public function deleteQuestion($id) {
        return $this->questionModel->deleteQuestion($id);
    }
    
    /**
     * Get quiz results
     */
    public function getQuizResults($quizId) {
        return $this->resultModel->getResultsByQuizId($quizId);
    }
}
?>
