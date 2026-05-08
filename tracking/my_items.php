<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get all items for this user
$query = "SELECT * FROM tracked_items WHERE user_id = '$userId' ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$items = [];
while($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

// Count by status
$total = count($items);
$inTransit = 0;
$delivered = 0;
$pending = 0;
$delayed = 0;

foreach($items as $item) {
    switch($item['status']) {
        case 'in_transit': $inTransit++; break;
        case 'delivered': $delivered++; break;
        case 'pending': $pending++; break;
        case 'delayed': $delayed++; break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Items - TrackMaster</title>
    
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
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_transit { background: #dbeafe; color: #1e40af; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-delayed { background: #fee2e2; color: #991b1b; }
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 8px;
            margin: 0 3px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f9f9f9;
            border-radius: 15px;
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
                My Items
            </h2>
            <div>
                <span class="me-3">
                    <i class="fas fa-user-circle me-1"></i>
                    <?php echo $_SESSION['user_name']; ?>
                </span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card d-flex align-items-center">
                    <div class="stats-icon bg-primary bg-opacity-10 me-3">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                    <div>
                        <small class="text-secondary">Total Items</small>
                        <h4 class="mb-0 fw-bold"><?php echo $total; ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card d-flex align-items-center">
                    <div class="stats-icon bg-warning bg-opacity-10 me-3">
                        <i class="fas fa-truck text-warning"></i>
                    </div>
                    <div>
                        <small class="text-secondary">In Transit</small>
                        <h4 class="mb-0 fw-bold"><?php echo $inTransit; ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card d-flex align-items-center">
                    <div class="stats-icon bg-success bg-opacity-10 me-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <small class="text-secondary">Delivered</small>
                        <h4 class="mb-0 fw-bold"><?php echo $delivered; ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card d-flex align-items-center">
                    <div class="stats-icon bg-danger bg-opacity-10 me-3">
                        <i class="fas fa-clock text-danger"></i>
                    </div>
                    <div>
                        <small class="text-secondary">Pending/Delayed</small>
                        <h4 class="mb-0 fw-bold"><?php echo $pending + $delayed; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">All Your Items</h5>
                <a href="create_item.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Item
                </a>
            </div>
            
            <?php if(empty($items)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open fa-4x text-secondary mb-3"></i>
                    <h5>No items yet</h5>
                    <p class="text-secondary mb-3">Create your first tracking item to get started</p>
                    <a href="create_item.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create New Item
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tracking #</th>
                                <th>Item Name</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-primary">
                                            <?php echo htmlspecialchars($item['tracking_number']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['origin']); ?></td>
                                    <td><?php echo htmlspecialchars($item['destination']); ?></td>
                                    <td>
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
                                            ?> me-1"></i>
                                            <?php echo ucwords(str_replace('_', ' ', $item['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <a href="view_item.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary btn-action" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="track_my_item.php?track=<?php echo $item['tracking_number']; ?>" 
                                           class="btn btn-sm btn-outline-success btn-action" 
                                           title="Track">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                        <?php if($item['status'] == 'pending'): ?>
                                            <a href="edit_item.php?id=<?php echo $item['id']; ?>" 
                                               class="btn btn-sm btn-outline-secondary btn-action" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>