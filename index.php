<?php
include 'db.php';

// Handle API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'stats') {
    header('Content-Type: application/json');
    
    $totalResult = $conn->query("SELECT COUNT(*) as count FROM vehicles");
    $total = $totalResult->fetch_assoc()['count'];
    
    $availableResult = $conn->query("SELECT COUNT(*) as count FROM vehicles WHERE vehicle_status = 'Available'");
    $available = $availableResult->fetch_assoc()['count'];
    
    $rentedResult = $conn->query("SELECT COUNT(*) as count FROM vehicles WHERE vehicle_status = 'Rented'");
    $rented = $rentedResult->fetch_assoc()['count'];
    
    $maintenanceResult = $conn->query("SELECT COUNT(*) as count FROM vehicles WHERE vehicle_status = 'Maintenance'");
    $maintenance = $maintenanceResult->fetch_assoc()['count'];
    
    echo json_encode([
        'total' => $total,
        'available' => $available,
        'rented' => $rented,
        'maintenance' => $maintenance
    ]);
    exit;
}

if ($action === 'fleet') {
    header('Content-Type: application/json');
    $result = $conn->query("SELECT * FROM vehicles");
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor/CarRental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body>
    
    <nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">RentEase</a>
    <div>
     <a href="vehicles.php" class="btn btn-outline-light btn-sm me-2">Vehicles</a>
     <a href="rent.php" class="btn btn-outline-light btn-sm me-2">Rent</a>
     <a href="logs.php" class="btn btn-outline-light btn-sm me-2">Logs</a>
     </div>
    </div>
    </nav>

   <div class="container mt-4">
    <h4 class="mb-4">Dashboard</h4>

    <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Total Vehicles</div>
                <div class="fs-3 fw-bold" id="totalCount">0</div>
    </div>
   </div>
      <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Available</div>
                <div class="fs-3 text-success" id="availCount">0</div>
    </div>
   </div>
     <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Rented</div>
                <div class="fs-3 text-warning" id="rentcount">0</div>
    </div>
   </div>
       <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="text-muted small">Maintenance</div>
                <div class="fs-3 text-danger" id="MainCount">0</div>
    </div>
   </div>
  </div>

  <h5>Fleet overview</h5>
  <div class="row g-3" id="fleetContainer">
    <div class="text-muted">Loading...</div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function renderDashboard() {
        loadStats();
        loadFleet();
    }

    function loadStats() {
        fetch('index.php?action=stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalCount').textContent = data.total;
                document.getElementById('availCount').textContent = data.available;
                document.getElementById('rentcount').textContent = data.rented;
                document.getElementById('MainCount').textContent = data.maintenance;
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    function loadFleet() {
        fetch('index.php?action=fleet')
            .then(response => response.json())
            .then(vehicles => {
                const container = document.getElementById('fleetContainer');
                if (vehicles.length === 0) {
                    container.innerHTML = '<div class="text-muted">No vehicles in fleet</div>';
                    return;
                }
                container.innerHTML = vehicles.map(v => `
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${v.vehicle_brand} ${v.vehicle_make}</h5>
                                <p class="card-text">
                                    <strong>Plate:</strong> ${v.licence_plate_number}<br>
                                    <strong>Type:</strong> ${v.vehicle_type}<br>
                                    <strong>Year:</strong> ${v.vehicle_year}<br>
                                    <strong>Daily Rate:</strong> $${v.daily_rate}<br>
                                    <strong>Status:</strong> <span class="badge bg-${v.vehicle_status === 'Available' ? 'success' : v.vehicle_status === 'Rented' ? 'warning' : 'danger'}">${v.vehicle_status}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => console.error('Error loading fleet:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderDashboard();
    });
  </script>
</body>
</html>