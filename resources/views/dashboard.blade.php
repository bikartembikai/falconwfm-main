<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon WMS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Custom Styles to make it look like a pro system */
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50; /* Dark Blue Theme */
            color: white;
        }
        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: white;
            border-left: 4px solid #3498db; /* Active Highlight */
        }
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card-custom:hover { transform: translateY(-5px); }
        .icon-box {
            font-size: 2rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <nav class="col-md-2 d-none d-md-block sidebar p-0">
            <div class="text-center p-4">
                <h4><i class="fa-solid fa-feather"></i> FALCON WMS</h4>
                <small class="text-muted">Operation Manager</small>
            </div>
            <hr class="mx-3 text-secondary">
            <ul class="list-unstyled">
                <li><a href="#" class="active"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a></li>
                <li><a href="#"><i class="fa-solid fa-calendar-days me-2"></i> Events</a></li>
                <li><a href="#"><i class="fa-solid fa-users me-2"></i> Facilitators</a></li>
                <li><a href="#"><i class="fa-solid fa-wand-magic-sparkles me-2"></i> Smart Assign</a></li> 
                <li><a href="#"><i class="fa-solid fa-money-bill-wave me-2"></i> Payroll</a></li>
                <li><a href="#" class="mt-5 text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-4 border-bottom">
                <h1 class="h2">Dashboard Overview</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus"></i> Create New Event
                    </button>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-custom bg-primary text-white h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Upcoming Events</h6>
                                <h2 class="display-6 fw-bold">12</h2>
                            </div>
                            <div class="icon-box"><i class="fa-solid fa-calendar-check"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-custom bg-success text-white h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Facilitators</h6>
                                <h2 class="display-6 fw-bold">185</h2>
                            </div>
                            <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-custom bg-warning text-dark h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Pending Claims</h6>
                                <h2 class="display-6 fw-bold">5</h2>
                            </div>
                            <div class="icon-box"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Recently Created Events</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Venue</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Team Building: Petronas</td>
                                    <td>25 Dec 2025</td>
                                    <td>Janda Baik, Pahang</td>
                                    <td><span class="badge bg-success">Open</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Assign Staff</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Leadership Camp: UiTM</td>
                                    <td>10 Jan 2026</td>
                                    <td>A'Famosa Resort</td>
                                    <td><span class="badge bg-warning text-dark">Planning</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
