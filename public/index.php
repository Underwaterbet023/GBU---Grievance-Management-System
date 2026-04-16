<?php
require_once '../includes/auth.php';
if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin_dashboard.php" : "student_dashboard.php"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBU Grievance Management System | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --gbu-maroon: #800000;
            --gbu-maroon-dark: #600000;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* Navigation */
        .navbar {
            background: var(--white);
            padding: 15px 0;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            font-weight: 600;
            color: #444 !important;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--gbu-maroon) !important;
        }

        .btn-login {
            background: var(--gbu-maroon);
            color: var(--white) !important;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--gbu-maroon-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 0, 0, 0.3);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 100vh;
            min-height: 600px;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            color: var(--white);
            transition: background-image 1s ease-in-out;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.2));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-hero-primary {
            background: var(--gbu-maroon);
            color: var(--white);
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            margin-right: 15px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-hero-outline {
            border: 2px solid var(--white);
            color: var(--white);
            padding: 13px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-hero-primary:hover, .btn-hero-outline:hover {
            transform: scale(1.05);
            color: var(--white);
        }

        /* Quick Access Section */
        .quick-access {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #333;
        }

        .glass-card:hover {
            transform: translateY(-10px);
            background: var(--white);
            border-color: var(--gbu-maroon);
        }

        .glass-card i {
            font-size: 2.5rem;
            color: var(--gbu-maroon);
            margin-bottom: 15px;
        }

        .glass-card h5 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* About Section */
        .about-section {
            padding: 120px 0;
            position: relative;
            background: url('images/Screenshot 2026-04-12 025517.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            color: var(--white);
        }

        .about-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.3));
            z-index: 1;
        }

        .about-content {
            position: relative;
            z-index: 2;
        }

        .about-section .section-title {
            color: var(--white);
            font-weight: 800;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
            font-size: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .about-section .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: var(--white);
        }

        .about-main-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .about-glass-cards {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 40px;
            margin-top: 50px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .about-feature {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
        }

        .about-feature i {
            font-size: 1.8rem;
            color: var(--white);
            background: rgba(255,255,255,0.1);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .about-feature h5 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .about-feature p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }

        /* How It Works */
        .how-it-works {
            padding: 80px 0;
            background: var(--gbu-maroon);
            color: var(--white);
        }

        .step-item {
            text-align: center;
            padding: 20px;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
            border: 2px solid rgba(255,255,255,0.2);
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: #ccc;
            padding: 80px 0 30px;
        }

        .footer-logo img {
            height: 60px;
            margin-bottom: 20px;
        }

        .footer-links h5 {
            color: var(--white);
            margin-bottom: 25px;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links ul li {
            margin-bottom: 12px;
        }

        .footer-links ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links ul li a:hover {
            color: var(--white);
        }

        .social-icons a {
            color: var(--white);
            font-size: 1.5rem;
            margin-right: 20px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .social-icons a:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1.1rem; }
            .navbar-collapse {
                background: var(--white);
                padding: 20px;
                border-radius: 15px;
                margin-top: 15px;
                box-shadow: var(--shadow-soft);
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="https://www.gbu.ac.in/Content/img/logo_gbu.png" alt="GBU Logo">
            <div class="ms-2 d-none d-sm-block">
                <span class="d-block fw-bold" style="font-size: 1rem; color: var(--gbu-maroon);">GAUTAM BUDDHA UNIVERSITY</span>
                <span class="text-muted x-small">Grievance Management System</span>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About GBU</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login / Register</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section" id="heroSlider">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content animate__animated animate__fadeInLeft">
            <h1 class="hero-title">Grievance<br>Management System</h1>
            <p class="hero-subtitle">Your Voice. Our Commitment. A premium, transparent, and efficient platform to raise and resolve university concerns.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-hero-primary">Raise a Grievance</a>
                <a href="login.php" class="btn btn-hero-outline">Track Grievance</a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroSection = document.getElementById('heroSlider');
    
    const sliderImages = [
        'images/gettyimages-1010436712-612x612.jpeg',
        'images/_dsf8724a.jpg',
        'images/banner1.jpg',
        'images/banner2.jpg',
        'images/banner1 Gautam Buddha University.png'
    ];
    
    let currentIndex = 0;

    function rotateImages() {
        currentIndex = (currentIndex + 1) % sliderImages.length;
        
        // Update Hero Slider
        if (heroSection) {
            heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.2)), url('${sliderImages[currentIndex]}')`;
        }
    }

    // Set initial image
    if (heroSection) heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.2)), url('${sliderImages[0]}')`;

    // Change every 6 seconds
    setInterval(rotateImages, 6000);
});
</script>

<!-- Quick Access -->
<section class="quick-access">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <a href="https://www.gbu.ac.in" target="_blank" class="glass-card">
                    <i class="fas fa-university"></i>
                    <h5>GBU Official</h5>
                    <span class="text-muted x-small">gbu.ac.in</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://exams.gbu.ac.in/" target="_blank" class="glass-card">
                    <i class="fas fa-file-invoice"></i>
                    <h5>Exams</h5>
                    <span class="text-muted x-small">exams.gbu.ac.in</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://csms.gbu.ac.in/" target="_blank" class="glass-card">
                    <i class="fas fa-user-graduate"></i>
                    <h5>CSMS</h5>
                    <span class="text-muted x-small">csms.gbu.ac.in</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://ohms.gbu.ac.in/" target="_blank" class="glass-card">
                    <i class="fas fa-hotel"></i>
                    <h5>OHMS</h5>
                    <span class="text-muted x-small">ohms.gbu.ac.in</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="about-overlay"></div>
    <div class="container about-content">
        <div class="row mb-5">
            <div class="col-lg-8">
                <h6 class="section-title">About Us</h6>
                <h2 class="about-main-title">GBU Grievance Management System</h2>
                <p class="lead mb-0" style="max-width: 800px; opacity: 0.9;">We provide a transparent, efficient, and confidential platform to help students, faculty, and staff raise and resolve their grievances promptly and fairly.</p>
            </div>
        </div>
        
        <div class="about-glass-cards">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="about-feature">
                        <i class="fas fa-edit"></i>
                        <div>
                            <h5>Raise Grievances</h5>
                            <p>Easily submit complaints for quick action</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-feature">
                        <i class="fas fa-search"></i>
                        <div>
                            <h5>Track Progress</h5>
                            <p>Monitor the status of your grievance in real-time.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-feature">
                        <i class="fas fa-history"></i>
                        <div>
                            <h5>Timely Resolution</h5>
                            <p>Ensuring all grievances are resolved efficiently.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-feature">
                        <i class="fas fa-lock"></i>
                        <div>
                            <h5>Confidentiality</h5>
                            <p>Your information is kept private and secure.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Removed about-img styles as they are no longer needed */
</style>

<section class="py-5" style="background: #f8f9fa;">
    <div class="container text-center">
        <h3 class="fw-bold mb-3">Dedicated to Fairness and Accountability</h3>
        <p class="text-muted mx-auto" style="max-width: 800px;">Our Grievance Management System is committed to providing a just and responsive environment. We ensure that every voice is heard and every issue is addressed with the highest level of integrity.</p>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">How It Works</h2>
        <div class="row">
            <div class="col-md-3 step-item">
                <div class="step-icon"><i class="fas fa-user-plus"></i></div>
                <h5>1. Register/Login</h5>
                <p class="opacity-75 small">Create your account using your university credentials.</p>
            </div>
            <div class="col-md-3 step-item">
                <div class="step-icon"><i class="fas fa-pen-fancy"></i></div>
                <h5>2. Raise Grievance</h5>
                <p class="opacity-75 small">Fill the detailed form with your specific concern.</p>
            </div>
            <div class="col-md-3 step-item">
                <div class="step-icon"><i class="fas fa-sync-alt"></i></div>
                <h5>3. Review & Action</h5>
                <p class="opacity-75 small">Administration reviews and initiates the resolution protocol.</p>
            </div>
            <div class="col-md-3 step-item">
                <div class="step-icon"><i class="fas fa-check-double"></i></div>
                <h5>4. Track & Resolve</h5>
                <p class="opacity-75 small">Monitor real-time progress until final resolution.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="footer-logo">
                    <img src="https://www.gbu.ac.in/Content/img/logo_gbu.png" alt="GBU Logo">
                </div>
                <p class="small">Gautam Buddha University, Greater Noida. An integrated academic and research institution creating a vibrant community of intellectuals.</p>
                <div class="social-icons mt-4">
                    <a href="https://www.facebook.com/gbugrnoida.dic/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.youtube.com/channel/UCOfkhzLuMRTfqSKMFr9LBCA" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="https://x.com/gbugrnoida" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="mailto:info@gbu.ac.in"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0 footer-links">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About GBU</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="faqs.php">FAQs</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4 mb-md-0 footer-links">
                <h5>Portals</h5>
                <ul>
                    <li><a href="https://csms.gbu.ac.in/"><i class="fas fa-user-graduate me-2"></i>CSMS Portal</a></li>
                    <li><a href="https://exams.gbu.ac.in/"><i class="fas fa-file-invoice me-2"></i>Examination Portal</a></li>
                    <li><a href="https://ohms.gbu.ac.in/"><i class="fas fa-hotel me-2"></i>Hostel Management</a></li>
                    <li><a href="https://digiforms.gbu.ac.in/"><i class="fas fa-file-contract me-2"></i>DigiForms</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 footer-links">
                <h5>Contact Info</h5>
                <p class="small mb-1">Opp. Yamuna Expressway, Greater Noida</p>
                <p class="small mb-1">Gautam Budh Nagar, UP - 201312</p>
                <p class="small mb-1">Phone: 0120-2344200</p>
                <p class="small">Email: info@gbu.ac.in</p>
            </div>
        </div>
        <hr class="mt-5 border-secondary">
        <div class="text-center mt-4">
            <p class="small mb-0">&copy; 2024 Gautam Buddha University. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

