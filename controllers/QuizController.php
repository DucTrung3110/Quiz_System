<?php
class QuizController {
    private $quizModel;
    private $questionModel;
    private $resultModel;
    
    public function __construct() {
        $this->quizModel = new Quiz();
        $this->questionModel = new Question();
        $this->resultModel = new Result();
    }
    
    /**
     * Display all available quizzes
     */
    public function index() {
        $quizzes = $this->quizModel->getAllQuizzes();
        return $quizzes;
    }
    
    /**
     * Start a quiz
     */
    public function startQuiz($quizId) {
        $quiz = $this->quizModel->getQuizById($quizId);
        if (!$quiz) {
            throw new Exception("Quiz not found");
        }
        
        $questions = $this->questionModel->getQuestionsByQuizId($quizId);
        if (empty($questions)) {
            throw new Exception("No questions found for this quiz");
        }
        
        return [
            'quiz' => $quiz,
            'questions' => $questions
        ];
    }
    
    /**
     * Submit quiz answers and calculate score
     */
    public function submitQuiz($quizId, $studentName, $email, $answers) {
        $quiz = $this->quizModel->getQuizById($quizId);
        if (!$quiz) {
            throw new Exception("Quiz not found");
        }
        
        $questions = $this->questionModel->getQuestionsByQuizId($quizId);
        $score = 0;
        $totalQuestions = count($questions);
        $detailedAnswers = [];
        
        foreach ($questions as $question) {
            $questionId = $question['id'];
            $userAnswer = isset($answers[$questionId]) ? $answers[$questionId] : '';
            $correctAnswer = $question['correct_answer'];
            $isCorrect = ($userAnswer === $correctAnswer);
            
            if ($isCorrect) {
                $score++;
            }
            
            $detailedAnswers[] = [
                'question_id' => $questionId,
                'question' => $question['question'],
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect
            ];
        }
        
        // Save result to database
        $resultId = $this->resultModel->saveResult($quizId, $studentName, $email, $score, $totalQuestions, $detailedAnswers);
        
        return [
            'result_id' => $resultId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => round(($score / $totalQuestions) * 100, 2),
            'detailed_answers' => $detailedAnswers
        ];
    }
    
    /**
     * Get quiz result
     */
    public function getResult($resultId) {
        $result = $this->resultModel->getResultById($resultId);
        if (!$result) {
            throw new Exception("Result not found");
        }
        
        $result['answers'] = json_decode($result['answers'], true);
        return $result;
    }
}
?>
