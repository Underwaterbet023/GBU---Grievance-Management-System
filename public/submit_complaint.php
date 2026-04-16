<?php require_once '../includes/header.php'; ?>

<div class="container-fluid py-3 min-vh-100">
    <div class="row mb-4 mb-md-5">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="bg-primary bg-opacity-10 p-3 p-md-4 rounded-4 shadow-primary-sm border border-primary border-opacity-20 flex-shrink-0">
                    <i class="fas fa-file-signature fa-lg fa-md-2x text-primary"></i>
                </div>
                <div>
                    <h1 class="h2 h1-md fw-bold text-white mb-1">Strategic Narrative Intake</h1>
                    <p class="text-secondary mb-0 small opacity-75 d-none d-sm-block">Record your grievance on the Immutable Network.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Form Terminal -->
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5 border-0 shadow-lg">
                <div id="submitAlert" class="alert d-none mb-4 animate-fade-in border-0 shadow-sm font-monospace x-small"></div>

                <form id="complaintForm" class="position-relative">
                    <div class="row g-3 g-md-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Operational Urgency</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white bg-opacity-5 text-primary px-3 rounded-start-4"><i class="fas fa-bolt"></i></span>
                                <select class="form-select border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-3 rounded-end-4 x-small" id="priority" required>
                                    <option value="Low">Alpha (Low Priority)</option>
                                    <option value="Medium" selected>Beta (Standard Priority)</option>
                                    <option value="High">Gamma (High Priority)</option>
                                    <option value="Urgent">Omega (Immediate Action)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Sector / Category</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white bg-opacity-5 text-primary px-3 rounded-start-4"><i class="fas fa-layer-group"></i></span>
                                <select class="form-select border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-3 rounded-end-4 x-small" id="category_hint">
                                    <option value="Unspecified">Auto-Detecting...</option>
                                    <option value="Academic">Academic Affairs</option>
                                    <option value="Hostel">Residential / Hostel</option>
                                    <option value="Finance">Financial Services</option>
                                    <option value="Infrastructure">IT & Utilities</option>
                                    <option value="Health">Medical / Wellbeing</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Academic Department</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white bg-opacity-5 text-primary px-3 rounded-start-4"><i class="fas fa-university"></i></span>
                                <select class="form-select border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-3 rounded-end-4 x-small" id="department" required>
                                    <option value="" selected disabled>Select Department...</option>
                                    <option value="SOE">School of Engineering</option>
                                    <option value="SOM">School of Management</option>
                                    <option value="SOB">School of Biotechnology</option>
                                    <option value="SOICT">School of ICT</option>
                                    <option value="SOLJ">School of Law & Justice</option>
                                    <option value="SOVS">School of Vocational Studies</option>
                                    <option value="SOHSS">School of Humanities & Social Sciences</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Residential Hostel</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white bg-opacity-5 text-primary px-3 rounded-start-4"><i class="fas fa-hotel"></i></span>
                                <select class="form-select border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-3 rounded-end-4 x-small" id="hostel" required>
                                    <option value="" selected disabled>Select Hostel...</option>
                                    <option value="Tansen">Tansen Hostel</option>
                                    <option value="Birsa Munda">Birsa Munda Hostel</option>
                                    <option value="Guru Ghasidas">Guru Ghasidas Hostel</option>
                                    <option value="Sant Ravidas">Sant Ravidas Hostel</option>
                                    <option value="Rani Laxmi Bai">Rani Laxmi Bai Hostel</option>
                                    <option value="Dayawati">Dayawati Hostel</option>
                                    <option value="N/A">Day Scholar / Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Narrative Identifier (Subject)</label>
                        <input type="text" class="form-control border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-4 rounded-4" id="subject" placeholder="Concise summary..." required>
                    </div>

                    <div class="mb-5 position-relative">
                        <label class="form-label x-small fw-bold text-primary text-uppercase tracking-widest mb-2 mb-md-3">Detailed Narrative</label>
                        <textarea class="form-control border-0 bg-white bg-opacity-5 text-white py-2 py-md-3 px-4 rounded-4" id="description" rows="6" placeholder="Provide factual details..." required></textarea>
                    </div>

                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3">
                        <button type="button" onclick="history.back()" class="btn btn-link text-secondary text-decoration-none fw-bold x-small tracking-widest order-2 order-sm-1">
                            <i class="fas fa-arrow-left me-2"></i> ABORT
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 py-3 fw-bold tracking-wider shadow-primary hover-lift d-flex align-items-center justify-content-center gap-3 w-100 w-sm-auto order-1 order-sm-2" id="submitBtn">
                            <span>INITIALIZE & SUBMIT</span>
                            <i class="fas fa-shield-alt"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Protocols Sidebar -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4 border-0">
                <h6 class="text-primary x-small fw-bold text-uppercase tracking-widest mb-4">Submission Protocols</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-white bg-opacity-5 rounded-4 border-start border-4 border-primary">
                        <span class="d-block x-small fw-bold text-white text-uppercase tracking-widest mb-1">Data Immutability</span>
                        <p class="text-secondary small mb-0">Once submitted, this record cannot be altered or deleted from the network logs.</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-5 rounded-4 border-start border-4 border-warning">
                        <span class="d-block x-small fw-bold text-white text-uppercase tracking-widest mb-1">Automatic Routing</span>
                        <p class="text-secondary small mb-0">Grievances are auto-routed based on department and priority detection.</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-5 rounded-4 border-start border-4 border-success">
                        <span class="d-block x-small fw-bold text-white text-uppercase tracking-widest mb-1">Privacy Shield</span>
                        <p class="text-secondary small mb-0">Your identity is masked using randomized hash identifiers for reviewers.</p>
                    </div>
                </div>
            </div>

            <!-- System Specs Card -->
            <div class="glass-card p-4 bg-primary bg-opacity-10 border-0 shadow-primary-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-primary x-small fw-bold text-uppercase tracking-widest">Network Pulse</span>
                    <i class="fas fa-bolt text-primary animate-pulse"></i>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between x-small">
                        <span class="text-secondary fw-bold text-uppercase tracking-widest">LATENCY</span>
                        <span class="text-success fw-bold font-monospace">24 MS</span>
                    </div>
                    <div class="d-flex justify-content-between x-small">
                        <span class="text-secondary fw-bold text-uppercase tracking-widest">NODE LOAD</span>
                        <span class="text-warning fw-bold text-uppercase">MODERATE</span>
                    </div>
                    <div class="d-flex justify-content-between x-small">
                        <span class="text-secondary fw-bold text-uppercase tracking-widest">ENCRYPTION</span>
                        <span class="text-info fw-bold font-monospace">AES-256</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles integrated into style.css -->



