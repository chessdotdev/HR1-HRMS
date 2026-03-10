<?php
require_once '../modules/Recruitment.php';


// $Job_openings = [
//     [
//         "title" => "New Staff",
//         "department" => "Restaurant",
//         "description" => "desc",
//         "requirements" => [
//             "High school graduate",
//             "Restaurant experience is a plus",
//             "Good communication skills"
//         ]
//     ]
// ];
$recruitment = new Recruitment();
$stmt = $recruitment->getOpenJobs();


    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);




// var_dump($jobs);

?>
<?php include '../includes/header.php'; ?>

<div class="container px-0">
    <!-- Banner Section -->
    <div class="position-relative">
        <div class="hero-banner bg-dark text-white d-flex align-items-center justify-content-center text-center">
            <div class="container">
                <h1 class="display-3 fw-bold mb-3">Join Our Team</h1>
                <p class="lead fs-4 mb-4 opacity-90">Exciting career opportunities in hospitality<br>Hotel & Restaurant – Where Passion Meets Profession</p>
                <a href="#jobs" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                    Explore Open Positions
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container py-5" id="jobs">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-3 text-dark">Available Job Openings</h2>
                <p class="lead text-muted">Be part of our growing family. We value dedication, creativity, and excellent service.</p>
            </div>
        </div>

        <?php if (count($jobs) > 0): ?>
            <div class="row g-4">
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card job-card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold text-primary mb-3"><?php echo htmlspecialchars($job['title']); ?></h5>
                                
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2 me-2">
                                        <?php echo htmlspecialchars($job['department']); ?>
                                    </span>
                                </div>

                                <p class="card-text text-muted mb-4 flex-grow-1">
                                    <?php echo nl2br(htmlspecialchars(substr($job['role'], 0, 220))); ?>...
                                </p>

                                <div class="mt-auto">
                                    <h6 class="fw-semibold mb-2">Qualifications:</h6>
                                    <ul class="list-unstyled small text-muted mb-4">
                                        <?php 
                                $reqs = array_filter(array_map('trim', explode("\n", $job['qualifications'])));
                                foreach (array_slice($reqs, 0, 4) as $req) {
                                            if (trim($req)) echo '<li>• ' . htmlspecialchars(trim($req)) . '</li>';
                                        }
                                        ?>
                                    </ul>

                                    <a href="apply.php?job_id=<?=$job['id'];?>&title=<?=urlencode($job['title']);?>" 
                                       class="btn btn-outline-primary w-100 rounded-pill">
                                        Apply Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 my-5">
                <div class="display-1 text-muted opacity-25 mb-4">📭</div>
                <h3 class="text-muted">No openings right now</h3>
                <p class="lead text-muted">Please check back later or follow us for updates.</p>
            </div>
        <?php endif; ?>
        
    </div>
</div>


<?php
include '../includes/footer.php';
?>