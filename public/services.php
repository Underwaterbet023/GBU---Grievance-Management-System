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
    <title>Our Services | GBU Grievance System</title>
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

        .service-card {
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

        .service-card:hover {
            transform: translateY(-10px);
            border-color: var(--gbu-maroon);
            box-shadow: 0 15px 45px rgba(128, 0, 0, 0.1);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: rgba(128, 0, 0, 0.05);
            color: var(--gbu-maroon);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .service-icon.digiform-icon {
            background: #fdf2f2;
            border-radius: 24px;
            color: #800000;
        }

        .service-card:hover .service-icon {
            background: var(--gbu-maroon);
            color: var(--white);
        }

        .service-card:hover .service-icon.digiform-icon {
            background: var(--gbu-maroon);
            color: var(--white);
        }

        .service-card h5 {
            font-weight: 700;
            margin-bottom: 15px;
        }

        .service-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0;
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
                <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login / Register</a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Services</h1>
        <p class="lead">Supporting Student Life and Academic Excellence</p>
    </div>
</header>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <a href="https://ohms.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-hotel"></i></div>
                    <h5>Hostel Management</h5>
                    <p>Manage your residential accommodation, room bookings, and hostel-related queries through OHMS.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://exams.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-file-signature"></i></div>
                    <h5>Examination Portal</h5>
                    <p>Access exam schedules, download admit cards, view results, and stay updated with exam notices.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://csms.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-user-graduate"></i></div>
                    <h5>CSMS Portal</h5>
                    <p>Unified platform for student registration, fee payments, and administrative documentation.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/placements" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                    <h5>Placement Cell</h5>
                    <p>Explore career opportunities, internship programs, and corporate networking for students.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="university_schools.php" class="service-card">
                    <div class="service-icon"><i class="fas fa-university"></i></div>
                    <h5>University School Department</h5>
                    <p>Access various school departments including Biotechnology, Engineering, ICT, and more.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.facebook.com/p/Gautam-Buddha-University-Alumni-Association-61574830550202/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-users"></i></div>
                    <h5>Alumni Relations</h5>
                    <p>Stay connected with our global network of alumni and participate in university events.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://gbu.samarth.edu.in/index.php/site/login" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-user-circle"></i></div>
                    <h5>Student Portal</h5>
                    <p>Access your academic profile, internal marks, and university governance through Samarth eGov.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/academics/academic-programmes" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-book-reader"></i></div>
                    <h5>Academic Programmes</h5>
                    <p>Explore the diverse range of undergraduate, postgraduate, and research programmes offered at GBU.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://mygbu.in/db/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-clock"></i></div>
                    <h5>Live Timetable</h5>
                    <p>Stay updated with real-time class schedules and venue changes through the Student Portal Live Data.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://mygbu.in/schd/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-project-diagram"></i></div>
                    <h5>DIC Portal</h5>
                    <p>Access the Design Innovation Center portal for scheduling and innovation-related resources.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://digiforms.gbu.ac.in/student/login.php" target="_blank" class="service-card">
                    <div class="service-icon digiform-icon"><i class="fas fa-file-contract"></i></div>
                    <h5>DigiForms</h5>
                    <p>Paperless university operations through secure and efficient digital form submissions.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/page/recruitment" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-user-tie"></i></div>
                    <h5>Faculty/Staff Recruitment</h5>
                    <p>Explore career opportunities and current job openings for faculty and staff positions at GBU.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://abhivyanjana.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-mask"></i></div>
                    <h5>ABHIVYANJANA FESTA</h5>
                    <p>Experience the annual cultural festival celebrating art, culture, and student talent.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://hostels.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-building-user"></i></div>
                    <h5>Student Accommodation</h5>
                    <p>Information and resources regarding the university's residential and hostel facilities.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://library.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-book-open-reader"></i></div>
                    <h5>Central Library</h5>
                    <p>Access digital resources and catalogs of the Bodhisattva Dr. B.R. Ambedkar Library.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/page/meditation-center" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-om"></i></div>
                    <h5>Meditation Center</h5>
                    <p>Focus on inner peace and mindfulness at the Mahatma Jyotiba Phule Dhyan Kendra.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/page/sport-council" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-running"></i></div>
                    <h5>Sport Council</h5>
                    <p>Promoting physical excellence through diverse sports and state-of-the-art athletic facilities.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/facilities/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h5>Convention Facilities</h5>
                    <p>Access to auditoriums, convention centers, and university guest house services.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://nss.gbu.ac.in/" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <h5>National Service Scheme</h5>
                    <p>Join voluntary community service programs and social development initiatives.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/about/forums" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-comments"></i></div>
                    <h5>Forums & Associations</h5>
                    <p>Connect with various student societies, professional forums, and campus associations.</p>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="https://www.gbu.ac.in/public/newsletter" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fas fa-newspaper"></i></div>
                    <h5>GBU News</h5>
                    <p>Stay updated with the latest newsletters, academic bulletins, and university announcements.</p>
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