<script>
$(document).ready(function() {
    // Simulated AI Processing
    const aiAnalysis = $('#aiAnalysis');
    const aiStatus = $('#aiStatus');
    let aiTimeout;

    $('#description').on('input', function() {
        const text = $(this).val();
        if (text.length > 10) {
            aiAnalysis.removeClass('d-none');
            clearTimeout(aiTimeout);
            
            const words = text.split(' ').length;
            if (words % 5 === 0) {
                aiStatus.text('Analyzing Complexity...');
                aiTimeout = setTimeout(() => aiStatus.text('Detecting Strategic Impact...'), 1000);
            } else if (words % 7 === 0) {
                aiStatus.text('Optimizing Sentiment Data...');
            }
        } else {
            aiAnalysis.addClass('d-none');
        }
    });

    $('#complaintForm').submit(function(e) {
        e.preventDefault();
        const btn = $('#submitBtn');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Encrypting...').prop('disabled', true);
        
        const data = {
            priority: $('#priority').val(),
            category_hint: $('#category_hint').val(),
            department: $('#department').val(),
            hostel: $('#hostel').val(),
            subject: $('#subject').val(),
            description: $('#description').val()
        };
        
        $.ajax({
            url: 'api/submit_complaint.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    $('#submitAlert').removeClass('d-none alert-danger').addClass('alert-success').html('<b>Success!</b> Your grievance has been recorded and broadcasted to the network.');
                    setTimeout(() => window.location.href = 'student_dashboard.php', 1800);
                } else {
                    $('#submitAlert').removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                $('#submitAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Network timeout. Please try again.');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
