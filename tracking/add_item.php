<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

// Function to generate unique tracking number
function generateTrackingNumber($con) {
    $prefix = 'TRK';
    $year = date('y');
    $month = date('m');
    
    do {
        // Format: TRK-24-12-XXXXX (TRK-YY-MM-12345)
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $trackingNumber = $prefix . $year . $month . $random;
        
        // Check if unique
        $check = mysqli_query($con, "SELECT id FROM tracked_items WHERE tracking_number = '$trackingNumber'");
    } while(mysqli_num_rows($check) > 0);
    
    return $trackingNumber;
}

// Process form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemName = mysqli_real_escape_string($con, $_POST['item_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $origin = mysqli_real_escape_string($con, $_POST['origin']);
    $destination = mysqli_real_escape_string($con, $_POST['destination']);
    $estimatedDelivery = $_POST['estimated_delivery'];
    
    // Generate tracking number
    $trackingNumber = generateTrackingNumber($con);
    
    // Insert into database
    $query = "INSERT INTO tracked_items (tracking_number, name, description, origin, destination, estimated_delivery, status, user_id, created_at) 
              VALUES ('$trackingNumber', '$itemName', '$description', '$origin', '$destination', '$estimatedDelivery', 'pending', '$userId', NOW())";
    
    if(mysqli_query($con, $query)) {
        $itemId = mysqli_insert_id($con);
        
        // Add initial location
        mysqli_query($con, "INSERT INTO locations (tracked_item_id, address, status, recorded_at) 
                           VALUES ('$itemId', '$origin', 'pending', NOW())");
        
        $success = "Item added successfully! Tracking Number: <strong>$trackingNumber</strong>";
    } else {
        $error = "Error adding item: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - TrackMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e2b4f;
            --primary-light: #2a3a66;
            --primary-dark: #121f3a;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --text-secondary: #64748b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--primary);
            color: white;
            padding: 30px 0;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .tracking-number-preview {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .tracking-number-preview h1 {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: none;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 15px;
            font-weight: 600;
            border-radius: 12px;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content same as dashboard -->
        <div class="px-4 mb-5">
            <h3>Track<span style="color: var(--warning);">Master</span></h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link text-white px-4 py-3">
                    <i class="fas fa-chart-pie me-3"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="add_item.php" class="nav-link text-white px-4 py-3" style="background: rgba(255,255,255,0.1);">
                    <i class="fas fa-plus-circle me-3"></i> Add Item
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="track.php" class="nav-link text-white px-4 py-3">
                    <i class="fas fa-search me-3"></i> Track Package
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-plus-circle me-2" style="color: var(--primary);"></i> Add New Package</h2>
            <div>
                <span class="me-3"><i class="fas fa-user me-2"></i><?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card p-4">
                    <h4 class="mb-4">Package Information</h4>
                    
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Item Name</label>
                                <input type="text" name="item_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estimated Delivery</label>
                                <input type="date" name="estimated_delivery" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Origin</label>
                                <input type="text" name="origin" class="form-control" placeholder="City, State" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Destination</label>
                                <input type="text" name="destination" class="form-control" placeholder="City, State" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-save me-2"></i> Generate Tracking Number & Save
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="tracking-number-preview">
                    <i class="fas fa-barcode fa-3x mb-3" style="opacity: 0.8;"></i>
                    <h5>Your tracking number will appear here</h5>
                    <p class="small opacity-75">Format: TRK-YY-MM-XXXXX</p>
                </div>
                
                <div class="card p-4">
                    <h5><i class="fas fa-info-circle me-2" style="color: var(--primary);"></i> How it works</h5>
                    <ol class="mt-3" style="color: var(--text-secondary);">
                        <li class="mb-2">Fill package details</li>
                        <li class="mb-2">System generates unique tracking number</li>
                        <li class="mb-2">Share number with customer</li>
                        <li class="mb-2">Customer tracks journey online</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>