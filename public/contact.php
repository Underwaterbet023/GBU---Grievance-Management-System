<?php
require_once '../includes/auth.php';
if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin_dashboard.php" : "student_dashboard.php"));
    exit();
}

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real scenario, you'd use mail() or a library like PHPMailer.
    // For this demonstration, we'll just simulate it.
    $messageSent = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | GBU Grievance System</title>
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

        .contact-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-soft);
            height: 100%;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .contact-info-icon {
            width: 50px;
            height: 50px;
            background: rgba(128, 0, 0, 0.1);
            color: var(--gbu-maroon);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .form-control {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.1);
            background: #fdfdfd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.1);
            border-color: var(--gbu-maroon);
        }

        .btn-send {
            background: var(--gbu-maroon);
            color: var(--white);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-send:hover {
            background: var(--gbu-maroon-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 0, 0, 0.3);
            color: var(--white);
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
                <li class="nav-item"><a class="nav-link" href="about.php">About GBU</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login / Register</a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Get in Touch</h1>
        <p class="lead">We're here to support you. Reach out with any questions or feedback.</p>
    </div>
</header>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-card">
                    <h3 class="fw-bold mb-4">Contact Information</h3>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted small mb-0">Opp. Yamuna Expressway, Greater Noida,<br>Gautam Budh Nagar, UP - 201312 (INDIA)</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Phone Number</h6>
                            <p class="text-muted small mb-0">0120-2344200</p>
                            <p class="x-small text-muted">(9:30 am - 5:30 pm | Mon - Fri)</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Support</h6>
                            <p class="text-muted small mb-0">info@gbu.ac.in</p>
                        </div>
                    </div>

                    <div class="social-icons pt-4">
                        <a href="https://www.facebook.com/gbugrnoida.dic/" class="text-maroon me-3" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://x.com/gbugrnoida" class="text-maroon me-3" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="https://www.youtube.com/channel/UCOfkhzLuMRTfqSKMFr9LBCA" class="text-maroon" target="_blank"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="contact-card">
                    <h3 class="fw-bold mb-4">Send Us a Message</h3>
                    <?php if ($messageSent): ?>
                        <div class="alert alert-success border-0 shadow-sm rounded-4 py-3 mb-4">
                            <i class="fas fa-check-circle me-2"></i> Your message has been sent to <strong>info@gbu.ac.in</strong>. We'll get back to you soon!
                        </div>
                    <?php endif; ?>
                    <form action="contact.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted">YOUR NAME</label>
                                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted">EMAIL ADDRESS</label>
                                <input type="email" class="form-control" name="email" placeholder="email@gbu.ac.in" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label x-small fw-bold text-muted">SUBJECT</label>
                                <input type="text" class="form-control" name="subject" placeholder="What is this regarding?" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label x-small fw-bold text-muted">YOUR MESSAGE</label>
                                <textarea class="form-control" name="message" rows="5" placeholder="Detailed message..." required></textarea>
                            </div>
                            <div class="col-12 text-end pt-3">
                                <button type="submit" class="btn btn-send">SEND MESSAGE <i class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
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
