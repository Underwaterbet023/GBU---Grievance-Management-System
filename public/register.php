<?php
require_once '../includes/auth.php';
// If already logged in, redirect based on role
if (isLoggedIn()) {
    header("Location: student_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antigravity GMS | Enlist Operative</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Styles integrated into style.css -->
</head>
<body class="auth-page">

<div class="auth-bg-decor">
    <div class="molecule-1"></div>
    <div class="molecule-2"></div>
</div>

<div class="auth-container animate__animated animate__fadeIn">
    <div class="auth-left d-none d-lg-flex">
        <div class="auth-hero-content">
            <div class="hero-chip">VERSION 2.0 PROTOCOL</div>
            <h1 class="display-4 fw-bold mb-4">Join the <br><span class="text-gradient">Antigravity</span> Elite.</h1>
            <p class="lead text-white-50 mb-5">Create your operative profile to start submitting and monitoring grievances with unmatched efficiency.</p>
            
            <div class="hero-features mt-auto">
                <div class="d-flex align-items-center mb-4 gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-20 p-2"><i class="fas fa-bolt-lightning text-primary"></i></div>
                    <span class="small fw-semibold opacity-75">Instant Data Synchronization</span>
                </div>
                <div class="d-flex align-items-center mb-4 gap-3">
                    <div class="rounded-circle bg-info bg-opacity-20 p-2"><i class="fas fa-eye text-info"></i></div>
                    <span class="small fw-semibold opacity-75">Full Resolution Transparency</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-20 p-2"><i class="fas fa-user-secret text-warning"></i></div>
                    <span class="small fw-semibold opacity-75">Encrypted Personal Data</span>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="mb-5">
            <h2 class="fw-bold mb-2">Enlistment Portal</h2>
            <p class="text-white-50 small">Fill the data fields to initialize your account.</p>
        </div>

        <div id="registerAlert" class="alert d-none border-0 rounded-4 x-small fw-bold py-3 mb-4"></div>

        <form id="registerForm">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">OPERATIVE NAME</label>
                    <input type="text" class="form-control" id="name" placeholder="John Doe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">ROLL IDENTIFIER</label>
                    <input type="text" class="form-control" id="roll_number" placeholder="21CSU001" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">NETWORK EMAIL (UNIVERSITY)</label>
                <input type="email" class="form-control" id="email" placeholder="student@gbu.ac.in" required>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">DEPARTMENT</label>
                    <select class="form-select" id="department" required>
                        <option value="" disabled selected>Select Sector</option>
                        <option value="Biotechnology">Biotechnology</option>
                        <option value="ICT">ICT</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Management">Management</option>
                        <option value="Law">Law</option>
                        <option value="Humanities">Humanities</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">HABITAT (HOSTEL)</label>
                    <select class="form-select" id="hostel" required>
                        <option value="" disabled selected>Select Location</option>
                        <option value="Birsa Munda">Birsa Munda</option>
                        <option value="Tulsidas">Tulsidas</option>
                        <option value="Savitri Bai">Savitri Bai</option>
                        <option value="Day Scholar">Day Scholar</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">ACCESS SECRET</label>
                    <input type="password" class="form-control" id="password" placeholder="••••••••" required minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label x-small fw-bold text-white-50 tracking-widest mb-2">VERIFY SECRET</label>
                    <input type="password" class="form-control" id="confirm_password" placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary-custom w-100 shadow-lg mb-4" id="registerBtn">
                INITIALIZE OPERATIVE <i class="fas fa-plus-circle ms-2"></i>
            </button>
            
            <div class="text-center">
                <p class="text-white-50 small">Already authenticated? <a href="login.php" class="text-primary fw-bold text-decoration-none ms-1">ACCESS PORTAL</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        
        if($('#password').val() !== $('#confirm_password').val()) {
            $('#registerAlert').removeClass('d-none alert-success').addClass('alert-danger bg-danger bg-opacity-20 text-danger').text('SECRET KEY MISMATCH!');
            return;
        }

        const btn = $('#registerBtn');
        const originalText = btn.html();
        btn.html('<i class="fas fa-dna fa-spin me-2"></i> ENLISTING...');
        btn.prop('disabled', true);
        
        const data = {
            name: $('#name').val(),
            roll_number: $('#roll_number').val(),
            email: $('#email').val(),
            department: $('#department').val(),
            hostel: $('#hostel').val(),
            password: $('#password').val()
        };
        
        $.ajax({
            url: 'api/register.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    $('#registerAlert').removeClass('d-none alert-danger').addClass('alert-success bg-success bg-opacity-20 text-success').html('<i class="fas fa-circle-check me-2"></i> ENLISTMENT COMPLETE. RE-ROUTING...');
                    setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                } else {
                    $('#registerAlert').removeClass('d-none alert-success').addClass('alert-danger bg-danger bg-opacity-20 text-danger').text(response.message);
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                $('#registerAlert').removeClass('d-none alert-success').addClass('alert-danger bg-danger bg-opacity-20 text-danger').text('DATA TRANSMISSION FAILURE.');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
</body>
</html>
