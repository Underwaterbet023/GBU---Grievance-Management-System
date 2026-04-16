<?php
require_once '../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University School Departments | GBU Grievance System</title>
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
            padding: 80px 0;
            color: var(--white);
            text-align: center;
        }

        .school-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .school-card:hover {
            transform: translateY(-10px);
            border-color: var(--gbu-maroon);
            box-shadow: 0 15px 45px rgba(128, 0, 0, 0.1);
        }

        .school-icon {
            width: 80px;
            height: 80px;
            background: rgba(128, 0, 0, 0.05);
            color: var(--gbu-maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .school-card:hover .school-icon {
            background: var(--gbu-maroon);
            color: var(--white);
        }

        .school-card h5 {
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        footer {
            background: #1a1a1a;
            color: #ccc;
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .breadcrumb-section {
            background: #fff;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .breadcrumb {
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: var(--gbu-maroon);
            text-decoration: none;
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
                <li class="nav-item"><a class="nav-link" href="about.php">About GBU</a></li>
                <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <?php if (!isLoggedIn()): ?>
                <a href="login.php" class="btn btn-login">Login / Register</a>
            <?php else: ?>
                <a href="<?php echo isAdmin() ? 'admin_dashboard.php' : 'student_dashboard.php'; ?>" class="btn btn-login">Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">University School Departments</h1>
        <p class="lead">Explore the various schools at Gautam Buddha University</p>
    </div>
</header>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="services.php">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">University Schools</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- School of Biotechnology -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/sob" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-flask"></i></div>
                    <h5>School of Biotechnology</h5>
                </a>
            </div>

            <!-- School of Buddhist Studies and Civilization -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/sobs" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-vihara"></i></div>
                    <h5>School of Buddhist Studies</h5>
                </a>
            </div>

            <!-- School of Engineering -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/soe" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-cog"></i></div>
                    <h5>School of Engineering</h5>
                </a>
            </div>

            <!-- School of Humanities and Social Sciences -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/soh" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-book"></i></div>
                    <h5>School of Humanities</h5>
                </a>
            </div>

            <!-- School of Information & Communication Technology -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/USICT/index.html" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-laptop-code"></i></div>
                    <h5>School of ICT</h5>
                </a>
            </div>

            <!-- School of Law Justice and Governance -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/soljg" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-gavel"></i></div>
                    <h5>School of Law</h5>
                </a>
            </div>

            <!-- School of Management -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/som" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-briefcase"></i></div>
                    <h5>School of Management</h5>
                </a>
            </div>

            <!-- School of Vocational Studies and Applied Sciences -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.gbu.ac.in/school/sovs" target="_blank" class="school-card">
                    <div class="school-icon"><i class="fas fa-microscope"></i></div>
                    <h5>School of Vocational Studies</h5>
                </a>
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