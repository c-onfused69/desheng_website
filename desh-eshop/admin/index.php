<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'Admin Dashboard';
$current_admin = getCurrentAdmin();

// Get dashboard statistics
try {
    $db = getDB();
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $total_users = $stmt->fetch()['count'];
    
    // Total products
    $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $total_products = $stmt->fetch()['count'];
    
    // Total orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
    $total_orders = $stmt->fetch()['count'];
    
    // Total revenue
    $stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE payment_status = 'paid'");
    $total_revenue = $stmt->fetch()['revenue'];
    
    // Monthly revenue (last 12 months)
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders 
        WHERE payment_status = 'paid' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthly_revenue = $stmt->fetchAll();
    
    // Recent orders
    $stmt = $db->query("
        SELECT o.*, u.name as user_name, COUNT(oi.id) as item_count
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $recent_orders = $stmt->fetchAll();
    
    // Top selling products
    $stmt = $db->query("
        SELECT p.title, p.slug, SUM(oi.quantity) as total_sold, SUM(oi.total_price) as total_revenue
        FROM products p
        JOIN order_items oi ON p.id = oi.product_id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.payment_status = 'paid'
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $top_products = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $total_users = $total_products = $total_orders = $total_revenue = 0;
    $monthly_revenue = $recent_orders = $top_products = [];
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar3 me-1"></i>
                    This week
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This week</a></li>
                    <li><a class="dropdown-item" href="#">This month</a></li>
                    <li><a class="dropdown-item" href="#">This year</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-primary">
                <i class="bi bi-download me-1"></i>
                Export
            </button>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo formatCurrency($total_revenue); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($total_orders); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($total_products); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($total_users); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-primary"></i> Direct Sales
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-success"></i> Referral
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-info"></i> Social
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tables Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-primary btn-sm">
                        View All Orders
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo $order['item_count']; ?></td>
                                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getOrderStatusColor($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/orders.php?view=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-primary btn-sm">
                        View All Products
                    </a>
                </div>
                <div class="card-body">
                    <?php foreach ($top_products as $product): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="fw-bold"><?php echo htmlspecialchars($product['title']); ?></div>
                            <small class="text-muted">
                                <?php echo $product['total_sold']; ?> sold • <?php echo formatCurrency($product['total_revenue']); ?>
                            </small>
                        </div>
                        <div class="progress" style="width: 100px; height: 20px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: <?php echo min(100, ($product['total_sold'] / max(1, $top_products[0]['total_sold'])) * 100); ?>%">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            $months = [];
            $revenues = [];
            foreach ($monthly_revenue as $data) {
                $months[] = "'" . date('M Y', strtotime($data['month'] . '-01')) . "'";
                $revenues[] = $data['revenue'];
            }
            echo implode(', ', $months);
            ?>
        ],
        datasets: [{
            label: 'Revenue',
            data: [<?php echo implode(', ', $revenues); ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Monthly Revenue'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Pie Chart
const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Direct Sales', 'Referral', 'Social'],
        datasets: [{
            data: [55, 30, 15],
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(75, 192, 192)',
                'rgb(255, 205, 86)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-400 {
    color: #858796 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.fa-2x {
    font-size: 2em;
}

.chart-area {
    position: relative;
    height: 320px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 245px;
    width: 100%;
}
</style>

<?php
// Helper function for order status colors
function getOrderStatusColor($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'processing':
            return 'primary';
        case 'pending':
            return 'warning';
        case 'cancelled':
        case 'refunded':
            return 'danger';
        default:
            return 'secondary';
    }
}

include 'includes/footer.php';
?>
