<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HR Self-Service Portal</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <style>
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --accent: #10b981;
      --light-bg: #f8fafc;
    }

    body {
      background-color: var(--light-bg);
      font-family: system-ui, -apple-system, sans-serif;
    }

    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
      color: white;
    }

    .nav-link {
      color: rgba(255,255,255,0.85) !important;
      border-radius: 8px;
      margin: 4px 12px;
    }

    .nav-link:hover,
    .nav-link.active {
      background-color: rgba(255,255,255,0.15);
      color: white !important;
    }

    .card-stat {
      border-left: 5px solid var(--primary);
      transition: transform 0.12s;
    }

    .card-stat:hover {
      transform: translateY(-3px);
    }

    .recognition-item {
      border-left: 4px solid var(--accent);
      background-color: white;
    }

    .badge-soft {
      background-color: rgba(59,130,246,0.1);
      color: var(--primary-dark);
    }

    .onboarding-progress .progress {
      height: 10px;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="d-flex">

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 260px;">
    <a href="/" class="d-flex align-items-center mb-4 text-white text-decoration-none">
      <span class="fs-4 fw-bold">HR Self-Service</span>
    </a>
    <hr class="text-white opacity-25">
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="#" class="nav-link active" aria-current="page">
          <i class="bi bi-house-door me-2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
          <i class="bi bi-briefcase me-2"></i> My Applications
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
          <i class="bi bi-rocket-takeoff me-2"></i> Onboarding
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
          <i class="bi bi-bar-chart me-2"></i> Goals & Performance
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
          <i class="bi bi-hand-thumbs-up me-2"></i> Recognition
        </a>
      </li>
    </ul>
    <hr class="text-white opacity-25">
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
        <img src="https://ui-avatars.com/api/?name=Alex+Rivera&background=0d8abc&color=fff&size=128" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Alex Rivera</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Sign out</a></li>
      </ul>
    </div>
  </div>

  <!-- Main Content -->
  <div class="flex-grow-1">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container-fluid px-4">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger rounded-pill">3</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">New recognition received</a></li>
                <li><a class="dropdown-item" href="#">Onboarding task due tomorrow</a></li>
                <li><a class="dropdown-item" href="#">Goal review requested</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="p-4 p-md-5">

      <h2 class="mb-4 fw-bold">Welcome back, Alex</h2>

      <div class="row g-4">

        <!-- Onboarding Card -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title d-flex justify-content-between align-items-center">
                New Hire Onboarding
                <span class="badge bg-primary">In Progress</span>
              </h5>
              <div class="onboarding-progress mt-3 mb-4">
                <div class="progress">
                  <div class="progress-bar bg-success" role="progressbar" style="width: 68%" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 small text-muted">
                  <span>68% complete</span>
                  <span>Target: Feb 28, 2026</span>
                </div>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Sign employment contract
                  <span class="badge bg-warning rounded-pill">Pending</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Upload ID documents
                  <i class="bi bi-check-circle-fill text-success"></i>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Complete compliance training
                  <span class="badge bg-danger rounded-pill">Due soon</span>
                </li>
              </ul>
              <a href="#" class="btn btn-outline-primary btn-sm mt-3">Continue Onboarding</a>
            </div>
          </div>
        </div>

        <!-- Recognition Card -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title d-flex justify-content-between align-items-center">
                Social Recognition
                <a href="#" class="btn btn-sm btn-outline-success">Give Kudos +</a>
              </h5>
              <div class="mt-3">
                <div class="recognition-item p-3 mb-3 rounded">
                  <div class="d-flex">
                    <img src="https://ui-avatars.com/api/?name=Sarah+M&background=10b981&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                    <div>
                      <strong>Sarah Miller</strong>
                      <small class="text-muted d-block">2 days ago</small>
                      <p class="mb-1 mt-1">Thank you for the excellent support during the launch ❤️ Great job!</p>
                      <span class="badge bg-success">+50 points</span>
                    </div>
                  </div>
                </div>

                <div class="recognition-item p-3 rounded">
                  <div class="d-flex">
                    <img src="https://ui-avatars.com/api/?name=You&background=3b82f6&color=fff" class="rounded-circle me-3" width="48" height="48" alt="">
                    <div>
                      <strong>You gave kudos to</strong> <strong>James K.</strong>
                      <small class="text-muted d-block">Jan 29, 2026</small>
                      <p class="mb-1 mt-1">Amazing analysis on Q4 forecast!</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-12">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="card card-stat border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="text-muted mb-1">Active Goals</h6>
                  <h3 class="fw-bold">4</h3>
                  <small class="text-success">2 due this quarter</small>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-stat border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="text-muted mb-1">Recognition Received</h6>
                  <h3 class="fw-bold">7</h3>
                  <small class="text-success">This year</small>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-stat border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="text-muted mb-1">Pending Reviews</h6>
                  <h3 class="fw-bold text-warning">1</h3>
                  <small>Self-assessment due Feb 20</small>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </main>
  </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>