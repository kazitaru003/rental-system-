<?php
include 'db.php';

// Handle API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'fetch') {
    header('Content-Type: application/json');
    $result = $conn->query("SELECT h.*, v.vehicle_brand, v.vehicle_make FROM history h 
                           LEFT JOIN vehicles v ON h.licence_plate_number = v.licence_plate_number 
                           ORDER BY h.rental_start DESC");
    $rentals = [];
    while ($row = $result->fetch_assoc()) {
        $rentals[] = $row;
    }
    echo json_encode($rentals);
    exit;
}

if ($action === 'clear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if ($conn->query("DELETE FROM history")) {
        echo json_encode(['success' => true, 'message' => 'All logs cleared']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class ="navbar navbar-dark bg-dark">
     <div class="container">
      <a class="navbar-brand" href="index.php">RentEase</a>
       <div>
        <a href="vehicles.php" class="btn btn-outline-light btn-sm me-2 active">Vehicles</a>
         <a href="rent.php" class="btn btn-outline-light btn-sm me-2 active">Rent</a>
         <a href="logs.php" class="btn btn-outline-light btn-sm me-2 active">Logs</a>
       </div>
    </div>
    </nav>

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Rental & Activity Logs</h4>
            <button class="btn btn-primary" onclick="clearLogs()">
                Clear Logs
            </button>
            </div>
    <div class="card">
        <div class="card-body p4">
            <div class="card-body p4">
                <table class="table table-hover align-middle" id="logsTable">
                    <thead class="table-dark">
                        <tr>
                         <th>Date & Time</th>
                         <th>Vehicle</th>
                         <th>Plate Number</th>
                         <th>Renter</th>
                         <th>Action</th>
                         <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="logsbody">
                            <tr>
                            <td colspsan="6" class="text-center text-muted py-4">
                                loading logs....
                            </td>
                            </tr>
                        </tbody>
                </table>
                </div>
        </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>        function renderLogs() {
            fetch('logs.php?action=fetch')
                .then(response => response.json())
                .then(rentals => {
                    const tbody = document.getElementById('logsbody');
                    if (rentals.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No rental records found</td></tr>';
                        return;
                    }
                    tbody.innerHTML = rentals.map(r => `
                        <tr>
                            <td>${new Date(r.rental_start).toLocaleDateString()}</td>
                            <td>${r.vehicle_brand || '-'} ${r.vehicle_make || '-'}</td>
                            <td>${r.licence_plate_number}</td>
                            <td>${r.renter_name}</td>
                            <td>${r.days_rented} day(s)</td>
                            <td><span class="badge bg-${r.rental_status === 'Active' ? 'success' : 'secondary'}">${r.rental_status}</span></td>
                        </tr>
                    `).join('');
                })
                .catch(error => console.error('Error loading logs:', error));
        }

        function clearLogs() {
            if (!confirm('Are you sure you want to clear all logs? This cannot be undone.')) {
                return;
            }

            fetch('logs.php?action=clear', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('All logs cleared successfully!');
                    renderLogs();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error clearing logs:', error));
        }
        document.addEventListener('DOMContentLoaded', function() {
            renderLogs();
        });
    </script>
</body>
</html>