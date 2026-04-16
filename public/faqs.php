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
    <title>FAQs | GBU Grievance System</title>
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

        .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 15px !important;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .accordion-button {
            padding: 20px 25px;
            font-weight: 700;
            color: #333;
            background: var(--white);
        }

        .accordion-button:not(.collapsed) {
            background: var(--gbu-maroon);
            color: var(--white);
            box-shadow: none;
        }

        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .accordion-body {
            padding: 25px;
            color: #666;
            line-height: 1.6;
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
                <li class="nav-item"><a class="nav-link active" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login / Register</a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Frequently Asked Questions</h1>
        <p class="lead">Find Quick Answers to Common Queries</p>
    </div>
</header>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I register for the Grievance System?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can register by clicking the "Login / Register" button on the top right. Use your university enrollment number and official email address to create a secure account.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What is the typical resolution time?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Most grievances are acknowledged within 24-48 hours. The final resolution time depends on the complexity of the issue, but we strive to resolve all cases within 7-10 working days.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I track my grievance status in real-time?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, once logged in, you can visit your dashboard to see the current status, which department is handling it, and any updates provided by the administrator.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Is my identity kept confidential?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely. The system is designed with secure access protocols. Your information is only visible to the specific administrator assigned to your case.
                            </div>
                        </div>
                    </div>
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
