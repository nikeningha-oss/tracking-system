<?php
session_start();
require_once 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Get statistics
$totalParcels = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tracked_items"))['count'];
$inTransit = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tracked_items WHERE status = 'in_transit'"))['count'];
$delivered = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tracked_items WHERE status = 'delivered'"))['count'];
$pending = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tracked_items WHERE status = 'pending'"))['count'];
$totalCustomers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM users WHERE role = 'customer'"))['count'];

// Get recent parcels
$recentQuery = "SELECT t.*, u.name as customer_name 
                FROM tracked_items t 
                LEFT JOIN users u ON t.customer_id = u.id 
                ORDER BY t.created_at DESC 
                LIMIT 10";
$recentResult = mysqli_query($con, $recentQuery);
$recentParcels = [];
while($row = mysqli_fetch_assoc($recentResult)) {
    $recentParcels[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TrackMaster</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* COLOR THEME - Consistent throughout project */
        :root {
            --primary: #1e2b4f;
            --primary-light: #2a3a66;
            --primary-dark: #121f3a;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #6366f1;
            --dark: #0f172a;
            --light: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-light: #e2e8f0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--text-primary);
        }
        
        /* Fixed Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--primary);
            color: white;
            padding: 30px 0;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 0 25px;
            margin-bottom: 40px;
        }
        
        .sidebar-brand h3 {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .sidebar-brand h3 span {
            color: var(--warning);
        }
        
        .sidebar-brand p {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            margin: 5px 0;
            border-left: 4px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: var(--warning);
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .page-title i {
            margin-right: 10px;
            color: var(--warning);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-badge {
            background: var(--light);
            padding: 8px 15px;
            border-radius: 30px;
            font-weight: 500;
        }
        
        .user-badge i {
            color: var(--primary);
            margin-right: 8px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--border-light);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(30, 43, 79, 0.1);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        
        .stat-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .stat-info p {
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
        }
        
        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid var(--border-light);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h5 {
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        
        .section-header h5 i {
            margin-right: 10px;
            color: var(--warning);
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            padding: 15px 12px;
            background: var(--light);
        }
        
        .table tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-light);
        }
        
        .tracking-number {
            font-weight: 600;
            color: var(--primary);
        }
        
        .customer-badge {
            background: var(--light);
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 0.85rem;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-in_transit {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-delivered {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-delayed {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-outline-primary {
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
        }
        
        /* Quick Actions Grid */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .action-card {
            background: var(--light);
            border: 1px solid var(--border-light);
            border-radius: 15px;
            padding: 25px 20px;
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s;
            display: block;
        }
        
        .action-card:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(30, 43, 79, 0.2);
        }
        
        .action-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .action-card:hover i {
            color: white;
        }
        
        .action-card h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .action-card p {
            font-size: 0.85rem;
            margin: 0;
            opacity: 0.7;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3>Track<span>Master</span></h3>
            <p>Admin Portal</p>
        </div>
        
        <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="create_item.php" class="nav-link">
                <i class="fas fa-plus-circle"></i>
                <span>Create Parcel</span>
            </a>
            <a href="all_items.php" class="nav-link">
                <i class="fas fa-box"></i>
                <span>All Parcels</span>
            </a>
            <a href="update_location.php" class="nav-link">
                <i class="fas fa-map-marker-alt"></i>
                <span>Update Location</span>
            </a>
            <a href="manage_customers.php" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
            <a href="../logout.php" class="nav-link" style="margin-top: 50px;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <i class="fas fa-chart-pie"></i>
                Dashboard Overview
            </div>
            <div class="user-info">
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['user_name']; ?>
                </div>
                <span class="badge bg-warning text-dark px-3 py-2">Admin</span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Total Parcels -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(30, 43, 79, 0.1); color: var(--primary);">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalParcels; ?></h3>
                    <p>Total Parcels</p>
                </div>
            </div>
            
            <!-- In Transit -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $inTransit; ?></h3>
                    <p>In Transit</p>
                </div>
            </div>
            
            <!-- Delivered -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $delivered; ?></h3>
                    <p>Delivered</p>
                </div>
            </div>
            
            <!-- Pending -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(100, 116, 139, 0.1); color: var(--text-secondary);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            
            <!-- Customers -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--info);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalCustomers; ?></h3>
                    <p>Customers</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Parcels Table -->
        <div class="section-card">
            <div class="section-header">
                <h5>
                    <i class="fas fa-history"></i>
                    Recent Parcels
                </h5>
                <a href="all_items.php" class="btn btn-sm btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Item</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recentParcels)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>
                                    <p class="text-secondary">No parcels yet</p>
                                    <a href="create_item.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Create First Parcel
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($recentParcels as $parcel): ?>
                                <tr>
                                    <td>
                                        <span class="tracking-number">
                                            <?php echo htmlspecialchars($parcel['tracking_number']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($parcel['item_name']); ?></td>
                                    <td>
                                        <?php if($parcel['customer_name']): ?>
                                            <span class="customer-badge">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($parcel['customer_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-secondary">
                                                <i class="fas fa-user-slash me-1"></i>
                                                Not linked
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            <?php echo htmlspecialchars($parcel['origin']); ?>
                                            <i class="fas fa-arrow-right mx-1"></i>
                                            <i class="fas fa-flag-checkered text-success me-1"></i>
                                            <?php echo htmlspecialchars($parcel['destination']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch($parcel['status']) {
                                            case 'pending': $statusClass = 'status-pending'; break;
                                            case 'in_transit': $statusClass = 'status-in_transit'; break;
                                            case 'delivered': $statusClass = 'status-delivered'; break;
                                            case 'delayed': $statusClass = 'status-delayed'; break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <i class="fas fa-<?php 
                                                echo $parcel['status'] == 'in_transit' ? 'truck' : 
                                                    ($parcel['status'] == 'delivered' ? 'check-circle' : 
                                                    ($parcel['status'] == 'pending' ? 'clock' : 'exclamation-circle')); 
                                            ?> me-1"></i>
                                            <?php echo ucwords(str_replace('_', ' ', $parcel['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary">
                                            <i class="far fa-calendar me-1"></i>
                                            <?php echo date('M d, Y', strtotime($parcel['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="update_location.php?track=<?php echo $parcel['tracking_number']; ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Update Location">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-card">
            <div class="section-header">
                <h5>
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h5>
            </div>
            
            <div class="actions-grid">
                <a href="create_item.php" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <h6>Create Parcel</h6>
                    <p>Register new package</p>
                </a>
                
                <a href="update_location.php" class="action-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h6>Update Location</h6>
                    <p>Track package movement</p>
                </a>
                
                <a href="all_items.php" class="action-card">
                    <i class="fas fa-box"></i>
                    <h6>View All Parcels</h6>
                    <p>See all shipments</p>
                </a>
                
                <a href="manage_customers.php" class="action-card">
                    <i class="fas fa-users"></i>
                    <h6>Manage Customers</h6>
                    <p>View customer list</p>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>