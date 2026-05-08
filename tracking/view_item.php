<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Check if item ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my_items.php");
    exit();
}

$itemId = mysqli_real_escape_string($con, $_GET['id']);

// Get item details (ensure it belongs to the logged-in user)
$query = "SELECT * FROM tracked_items WHERE id = '$itemId' AND user_id = '$userId'";
$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) == 0) {
    // Item not found or doesn't belong to user
    header("Location: my_items.php");
    exit();
}

$item = mysqli_fetch_assoc($result);

// Get location history for this item
$locQuery = "SELECT * FROM locations WHERE tracked_item_id = '$itemId' ORDER BY recorded_at DESC";
$locResult = mysqli_query($con, $locQuery);
$locations = [];
while($row = mysqli_fetch_assoc($locResult)) {
    $locations[] = $row;
}

// Format dates
$createdDate = date('F j, Y, g:i a', strtotime($item['created_at']));
$estimatedDate = $item['estimated_delivery'] ? date('F j, Y', strtotime($item['estimated_delivery'])) : 'Not set';
$actualDate = $item['actual_delivery'] ? date('F j, Y', strtotime($item['actual_delivery'])) : null;

// Calculate progress percentage for status
$statusProgress = [
    'pending' => 25,
    'in_transit' => 60,
    'delivered' => 100,
    'delayed' => 75
];
$progress = $statusProgress[$item['status']] ?? 25;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Item - TrackMaster</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e2b4f;
            --primary-light: #2a3a66;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --light-bg: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
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
        
        .sidebar-brand {
            padding: 0 25px;
            margin-bottom: 40px;
        }
        
        .sidebar-brand h3 {
            font-weight: 800;
        }
        
        .sidebar-brand span {
            color: var(--warning);
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left: 4px solid var(--warning);
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        /* Tracking Number Card */
        .tracking-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(30, 43, 79, 0.2);
        }
        
        .tracking-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .tracking-number {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }
        
        .status-badge-large {
            display: inline-block;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid #e2e8f0;
        }
        
        .info-title {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .info-value {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-sub {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        /* Progress Bar */
        .progress-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0 20px;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }
        
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        
        .step-circle {
            width: 35px;
            height: 35px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
            background: white;
        }
        
        .step.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .step.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }
        
        .step.delayed .step-circle {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Timeline */
        .timeline {
            margin-top: 20px;
        }
        
        .timeline-item {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .timeline-item:last-child {
            border-bottom: none;
        }
        
        .timeline-icon {
            width: 45px;
            height: 45px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .timeline-content {
            flex: 1;
        }
        
        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .timeline-time {
            color: #64748b;
            font-size: 0.85rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-action {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 500;
            flex: 1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3>Track<span>Master</span></h3>
        </div>
        
        <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="my_items.php" class="nav-link active">
                <i class="fas fa-box"></i> My Items
            </a>
            <a href="create_item.php" class="nav-link">
                <i class="fas fa-plus-circle"></i> Create New
            </a>
            <a href="profile.php" class="nav-link">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="logout.php" class="nav-link" style="margin-top: 50px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-box me-2" style="color: var(--primary);"></i>
                Item Details
            </h2>
            <div>
                <a href="my_items.php" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Items
                </a>
                <span>
                    <i class="fas fa-user-circle me-1"></i>
                    <?php echo $_SESSION['user_name']; ?>
                </span>
            </div>
        </div>
        
        <!-- Tracking Number Card -->
        <div class="tracking-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="tracking-label">TRACKING NUMBER</div>
                    <div class="tracking-number"><?php echo htmlspecialchars($item['tracking_number']); ?></div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="status-badge-large">
                            <i class="fas fa-<?php 
                                echo $item['status'] == 'in_transit' ? 'truck' : 
                                    ($item['status'] == 'delivered' ? 'check-circle' : 
                                    ($item['status'] == 'pending' ? 'clock' : 'exclamation-circle')); 
                            ?> me-2"></i>
                            <?php echo ucwords(str_replace('_', ' ', $item['status'])); ?>
                        </span>
                        <span><i class="far fa-calendar me-1"></i> Created: <?php echo $createdDate; ?></span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Item Details Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-title">ITEM INFORMATION</div>
                    <div class="info-value"><?php echo htmlspecialchars($item['name']); ?></div>
                    <?php if(!empty($item['description'])): ?>
                        <div class="info-sub"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-title">ROUTE</div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="info-sub">FROM</div>
                            <div class="fw-semibold"><?php echo htmlspecialchars($item['origin']); ?></div>
                        </div>
                        <div class="text-end">
                            <div class="info-sub">TO</div>
                            <div class="fw-semibold"><?php echo htmlspecialchars($item['destination']); ?></div>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-title">ESTIMATED DELIVERY</div>
                    <div class="info-value"><?php echo $estimatedDate; ?></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-title">ACTUAL DELIVERY</div>
                    <div class="info-value"><?php echo $actualDate ?? 'Not delivered yet'; ?></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-title">LAST UPDATED</div>
                    <div class="info-value"><?php echo date('M j, Y', strtotime($item['updated_at'] ?? $item['created_at'])); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Progress Steps -->
        <div class="progress-container">
            <h5 class="fw-bold mb-4">Tracking Progress</h5>
            <div class="progress-steps">
                <?php
                $steps = ['pending', 'in_transit', 'delivered'];
                $currentStatus = $item['status'];
                $currentIndex = array_search($currentStatus, $steps);
                if($currentIndex === false) $currentIndex = 0;
                
                foreach($steps as $index => $step):
                    $isCompleted = $index < $currentIndex;
                    $isActive = $index == $currentIndex;
                    $isDelayed = ($step == 'in_transit' && $item['status'] == 'delayed');
                ?>
                <div class="step 
                    <?php echo $isCompleted ? 'completed' : ''; ?>
                    <?php echo $isActive ? 'active' : ''; ?>
                    <?php echo $isDelayed ? 'delayed' : ''; ?>
                ">
                    <div class="step-circle">
                        <?php if($isCompleted): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <?php echo $index + 1; ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-label"><?php echo ucwords(str_replace('_', ' ', $step)); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Location Timeline -->
        <div class="progress-container">
            <h5 class="fw-bold mb-4">Tracking History</h5>
            
            <?php if(empty($locations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-secondary mb-3"></i>
                    <p class="text-secondary">No location updates yet</p>
                </div>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach($locations as $location): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title"><?php echo htmlspecialchars($location['location_name'] ?? 'Location Update'); ?></div>
                                <?php if(!empty($location['description'])): ?>
                                    <p class="mb-1"><?php echo htmlspecialchars($location['description']); ?></p>
                                <?php endif; ?>
                                <div class="timeline-time">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo date('F j, Y, g:i a', strtotime($location['recorded_at'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="track_my_item.php?track=<?php echo $item['tracking_number']; ?>" class="btn btn-primary btn-action">
                <i class="fas fa-map-marker-alt me-2"></i> Track on Map
            </a>
            <?php if($item['status'] == 'pending'): ?>
                <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-edit me-2"></i> Edit Item
                </a>
            <?php endif; ?>
            <a href="my_items.php" class="btn btn-outline-primary btn-action">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>