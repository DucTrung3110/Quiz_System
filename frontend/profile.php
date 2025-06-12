<?php
require_once '../config/config.php';

$userController = new UserController();

// Check if user is logged in
if (!$userController->isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$user = $userController->getCurrentUser();
$userResults = $userController->getUserResults();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    
    try {
        $userController->updateProfile($email, $fullName);
        $success = 'Profile updated successfully';
        $user = $userController->getCurrentUser(); // Refresh user data
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    try {
        $userController->changePassword($currentPassword, $newPassword, $confirmPassword);
        $success = 'Password changed successfully';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../views/layout/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Đổi mật khẩu</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Thống kê cá nhân</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3><?php echo count($userResults); ?></h3>
                        <p class="text-muted">Số bài quiz đã làm</p>
                    </div>
                    
                    <?php if (!empty($userResults)): ?>
                        <hr>
                        <h6>Kết quả gần đây:</h6>
                        <?php foreach (array_slice($userResults, 0, 5) as $result): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small><?php echo htmlspecialchars($result['quiz_title']); ?></small>
                                <span class="badge <?php echo $result['percentage'] >= 70 ? 'bg-success' : ($result['percentage'] >= 50 ? 'bg-warning' : 'bg-danger'); ?>">
                                    <?php echo $result['percentage']; ?>%
                                </span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($userResults) > 5): ?>
                            <small class="text-muted">và <?php echo count($userResults) - 5; ?> kết quả khác...</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>