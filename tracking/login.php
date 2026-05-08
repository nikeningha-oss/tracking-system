<?php
// Start session
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
require_once 'connect.php';

$error = '';
$success = '';

// Check if coming from registration
if(isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success = "Registration successful! Please login with your credentials.";
}

// Process login form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validation
    if(empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check user credentials
        $query = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $query);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Set remember me cookie (30 days)
                if($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (86400 * 30); // 30 days
                    
                    // Store token in database (you'll need a remember_tokens table)
                    // For now, just set cookie
                    setcookie('remember_token', $token, $expiry, '/');
                    setcookie('user_email', $email, $expiry, '/');
                }
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}

// Check for remember me cookie
$remember_email = '';
if(isset($_COOKIE['user_email'])) {
    $remember_email = $_COOKIE['user_email'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TrackMaster Pro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* MATURE COLOR SCHEME - Same as dashboard and register */
        :root {
            --primary: #1e2b4f;
            --primary-light: #2a3a66;
            --primary-dark: #121f3a;
            --secondary: #2c3e5c;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #6366f1;
            --dark: #0f172a;
            --light: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-light: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements - matching register page */
        .bg-element {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            pointer-events: none;
        }

        .element-1 {
            top: -100px;
            right: -100px;
            animation: float 20s infinite;
        }

        .element-2 {
            bottom: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            animation: float 25s infinite reverse;
        }

        .element-3 {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            opacity: 0.02;
            animation: pulse 15s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-30px, 30px) rotate(240deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.02; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.03; }
        }

        /* Main container */
        .login-container {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
        }

        /* Brand section */
        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand a {
            text-decoration: none;
        }

        .brand h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            margin-bottom: 10px;
        }

        .brand h1 span {
            color: var(--warning);
        }

        .brand p {
            color: rgba(255,255,255,0.8);
            font-size: 1rem;
        }

        /* Login card */
        .login-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .login-header p a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .login-header p a:hover {
            text-decoration: underline;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            z-index: 10;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid var(--border-light);
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 5px 20px rgba(30, 43, 79, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        /* Password field with toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            z-index: 20;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        /* Remember me and forgot password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .remember-me span {
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Buttons */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 43, 79, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .btn-login:hover i {
            transform: translateX(5px);
        }

        /* Social login */
        .social-login {
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 20px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: var(--border-light);
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border: 2px solid var(--border-light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1.3rem;
        }

        .social-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Alert messages */
        .alert {
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert i {
            font-size: 1.2rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Register link */
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--border-light);
        }

        .register-link p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .register-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Demo credentials */
        .demo-credentials {
            background: rgba(30, 43, 79, 0.05);
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
        }

        .demo-credentials h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .demo-credentials p {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 5px;
            font-family: monospace;
        }

        .demo-credentials i {
            color: var(--primary);
            width: 20px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .brand h1 {
                font-size: 2rem;
            }
            
            .bg-element {
                display: none;
            }
            
            .form-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Animated Background Elements - Matching register page -->
    <div class="bg-element element-1"></div>
    <div class="bg-element element-2"></div>
    <div class="bg-element element-3"></div>

    <div class="login-container">
        <!-- Brand -->
        <div class="brand">
            <a href="index.php">
                <h1>Track<span>Master</span></h1>
            </a>
            <p>Welcome back! Please login to your account</p>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>

            <!-- Error/Success Messages -->
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm">
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="nike@example.com"
                               value="<?php echo htmlspecialchars($remember_email); ?>"
                               required>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••"
                               required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot_password.php" class="forgot-password">
                        <i class="fas fa-question-circle me-1"></i>
                        Forgot Password?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <span>Login to Dashboard</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Demo Credentials (for testing) -->
            <div class="demo-credentials">
                <h6><i class="fas fa-info-circle me-1"></i> Demo Credentials</h6>
                <p><i class="fas fa-envelope"></i> demo@trackmaster.com</p>
                <p><i class="fas fa-lock"></i> password123</p>
            </div>

            <!-- Social Login -->
            <div class="social-login">
                <p>Or continue with</p>
                <div class="social-buttons">
                    <a href="#" class="social-btn"><i class="fab fa-google"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <!-- Register Link -->
            <div class="register-link">
                <p>New to TrackMaster? <a href="register.php">Create an account</a></p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Form validation
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const pass = document.getElementById('password').value;
            
            if(!email || !pass) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });

        // Smooth animations for form fields
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--primary)';
            });
            
            input.addEventListener('blur', function() {
                if(!this.value) {
                    this.parentElement.querySelector('.input-icon').style.color = 'var(--text-secondary)';
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>