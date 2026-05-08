<?php
// Start session to check if user is already logged in
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMaster Pro - Professional Tracking System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
            color: var(--text-primary);
            overflow-x: hidden;
            background-color: white;
        }

        /* Navigation */
        .navbar {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .navbar.scrolled {
            padding: 15px 0;
            box-shadow: 0 5px 30px rgba(30, 43, 79, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary) !important;
        }

        .navbar-brand span {
            color: var(--accent);
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-primary) !important;
            margin: 0 15px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-login {
            padding: 10px 25px;
            border: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
            margin-right: 10px;
        }

        .btn-login:hover {
            background: var(--primary);
            color: white;
        }

        .btn-register {
            padding: 10px 25px;
            background: var(--primary);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
            border: 2px solid var(--primary);
        }

        .btn-register:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }

        /* Hero Section */
        .hero {
            padding: 150px 0 100px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(30,43,79,0.03) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59,130,246,0.03) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
            color: var(--primary);
        }

        .hero-title span {
            color: var(--accent);
            position: relative;
        }

        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(59,130,246,0.2);
            z-index: -1;
        }

        .hero-text {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .hero-stats {
            display: flex;
            gap: 50px;
            margin-top: 50px;
        }

        .stat-item h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-item p {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .hero-image {
            position: relative;
            z-index: 1;
        }

        .hero-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(30,43,79,0.15);
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(30,43,79,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: float 3s ease-in-out infinite;
        }

        .floating-card i {
            font-size: 1.8rem;
            color: var(--success);
        }

        .card-1 {
            top: 20%;
            left: -30px;
        }

        .card-2 {
            bottom: 20%;
            right: -30px;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 70px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid var(--border-light);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(30,43,79,0.1);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: rgba(30,43,79,0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .feature-icon i {
            font-size: 2.5rem;
            color: var(--primary);
        }

        .feature-card h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* How It Works */
        .how-it-works {
            padding: 100px 0;
            background: var(--light);
        }

        .step-card {
            text-align: center;
            padding: 40px 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 auto 25px;
        }

        .step-card h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .step-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.03)"/></svg>');
            opacity: 0.1;
        }

        .cta-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .btn-cta {
            padding: 15px 40px;
            background: white;
            color: var(--primary);
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: inline-block;
            border: 2px solid white;
        }

        .btn-cta:hover {
            background: transparent;
            color: white;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 70px 0 30px;
        }

        .footer h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 50px;
            margin-top: 50px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }
            
            .floating-card {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                Track<span>Master</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="ms-lg-4 mt-3 mt-lg-0">
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">
                        Track Anything,
                        <span>Anywhere</span>
                        in Real-Time
                    </h1>
                    <p class="hero-text">
                        Professional tracking system for businesses and individuals. 
                        Monitor your shipments, packages, and assets with precision and ease.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="register.php" class="btn-register">Start Free Trial</a>
                        <a href="#features" class="btn-login">Learn More</a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <h3>10K+</h3>
                            <p>Active Users</p>
                        </div>
                        <div class="stat-item">
                            <h3>50K+</h3>
                            <p>Items Tracked</p>
                        </div>
                        <div class="stat-item">
                            <h3>99.9%</h3>
                            <p>Uptime</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-image" data-aos="fade-left">
                    <div class="position-relative">
                        <img src="https://placehold.co/600x400/1e2b4f/white?text=Tracking+Dashboard" alt="Dashboard Preview">
                        
                        <!-- Floating Cards -->
                        <div class="floating-card card-1">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <small>Delivered</small>
                                <h6 class="mb-0">2,847 items</h6>
                            </div>
                        </div>
                        
                        <div class="floating-card card-2">
                            <i class="fas fa-truck"></i>
                            <div>
                                <small>In Transit</small>
                                <h6 class="mb-0">1,234 items</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Why Choose TrackMaster?</h2>
                <p>Powerful features that make tracking simple and efficient</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Real-Time GPS</h4>
                        <p>Track your items in real-time with precise GPS location updates every minute.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Instant Alerts</h4>
                        <p>Get notified instantly when status changes or items reach their destination.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analytics</h4>
                        <p>Detailed reports and analytics to optimize your tracking operations.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Secure</h4>
                        <p>Enterprise-grade security to protect your tracking data and privacy.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>How It Works</h2>
                <p>Get started in just three simple steps</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Create Account</h4>
                        <p>Sign up for free and set up your profile in less than 2 minutes.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Add Items</h4>
                        <p>Enter tracking numbers and details for items you want to monitor.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Start Tracking</h4>
                        <p>Monitor everything in real-time from your personalized dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Ready to Start Tracking?</h2>
                <p>Join thousands of satisfied users and streamline your tracking today.</p>
                <a href="register.php" class="btn-cta">Get Started Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>TrackMaster Pro</h5>
                    <p style="color: rgba(255,255,255,0.6); line-height: 1.8;">
                        Professional tracking solution for businesses and individuals. Monitor your assets with confidence.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 offset-lg-1">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5>Support</h5>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Business Ave</li>
                        <li><i class="fas fa-phone me-2"></i> +237674576684/+237673576684</li>
                        <li><i class="fas fa-envelope me-2"></i> info@trackmaster.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TrackMaster Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>