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
                global $pdo;
                
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Đặt Lại Mật Khẩu</h2>
                <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                    <div class="auth-links" style="margin-top: 15px;">
                        <a href="login.php" class="btn btn-primary">Đăng Nhập Ngay</a>
                    </div>
                </div>
            <?php elseif ($valid_token): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Đặt Lại Mật Khẩu</button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="auth-links">
                <a href="login.php">Quay lại đăng nhập</a>
                <a href="forgot_password.php">Yêu cầu mã mới</a>
            </div>
        </div>
    </div>

    <script>
        // Validate password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>