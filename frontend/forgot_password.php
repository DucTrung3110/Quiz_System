<?php
require_once '../config/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Vui lòng nhập địa chỉ email.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Địa chỉ email không hợp lệ.';
    } else {
        global $pdo;
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
            
            // Update user with reset token
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $stmt->execute([$reset_token, $expires, $email]);
            
            $message = 'Liên kết đặt lại mật khẩu đã được tạo. Sử dụng mã sau để đặt lại mật khẩu: ' . $reset_token;
        } else {
            $error = 'Không tìm thấy tài khoản với địa chỉ email này.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Quên Mật Khẩu</h2>
                <p>Nhập địa chỉ email để đặt lại mật khẩu</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Địa chỉ Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
                </div>
            </form>

            <div class="auth-links">
                <a href="login.php">Quay lại đăng nhập</a>
                <a href="register.php">Đăng ký tài khoản mới</a>
            </div>
        </div>
    </div>
</body>
</html>