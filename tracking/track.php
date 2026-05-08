<?php
require_once 'connect.php';

$trackingNumber = '';
$item = null;
$locations = [];
$error = '';

// Check if tracking number is submitted
if(isset($_POST['track']) || isset($_GET['track'])) {
    $trackingNumber = isset($_POST['tracking_number']) ? $_POST['tracking_number'] : $_GET['track'];
    $trackingNumber = mysqli_real_escape_string($con, $trackingNumber);
    
    // Get item details
    $query = "SELECT * FROM tracked_items WHERE tracking_number = '$trackingNumber'";
    $result = mysqli_query($con, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
        
        // Get location history
        $locQuery = "SELECT * FROM locations WHERE tracked_item_id = '{$item['id']}' ORDER BY recorded_at DESC";
        $locResult = mysqli_query($con, $locQuery);
        while($row = mysqli_fetch_assoc($locResult)) {
            $locations[] = $row;
        }
    } else {
        $error = "Tracking number not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Package - TrackMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e2b4f;
            --primary-light: #2a3a66;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 20px;
        }
        
        .track-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .track-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .track-header h1 {
            color: var(--primary);
            font-weight: 800;
            font-size: 2.5rem;
        }
        
        .track-header h1 span {
            color: var(--warning);
        }
        
        .search-box {
            background: #f8fafc;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_transit { background: #dbeafe; color: #1e40af; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-delayed { background: #fee2e2; color: #991b1b; }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e2e8f0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 30px;
        }
        
        .timeline-marker {
            position: absolute;
            left: 11px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--primary);
            z-index: 2;
        }
        
        .timeline-item.completed .timeline-marker {
            background: var(--success);
            border-color: var(--success);
        }
        
        .timeline-item.current .timeline-marker {
            background: var(--warning);
            border-color: var(--warning);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }
        
        .timeline-content {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 12px;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
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
        
        .step-icon {
            width: 45px;
            height: 45px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
        }
        
        .step.active .step-icon {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .step.completed .step-icon {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }
        
        .map-placeholder {
            background: #f1f5f9;
            height: 200px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="track-card">
            <div class="track-header">
                <a href="index.php" style="text-decoration: none;">
                    <h1>Track<span>Master</span></h1>
                </a>
                <p class="text-secondary">Enter your tracking number to see package status</p>
            </div>
            
            <!-- Search Form -->
            <div class="search-box">
                <form method="POST" action="">
                    <div class="input-group">
                        <input type="text" 
                               name="tracking_number" 
                               class="form-control form-control-lg" 
                               placeholder="Enter tracking number (e.g., TRK241212345)"
                               value="<?php echo htmlspecialchars($trackingNumber); ?>"
                               required>
                        <button type="submit" name="track" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i> Track
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($item): ?>
                <!-- Tracking Results -->
                <div class="tracking-result">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="text-secondary">Tracking #: <strong><?php echo $item['tracking_number']; ?></strong></p>
                        
                        <?php
                        $statusClass = '';
                        switch($item['status']) {
                            case 'pending': $statusClass = 'status-pending'; break;
                            case 'in_transit': $statusClass = 'status-in_transit'; break;
                            case 'delivered': $statusClass = 'status-delivered'; break;
                            case 'delayed': $statusClass = 'status-delayed'; break;
                        }
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <i class="fas fa-<?php 
                                echo $item['status'] == 'in_transit' ? 'truck' : 
                                    ($item['status'] == 'delivered' ? 'check-circle' : 
                                    ($item['status'] == 'pending' ? 'clock' : 'exclamation-circle')); 
                            ?> me-2"></i>
                            <?php echo strtoupper(str_replace('_', ' ', $item['status'])); ?>
                        </span>
                    </div>
                    
                    <!-- Progress Steps Visual -->
                    <div class="progress-steps">
                        <?php
                        $steps = ['pending', 'in_transit', 'delivered'];
                        $currentStatus = $item['status'];
                        $currentIndex = array_search($currentStatus, $steps);
                        if($currentIndex === false) $currentIndex = 0;
                        
                        foreach($steps as $index => $step):
                            $stepName = ucwords(str_replace('_', ' ', $step));
                            $isCompleted = $index < $currentIndex;
                            $isActive = $index == $currentIndex;
                        ?>
                        <div class="step <?php echo $isCompleted ? 'completed' : ($isActive ? 'active' : ''); ?>">
                            <div class="step-icon">
                                <?php if($isCompleted): ?>
                                    <i class="fas fa-check"></i>
                                <?php else: ?>
                                    <?php echo $index + 1; ?>
                                <?php endif; ?>
                            </div>
                            <small><?php echo $stepName; ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Route Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-secondary">FROM</small>
                                <h6><i class="fas fa-map-marker-alt me-2 text-danger"></i> <?php echo htmlspecialchars($item['origin']); ?></h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-secondary">TO</small>
                                <h6><i class="fas fa-flag-checkered me-2 text-success"></i> <?php echo htmlspecialchars($item['destination']); ?></h6>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map Placeholder (You can integrate Google Maps later) -->
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt fa-3x text-secondary mb-3"></i>
                        <p class="text-secondary mb-0">Map showing route from <?php echo $item['origin']; ?> to <?php echo $item['destination']; ?></p>
                        <small class="text-secondary">(Google Maps integration coming soon)</small>
                    </div>
                    
                    <!-- Timeline of Locations -->
                    <?php if(!empty($locations)): ?>
                        <h5 class="mb-3"><i class="fas fa-history me-2" style="color: var(--primary);"></i> Tracking History</h5>
                        <div class="timeline">
                            <?php foreach($locations as $index => $location): ?>
                                <?php
                                $isFirst = $index == 0;
                                $isLast = $index == count($locations) - 1;
                                $statusClass = $isFirst ? 'current' : ($isLast ? '' : 'completed');
                                ?>
                                <div class="timeline-item <?php echo $statusClass; ?>">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($location['address'] ?? 'Location Update'); ?></h6>
                                            <small class="text-secondary">
                                                <?php echo date('M d, H:i', strtotime($location['recorded_at'])); ?>
                                            </small>
                                        </div>
                                        <?php if(isset($location['status'])): ?>
                                            <p class="mb-0 small">
                                                <i class="fas fa-circle me-1" style="color: <?php 
                                                    echo $location['status'] == 'delivered' ? 'var(--success)' : 
                                                        ($location['status'] == 'in_transit' ? 'var(--warning)' : 'var(--primary)'); 
                                                ?>; font-size: 8px;"></i>
                                                Status: <?php echo ucwords(str_replace('_', ' ', $location['status'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- How it works (shown when no tracking) -->
            <?php if(!$item): ?>
                <div class="text-center py-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-box fa-3x mb-3" style="color: var(--primary);"></i>
                                <h6>1. Add Package</h6>
                                <p class="small text-secondary">Create tracking number</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-share-alt fa-3x mb-3" style="color: var(--primary);"></i>
                                <h6>2. Share Number</h6>
                                <p class="small text-secondary">Give to customer</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-search fa-3x mb-3" style="color: var(--primary);"></i>
                                <h6>3. Track Here</h6>
                                <p class="small text-secondary">Enter number above</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>