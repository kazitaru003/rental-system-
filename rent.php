<?php
include 'db.php';

// Handle API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'fetch-available') {
    header('Content-Type: application/json');
    $result = $conn->query("SELECT * FROM vehicles WHERE vehicle_status = 'Available'");
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
    exit;
}

if ($action === 'add-rental' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $renter_name = $conn->real_escape_string($_POST['renter_name'] ?? '');
    $renter_contact = intval($_POST['renter_contact'] ?? 0);
    $licence_plate_number = $conn->real_escape_string($_POST['licence_plate_number'] ?? '');
    $days_rented = intval($_POST['days_rented'] ?? 0);
    $rental_start = $conn->real_escape_string($_POST['rental_start'] ?? '');
    $rental_end = $conn->real_escape_string($_POST['rental_end'] ?? '');
    $rental_status = $conn->real_escape_string($_POST['rental_status'] ?? 'Active');
    
    $sql = "INSERT INTO history (renter_name, renter_contact, licence_plate_number, days_rented, rental_start, rental_end, rental_status) 
            VALUES ('$renter_name', $renter_contact, '$licence_plate_number', $days_rented, '$rental_start', '$rental_end', '$rental_status')";
    
    if ($conn->query($sql)) {
        $updateVehicle = "UPDATE vehicles SET vehicle_status = 'Rented' WHERE licence_plate_number = '$licence_plate_number'";
        $conn->query($updateVehicle);
        echo json_encode(['success' => true, 'message' => 'Rental created successfully']);
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
    <title>Rent System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class ="navbar navbar-dark bg-dark">
     <div class="container">
      <a class="navbar-brand" href="index.php">RentEase</a>
       <div>
        <a href="vehicles.php" class="btn btn-outline-light btn-sm me-2 active">Vehicles</a>
         <a href="rent.php" class="btn btn-outline-light btn-sm me-2 active">Rent</a>
         <a href="logs.php " class="btn btn-outline-light btn-sm me-2 active">Logs</a>
       </div>
    </div>
    </nav>

    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Make a rental</h4>
        </div>

        <div class="row">
            <div class="col-lg-7">
            <div class="card">
            <div class="card-header bg-light">
                <strong>Available Vehicles</strong>
                </div>
                <div class="card-body">
                <div class="row g-3" id="availableVehiclesContainer">
                <div class="col-12 text-center text-muted py-4">
                    Loading Available Vehicles....
                </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
             <div class="card-header bg-light">
                <strong>Rental Details</strong>
        </div>
        <div class="card-body p-4">
        <form id="rentalForm">
            <div class="mb-4">
           <label class="form-label">Selected Vehicle</label>
           <input type="text" class="form-control" id="selectedVehicle" readonly placeholder="No vehicles selected">
           <input type="hidden" id="licence_plate_number" name="licence_plate_number">
           <input type="hidden" id="daily_rate" name="daily_rate">
        </div>
        <div class="mb-4">
             <label class="form-label">Renter Name</label>
             <input type="text" class="form-control" id="renter_name" name="renter_name" required>
        </div>
        <div class="mb-4">
             <label class="form-label">Contact Number</label>
             <input type="number" class="form-control" id="renter_contact" name="renter_contact" required>
        </div>
        <div class="mb-4">
             <label class="form-label">Rental Start Date</label>
             <input type="date" class="form-control" id="rental_start" name="rental_start" required>
        </div>
        <div class="mb-4">
             <label class="form-label">Rental End Date</label>
             <input type="date" class="form-control" id="rental_end" name="rental_end" required>
        </div>
        <div class="mb-4">
             <label class="form-label">Days Rented</label>
             <input type="number" class="form-control" id="days_rented" name="days_rented" readonly>
        </div>
        <div class="mb-4">
             <label class="form-label">Total Amount</label>
             <input type="text" class="form-control" id="totalAmount" readonly>
            </div>
          
             <div class="pt-3">
            <button type="button" class="btn btn-primary w-100" onclick="processRental()">
                Confirm Rental
            </button>

        </form>
        </div>
        </div>
        </div>
        </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedVehicle = null;

        function renderRentPage() {
            loadAvailableVehicles();
        }

        function loadAvailableVehicles() {
            fetch('rent.php?action=fetch-available')
                .then(response => response.json())
                .then(vehicles => {
                    const container = document.getElementById('availableVehiclesContainer');
                    if (vehicles.length === 0) {
                        container.innerHTML = '<div class="col-12 text-center text-muted py-4">No available vehicles</div>';
                        return;
                    }
                    container.innerHTML = vehicles.map(v => `
                        <div class="col-md-6">
                            <div class="card h-100" style="cursor: pointer;" onclick="selectVehicle('${v.licence_plate_number}', '${v.vehicle_brand} ${v.vehicle_make}', ${v.daily_rate})">
                                <div class="card-body">
                                    <h5 class="card-title">${v.vehicle_brand} ${v.vehicle_make}</h5>
                                    <p class="card-text">
                                        <strong>Plate:</strong> ${v.licence_plate_number}<br>
                                        <strong>Type:</strong> ${v.vehicle_type}<br>
                                        <strong>Year:</strong> ${v.vehicle_year}<br>
                                        <strong>Daily Rate:</strong> $${v.daily_rate}/day
                                    </p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => console.error('Error loading vehicles:', error));
        }

        function selectVehicle(plate, name, dailyRate) {
            selectedVehicle = { plate, name, dailyRate };
            document.getElementById('selectedVehicle').value = name;
            document.getElementById('licence_plate_number').value = plate;
            document.getElementById('daily_rate').value = dailyRate;
            calculateTotalAmount();
        }

        function calculateTotalAmount() {
            const startDate = document.getElementById('rental_start').value;
            const endDate = document.getElementById('rental_end').value;
            
            if (startDate && endDate && selectedVehicle) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const daysRented = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                
                if (daysRented > 0) {
                    document.getElementById('days_rented').value = daysRented;
                    const totalAmount = daysRented * selectedVehicle.dailyRate;
                    document.getElementById('totalAmount').value = '$' + totalAmount.toFixed(2);
                }
            }
        }

        function processRental() {
            if (!selectedVehicle) {
                alert('Please select a vehicle');
                return;
            }

            const form = document.getElementById('rentalForm');
            const formData = new FormData(form);
            formData.append('rental_status', 'Active');
            
            fetch('rent.php?action=add-rental', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Rental created successfully!');
                    form.reset();
                    selectedVehicle = null;
                    document.getElementById('selectedVehicle').value = '';
                    loadAvailableVehicles();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error processing rental:', error));
        }

        document.getElementById('rental_start').addEventListener('change', calculateTotalAmount);
        document.getElementById('rental_end').addEventListener('change', calculateTotalAmount);

        document.addEventListener('DOMContentLoaded', function() {
            renderRentPage(); 
        });
    </script>
</body>
</html>