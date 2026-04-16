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
    <title>About GBU | Grievance Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gbu-maroon: #800000;
            --gbu-maroon-dark: #600000;
            --white: #ffffff;
            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f9fa;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        .navbar {
            background: var(--white);
            padding: 15px 0;
            box-shadow: var(--shadow-soft);
        }

        .nav-link {
            font-weight: 600;
            color: #444 !important;
            margin: 0 10px;
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
        }

        .page-header {
            background: linear-gradient(rgba(128, 0, 0, 0.6), rgba(128, 0, 0, 0.4)), url('images/Screenshot 2026-04-12 025517.png') no-repeat center center;
            background-size: cover;
            padding: 100px 0;
            color: var(--white);
            text-align: center;
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: transform 0.3s ease;
            height: 100%;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            color: var(--gbu-maroon);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--gbu-maroon);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            margin-bottom: 5px;
        }

        .section-title {
            color: var(--gbu-maroon);
            font-weight: 800;
            margin-bottom: 30px;
            text-align: center;
        }

        .value-icon {
            width: 60px;
            height: 60px;
            background: rgba(128, 0, 0, 0.1);
            color: var(--gbu-maroon);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        footer {
            background: #1a1a1a;
            color: #ccc;
            padding: 60px 0 30px;
            margin-top: 80px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="https://www.gbu.ac.in/Content/img/logo_gbu.png" alt="GBU Logo" height="50">
            <div class="ms-2 d-none d-sm-block">
                <span class="d-block fw-bold" style="font-size: 1rem; color: var(--gbu-maroon);">GAUTAM BUDDHA UNIVERSITY</span>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="about.php">About GBU</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login / Register</a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">About the University</h1>
        <p class="lead">A Legacy of Knowledge, A Vision for the Future</p>
    </div>
</header>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <a href="https://faculty.gbu.ac.in/" target="_blank" class="stat-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span class="stat-number">200+</span>
                    <span class="text-muted fw-bold">Faculty Members</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://csms.gbu.ac.in/" target="_blank" class="stat-card">
                    <i class="fas fa-user-graduate"></i>
                    <span class="stat-number">5000+</span>
                    <span class="text-muted fw-bold">Students</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://www.gbu.ac.in/page/campuslife" target="_blank" class="stat-card">
                    <i class="fas fa-campground"></i>
                    <span class="stat-number">511</span>
                    <span class="text-muted fw-bold">Acres Campus</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="https://www.gbu.ac.in/club" target="_blank" class="stat-card">
                    <i class="fas fa-icons"></i>
                    <span class="stat-number">15+</span>
                    <span class="text-muted fw-bold">Student Clubs</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm border-start border-4 border-maroon">
                    <h3 class="fw-bold mb-3"><i class="fas fa-eye me-2 text-maroon"></i>Our Vision</h3>
                    <p class="text-muted">A globally acclaimed integrated academic and research institution that creates a vibrant community of intellectuals and entrepreneurs endowed with Character, Creativity, Competence and Commitment.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm border-start border-4 border-maroon">
                    <h3 class="fw-bold mb-3"><i class="fas fa-bullseye me-2 text-maroon"></i>Our Mission</h3>
                    <p class="text-muted">To generate a community of scholars who can inspire meaningful transformations to ensure holistic growth and development of the society, through excellence in education and research.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title">Core Values</h2>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="value-icon mx-auto"><i class="fas fa-hand-holding-heart"></i></div>
                <h5 class="fw-bold">Compassion</h5>
            </div>
            <div class="col-md-3">
                <div class="value-icon mx-auto"><i class="fas fa-balance-scale"></i></div>
                <h5 class="fw-bold">Integrity</h5>
            </div>
            <div class="col-md-3">
                <div class="value-icon mx-auto"><i class="fas fa-lightbulb"></i></div>
                <h5 class="fw-bold">Innovation</h5>
            </div>
            <div class="col-md-3">
                <div class="value-icon mx-auto"><i class="fas fa-users"></i></div>
                <h5 class="fw-bold">Inclusivity</h5>
            </div>
        </div>
    </div>
</section>

<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 Gautam Buddha University. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
