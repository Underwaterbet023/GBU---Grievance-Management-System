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
    <title>Access Portal | GBU Grievance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="auth-page">

<div class="auth-bg-decor">
    <div class="molecule-1"></div>
    <div class="molecule-2"></div>
</div>

<div class="auth-container animate__animated animate__zoomIn">
    <div class="auth-left d-none d-lg-flex">
        <div class="auth-hero-content text-center d-flex flex-column justify-content-center align-items-center w-100">
            <img src="https://www.gbu.ac.in/Content/img/logo_gbu.png" alt="GBU Logo" class="mb-4" style="width: 120px; filter: drop-shadow(0 0 10px rgba(0,217,255,0.3));">
            <h2 class="display-5 fw-bold mb-3 text-white">Gautam Buddha<br>University</h2>
            <h3 class="h4 fw-bold mb-4 text-white-50">Grievance Management System</h3>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrapper">
            <div class="mb-5">
                <h2 class="fw-bold mb-2">Access Portal</h2>
                <p class="text-white-50 small">Enter your secure identifiers to begin.</p>
            </div>

            <div id="loginAlert" class="alert d-none border-0 rounded-4 x-small fw-bold py-3 mb-4"></div>

            <form id="loginForm">
                <div class="mb-4">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">USER IDENTIFIER</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="text" class="form-control" id="identifier" placeholder="operator@nexus.core" required>
                    </div>
                </div>
                
                <div class="mb-5">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label x-small fw-bold text-white-50 tracking-widest m-0">SECRET KEY</label>
                        <a href="#" class="text-primary text-decoration-none x-small fw-bold">RECOVER</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" id="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-lg mb-4" id="loginBtn">
                    EXECUTE LOGIN <i class="fas fa-arrow-right-long ms-2"></i>
                </button>
                
                <div class="text-center">
                    <p class="text-white-50 small">New operative? <a href="register.php" class="text-primary fw-bold text-decoration-none ms-1">REGISTER SESSION</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        const btn = $('#loginBtn');
        const originalText = btn.html();
        btn.html('<i class="fas fa-dna fa-spin me-2"></i> RECOGNIZING...').prop('disabled', true);
        
        const data = {
            identifier: $('#identifier').val(),
            password: $('#password').val()
        };
        
        $.ajax({
            url: 'api/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    $('#loginAlert').removeClass('d-none alert-danger').addClass('alert-success bg-success text-white shadow-lg border-0')
                        .html('<i class="fas fa-circle-check me-2"></i> IDENTITY CONFIRMED. REDIRECTING...');
                    setTimeout(() => window.location.href = response.redirect, 1000);
                } else {
                    $('#loginAlert').removeClass('d-none alert-success').addClass('alert-danger bg-danger text-white shadow-lg border-0')
                        .html('<i class="fas fa-triangle-exclamation me-2"></i> ' + (response.message || 'AUTHENTICATION FAILED'));
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                $('#loginAlert').removeClass('d-none alert-success').addClass('alert-danger bg-danger text-white shadow-lg border-0')
                    .html('<i class="fas fa-circle-exclamation me-2"></i> SYSTEM COMMUNICATION ERROR');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
</body>
</html>
