
<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Please submit a booking using the service form.");
}

$name = trim($_POST["customer_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$registration = strtoupper(trim($_POST["registration"] ?? ""));
$vehicleType = trim($_POST["vehicle_type"] ?? "");
$model = trim($_POST["vehicle_model"] ?? "");
$year = filter_var($_POST["year"] ?? "", FILTER_VALIDATE_INT);
$serviceDate = trim($_POST["service_date"] ?? "");

$servicePrices = [
    "Engine Oil Change" => 2000,
    "Brake Inspection" => 3000,
    "Wheel Alignment" => 3500,
    "Computer Diagnosis" => 2500,
    "Full Service" => 8000,
    "Tyre Balancing" => 2000
];

$vehicleTypes = ["Saloon Car", "SUV", "Pickup", "Van"];
$errors = [];

if ($name === "") {
    $errors[] = "Customer name is required.";
}

if (!preg_match('/^\d{10}$/', $phone)) {
    $errors[] = "Phone number must contain exactly 10 digits.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

if ($registration === "") {
    $errors[] = "Vehicle registration is required.";
}

if (!in_array($vehicleType, $vehicleTypes, true)) {
    $errors[] = "Please select a valid vehicle type.";
}

if ($model === "") {
    $errors[] = "Vehicle model is required.";
}

$currentYear = (int) date("Y");

if ($year === false || $year < 1900 || $year > $currentYear) {
    $errors[] = "Please provide a valid manufacturing year.";
}

$dateObject = DateTime::createFromFormat("!Y-m-d", $serviceDate);
$validDate = $dateObject !== false
    && $dateObject->format("Y-m-d") === $serviceDate;

if (!$validDate || $serviceDate < date("Y-m-d")) {
    $errors[] = "Please select a valid date today or in the future.";
}

$selectedServices = $_POST["services"] ?? [];

if (!is_array($selectedServices) || count($selectedServices) === 0) {
    $errors[] = "Please select at least one service.";
} elseif (count($selectedServices) !== count(array_unique($selectedServices))) {
    $errors[] = "Duplicate services are not allowed.";
} else {
    foreach ($selectedServices as $service) {
        if (!is_string($service) || !array_key_exists($service, $servicePrices)) {
            $errors[] = "An invalid service was submitted.";
            break;
        }
    }
}

if (!empty($errors)) {
    echo "<h1>Booking Could Not Be Submitted</h1>";
    echo "<ul>";

    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</li>";
    }

    echo "</ul>";
    echo '<p><a href="service.html">Return to the booking form</a></p>';
    exit;
}

$total = 0;

foreach ($selectedServices as $service) {
    $total += $servicePrices[$service];
}

function safeOutput($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Anne's Garage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="booking-page">

    <nav class="navbar">
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="index.html#about">About</a>
            <a href="index.html#services">Services</a>
            <a href="index.html#contact">Contact</a>
            <a href="service.html" class="nav-book-btn">Book us</a>
        </div>
    </nav>

    <main class="booking-container confirmation-container">
        

        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>Service Request Received</h1>
            <p>Thank you! Your booking has been successfully submitted. We will contact you shortly to confirm the details.</p>
        </div>

        <div class="form-card">
            <h2>Customer Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Customer:</span>
                    <span class="info-value"><?= safeOutput($name) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?= safeOutput($phone) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= safeOutput($email) ?></span>
                </div>
            </div>
        </div>

        <div class="form-card">
            <h2>Vehicle Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Registration:</span>
                    <span class="info-value"><?= safeOutput($registration) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehicle Type:</span>
                    <span class="info-value"><?= safeOutput($vehicleType) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Model:</span>
                    <span class="info-value"><?= safeOutput($model) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Year of Manufacture:</span>
                    <span class="info-value"><?= safeOutput($year) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Preferred Service Date:</span>
                    <span class="info-value"><?= safeOutput($serviceDate) ?></span>
                </div>
            </div>
        </div>

        <div class="form-card">
            <h2>Selected Services</h2>
            <ul class="confirmation-services-list">
                <?php foreach ($selectedServices as $service): ?>
                    <li>
                        <span><?= safeOutput($service) ?></span>
                        <span class="service-price">KSh <?= number_format($servicePrices[$service]) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="form-card estimate-card">
            <div class="confirmation-total">
                <h2>Estimated Service Cost</h2>
                <p class="total-price-large">KSh <?= number_format($total) ?></p>
                <p class="helper-text">* This is an estimate. Final cost may vary based on inspection.</p>
            </div>

            <div class="confirmation-actions">
                <a href="service.html" class="hero-button">Book Another Service</a>
                <a href="index.html" class="secondary-btn">Return to Home</a>
            </div>
        </div>

    </main>

    <footer>
        <p>&copy; 2026 Anne's Garage. All rights reserved.</p>
    </footer>

</body>
</html>
