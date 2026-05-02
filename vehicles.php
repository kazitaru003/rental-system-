<?php
include 'db.php';

// Handle API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'fetch') {
    header('Content-Type: application/json');
    $result = $conn->query("SELECT * FROM vehicles");
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
    exit;
}

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $plate = $conn->real_escape_string($_POST['licence_plate_number'] ?? '');
    $daily_rate = intval($_POST['daily_rate'] ?? 0);
    $vehicle_make = $conn->real_escape_string($_POST['vehicle_make'] ?? '');
    $vehicle_brand = $conn->real_escape_string($_POST['vehicle_brand'] ?? '');
    $vehicle_type = $conn->real_escape_string($_POST['vehicle_type'] ?? '');
    $vehicle_year = intval($_POST['vehicle_year'] ?? 0);
    $vehicle_status = $conn->real_escape_string($_POST['vehicle_status'] ?? '');
    
    $sql = "INSERT INTO vehicles (licence_plate_number, daily_rate, vehicle_make, vehicle_brand, vehicle_type, vehicle_year, vehicle_status) 
            VALUES ('$plate', $daily_rate, '$vehicle_make', '$vehicle_brand', '$vehicle_type', $vehicle_year, '$vehicle_status')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Vehicle added successfully']);
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
    <title>Vehicles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class ="navbar navbar-dark bg-dark">
     <div class="container">
      <a class="navbar-brand" href="index.html">Rental System</a>
       <div>
        <a href="vehicles.html" class="btn btn-outline-light btn-sm me-2 active">Vehicles</a>
         <a href="rent.html" class="btn btn-outline-light btn-sm me-2 active">Rent</a>
         <a href="logs.html" class="btn btn-outline-light btn-sm me-2 active">Logs</a>
       </div>
    </div>
    </nav>

       <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Fleet Vehicles</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                + Add Vehicle
            </button>
        </div>

    <div class="row g-3" id="vehiclesContainer">
        <div class="text-center text-muted py-5">Loading Vehicles..</div>
        </div>
    </div>

    <div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Add New Vehicle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
     <form id="addVehicleForm">
       <div class="mb-3">
        <label class="form-label">Licence Plate Number</label>
       <input type="text" class="form-control" id="licence_plate_number" name="licence_plate_number" placeholder="e.g. ABC123" maxlength="6" required>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Vehicle Brand</label>
            <input type="text" class="form-control" id="vehicle_brand" name="vehicle_brand" placeholder="e.g. Toyota" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Vehicle Model</label>
            <input type="text" class="form-control" id="vehicle_make" name="vehicle_make" placeholder="e.g. Corolla" required>
        </div>
 </div>
 <div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Vehicle Type</label>
    <select class="form-select" id="vehicle_type" name="vehicle_type" required>
        <option value="">Select Type</option>
        <option value="Motorcycle">Motorcycle</option>
        <option value="Car">Car</option>
        <option value="Van">Van</option>
        <option value="Truck">Truck</option>
    </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Daily Rate</label>
      <input type="number" class="form-control" id="daily_rate" name="daily_rate" placeholder="1000" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Year</label>
      <input type="number" class="form-control" id="vehicle_year" name="vehicle_year" placeholder="2024" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Vehicle Status</label>
        <select class="form-select" id="vehicle_status" name="vehicle_status" required>
            <option value="">Select Status</option>
            <option value="Available">Available</option>
            <option value="Rented">Rented</option>
            <option value="Maintenance">Maintenance</option>
        </select>
    </div>
 </div>
    </form>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="addNewVehicle()">Save Vehicle</button>
    </div>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
        function renderVehicles() {
            fetch('vehicles.php?action=fetch')
                .then(response => response.json())
                .then(vehicles => {
                    const container = document.getElementById('vehiclesContainer');
                    if (vehicles.length === 0) {
                        container.innerHTML = '<div class="text-center text-muted py-5 w-100">No vehicles available</div>';
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
                                        <strong>Status:</strong> ${v.vehicle_status}
                                    </p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => console.error('Error loading vehicles:', error));
        }

        function addNewVehicle() {
            const form = document.getElementById('addVehicleForm');
            const formData = new FormData(form);
            
            fetch('vehicles.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Vehicle added successfully!');
                    form.reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addVehicleModal'));
                    modal.hide();
                    renderVehicles();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error adding vehicle:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderVehicles();
        });
    </script>
</body>
</html>