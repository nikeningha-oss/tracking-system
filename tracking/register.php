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

// Process registration form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validation
    if(empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(!$terms) {
        $error = "You must agree to the Terms & Conditions";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($con, $check_query);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered. Please login.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $insert_query = "INSERT INTO users (name, email, password, role, created_at) 
                            VALUES ('$name', '$email', '$hashed_password', 'user', NOW())";
            
            if(mysqli_query($con, $insert_query)) {
                $success = "Registration successful! You can now login.";
                // Clear form data
                $_POST = array();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TrackMaster Pro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* MATURE COLOR SCHEME - Same as dashboard */
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
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
            left: -100px;
            animation: float 20s infinite;
        }

        .element-2 {
            bottom: -100px;
            right: -100px;
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
        .register-container {
            width: 100%;
            max-width: 500px;
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

        /* Register card */
        .register-card {
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

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h2 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .register-header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .register-header p a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-header p a:hover {
            text-decoration: underline;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 20px;
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

        /* Password strength meter */
        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 5px;
            background: var(--border-light);
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-progress {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Terms checkbox */
        .terms-group {
            margin: 25px 0;
        }

        .terms-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .terms-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .terms-checkbox span {
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .terms-checkbox a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .terms-checkbox a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn-register {
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

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 43, 79, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register i {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .btn-register:hover i {
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

        /* Login link */
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--border-light);
        }

        .login-link p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .register-card {
                padding: 30px 20px;
            }
            
            .brand h1 {
                font-size: 2rem;
            }
            
            .bg-element {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Animated Background Elements -->
    <div class="bg-element element-1"></div>
    <div class="bg-element element-2"></div>
    <div class="bg-element element-3"></div>

    <div class="register-container">
        <!-- Brand -->
        <div class="brand">
            <a href="index.php">
                <h1>Track<span>Master</span></h1>
            </a>
            <p>Create your account to start tracking</p>
        </div>

        <!-- Register Card -->
        <div class="register-card">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Already have an account? <a href="login.php">Sign in</a></p>
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

            <!-- Registration Form -->
            <form method="POST" action="" id="registerForm">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="nike junior"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               required>
                    </div>
                </div>

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
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
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
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-progress" id="strengthProgress"></div>
                        </div>
                        <span class="strength-text" id="strengthText">Enter password</span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="••••••••"
                               required>
                    </div>
                    <div id="passwordMatch" style="font-size: 0.8rem; margin-top: 5px;"></div>
                </div>

                <!-- Terms and Conditions -->
                <div class="terms-group">
                    <label class="terms-checkbox">
                        <input type="checkbox" name="terms" id="terms" checked>
                        <span>
                            I agree to the <a href="#" target="_blank">Terms & Conditions</a> and 
                            <a href="#" target="_blank">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <!-- Register Button -->
                <button type="submit" class="btn-register" id="submitBtn">
                    <span>Create Account</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Social Login -->
            <div class="social-login">
                <p>Or register with</p>
                <div class="social-buttons">
                    <a href="#" class="social-btn"><i class="fab fa-google"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <!-- Login Link -->
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>

    <!-- JavaScript for validation and interactivity -->
    <script>
        // Password strength checker
        const password = document.getElementById('password');
        const strengthProgress = document.getElementById('strengthProgress');
        const strengthText = document.getElementById('strengthText');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordStrength(password) {
            let strength = 0;
            
            if(password.length >= 6) strength += 20;
            if(password.length >= 8) strength += 20;
            if(/[a-z]/.test(password)) strength += 20;
            if(/[A-Z]/.test(password)) strength += 20;
            if(/[0-9!@#$%^&*]/.test(password)) strength += 20;
            
            return Math.min(strength, 100);
        }

        function updateStrengthIndicator() {
            const strength = checkPasswordStrength(password.value);
            strengthProgress.style.width = strength + '%';
            
            if(password.value.length === 0) {
                strengthProgress.style.backgroundColor = 'transparent';
                strengthText.textContent = 'Enter password';
                strengthText.style.color = 'var(--text-secondary)';
            } else if(strength < 40) {
                strengthProgress.style.backgroundColor = '#ef4444';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#ef4444';
            } else if(strength < 70) {
                strengthProgress.style.backgroundColor = '#f59e0b';
                strengthText.textContent = 'Medium password';
                strengthText.style.color = '#f59e0b';
            } else {
                strengthProgress.style.backgroundColor = '#10b981';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#10b981';
            }
        }

        function checkPasswordMatch() {
            if(confirmPassword.value.length === 0) {
                passwordMatch.textContent = '';
                passwordMatch.style.color = '';
            } else if(password.value === confirmPassword.value) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#10b981';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#ef4444';
            }
        }

        password.addEventListener('input', function() {
            updateStrengthIndicator();
            checkPasswordMatch();
        });

        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Form validation before submit
        form.addEventListener('submit', function(e) {
            const terms = document.getElementById('terms');
            
            if(!terms.checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions to continue.');
            }
            
            if(password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
            
            if(checkPasswordStrength(password.value) < 40) {
                e.preventDefault();
                alert('Please choose a stronger password (min 8 chars with uppercase & numbers)');
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
    </script>

    <!-- Bootstrap JS (optional, for certain features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>