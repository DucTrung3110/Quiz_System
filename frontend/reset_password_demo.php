<?php
require_once '../config/config.php';

$message = '';
$error = '';
$valid_token = false;
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (!empty($token)) {
    global $pdo;
    
    // Verify token
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expires > datetime('now')");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid_token = true;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            if (empty($new_password)) {
                $error = 'Vui lòng nhập mật khẩu mới.';
            } else if (strlen($new_password) < 6) {
                $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } else if ($new_password !== $confirm_password) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                // Update password and clear reset token
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                $stmt->execute([$hashed_password, $user['id']]);
                
                $message = 'Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập với mật khẩu mới.';
            }
        }
    } else {
        $error = 'Mã đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
    }
} else {
    $error = 'Mã đặt lại mật khẩu không được cung cấp.';
}

include '../views/layout/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-success"></i>
                        <h3 class="mt-2">Đặt Lại Mật Khẩu</h3>
                        <p class="text-muted">Nhập mật khẩu mới cho tài khoản</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($message); ?>
                            <div class="text-center mt-3">
                                <a href="login.php" class="btn btn-primary">Đăng Nhập Ngay</a>
                            </div>
                        </div>
                    <?php elseif ($valid_token): ?>
                        <form method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Đặt Lại Mật Khẩu</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <p class="mb-0"><a href="login.php">Quay lại đăng nhập</a></p>
                        <p class="mb-0"><a href="forgot_password_demo.php">Yêu cầu mã mới</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validate password confirmation
    document.getElementById('confirm_password')?.addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirm = this.value;
        
        if (confirm && password !== confirm) {
            this.setCustomValidity('Mật khẩu xác nhận không khớp');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php include '../views/layout/footer.php'; ?>