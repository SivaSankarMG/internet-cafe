<?php
session_start();
include "connect.php";

// Validate booking constraints
function validateBooking($booking_date, $start_time, $duration) {
    $current_date = date('Y-m-d');
    $max_date = date('Y-m-d', strtotime('+2 days'));

    // Check if booking date is within allowed range (up to 2 days in advance)
    if ($booking_date < $current_date || $booking_date > $max_date) {
        return false;
    }

    // Check if the booking time is within allowed hours (9am to 10pm)
    $start_hour = (int)date('H', strtotime($start_time));
    $end_hour = $start_hour + ceil($duration / 60);

    if ($start_hour < 9 || $end_hour > 22) {
        return false;
    }

    // Check if the duration is in multiples of 30 minutes
    if ($duration % 30 !== 0 || $duration < 30) {
        return false;
    }

    // Check if booking is in the past on the current date
    if ($booking_date == $current_date) {
        $current_time = date('H:i:s');
        if ($start_time < $current_time) {
            return false;
        }
    }

    // Additional check for 9:30 PM slot
    if ($start_time == "21:30" && $duration > 30) {
        return false;
    }

    return true;
}

// Get available systems and their time slots
function getAvailableSystems($conn) {
    $sql = "SELECT * FROM systems";
    $result = $conn->query($sql);
    $systems = [];

    while ($row = $result->fetch_assoc()) {
        $systems[] = $row;
    }

    return $systems;
}

// Function to check availability
function checkAvailability($conn, $system_id, $booking_date, $start_time) {
    $duration = 30; // Check for 30 minute intervals
    $end_time = date('H:i:s', strtotime($start_time) + $duration * 60);

    $sql = "SELECT * FROM bookings WHERE system_id = '$system_id' AND booking_date = '$booking_date' AND 
            (start_time < '$end_time' AND DATE_ADD(start_time, INTERVAL duration MINUTE) > '$start_time')";
    $result = $conn->query($sql);

    return ($result->num_rows == 0) ? 'Available' : 'Booked';
}

// Function to update the system status based on the day's bookings
function updateSystemStatus($conn, $system_id, $booking_date) {
    $times = ["09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30"];
    $allBooked = true;

    foreach ($times as $time) {
        if (checkAvailability($conn, $system_id, $booking_date, $time) === 'Available') {
            $allBooked = false;
            break;
        }
    }

    $status_column = ($booking_date == date('Y-m-d')) ? 'status' :
                     (($booking_date == date('Y-m-d', strtotime('+1 day'))) ? 'status_tomorrow' : 'status_day_after_tomorrow');

    $newStatus = $allBooked ? 'booked' : 'available';
    $updateStatus = "UPDATE systems SET $status_column = '$newStatus' WHERE id = '$system_id'";
    $conn->query($updateStatus);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['Email'])) {
        header("Location: SignIn.php");
        exit();
    }

    $system_id = $_POST['system_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $duration = (int)$_POST['duration'];
    $payment_method = $_POST['payment_method'];

    if (!validateBooking($booking_date, $start_time, $duration)) {
        echo "Invalid booking time or duration.";
        exit();
    }

    // Check if the selected time slot is available
    $end_time = date('H:i:s', strtotime($start_time) + $duration * 60);
    $checkAvailability = "SELECT * FROM bookings WHERE system_id = '$system_id' AND booking_date = '$booking_date' AND 
                          (start_time < '$end_time' AND DATE_ADD(start_time, INTERVAL duration MINUTE) > '$start_time')";
    $result = $conn->query($checkAvailability);

    if ($result->num_rows > 0) {
        echo "The selected time slot is not available.";
        exit();
    }

    // Check if the user already has a booking for the same time slot
    $email = $_SESSION['Email'];
    $getUserId = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($getUserId);
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    $checkUserBooking = "SELECT * FROM bookings WHERE user_id = '$user_id' AND booking_date = '$booking_date' AND 
                         (start_time < '$end_time' AND DATE_ADD(start_time, INTERVAL duration MINUTE) > '$start_time')";
    $result = $conn->query($checkUserBooking);

    if ($result->num_rows > 0) {
        echo "You already have a booking for this time slot.";
        exit();
    }

    // Book the system
    $bookSystem = "INSERT INTO bookings (user_id, system_id, booking_date, start_time, duration) VALUES ('$user_id', '$system_id', '$booking_date', '$start_time', '$duration')";
    if ($conn->query($bookSystem) === TRUE) {
        echo "Booking successful.";
        header("Location: dashboard.php?section=mybookings");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    // Update system status for the specific day
    updateSystemStatus($conn, $system_id, $booking_date);
}

// Fetch available systems
$systems = getAvailableSystems($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now</title>
</head>
<body>
    <h1>Book a System</h1> <a href="dashboard.php" style="margin-left:300px;">Dashboard</a>

    <form method="post" action="book.php">
        <label for="system_id">Select System:</label>
        <select id="system_id" name="system_id" required>
            <?php foreach ($systems as $system): ?>
                <option value="<?= $system['id'] ?>"><?= $system['type'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="booking_date">Booking Date:</label>
        <select id="booking_date" name="booking_date" required>
            <option value="<?= date('Y-m-d') ?>">Today</option>
            <option value="<?= date('Y-m-d', strtotime('+1 day')) ?>">Tomorrow</option>
            <option value="<?= date('Y-m-d', strtotime('+2 days')) ?>">Day After Tomorrow</option>
        </select><br>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required step="1800" min="09:00" max="21:30"><br>

        <label for="duration">Duration (minutes):</label>
        <input type="number" id="duration" name="duration" required step="30" min="30"><br>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="online">Online Payment</option>
            <option value="onspot">Onspot Booking</option>
        </select><br>

        <button type="submit">Book Now</button>
    </form>

    <h2>Available Systems</h2>
    <?php foreach ($systems as $system): ?>
        <h3>System ID: <?= $system['id'] ?> (<?= $system['type'] ?>)</h3>
        <table border="1">
            <tr>
                <th>Time</th>
                <th>Today</th>
                <th>Tomorrow</th>
                <th>Day After Tomorrow</th>
            </tr>
            <?php
            $times = ["09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30"];
            foreach ($times as $time) {
                echo "<tr>";
                echo "<td>$time</td>";
                echo "<td>" . checkAvailability($conn, $system['id'], date('Y-m-d'), $time) . "</td>";
                echo "<td>" . checkAvailability($conn, $system['id'], date('Y-m-d', strtotime('+1 day')), $time) . "</td>";
                echo "<td>" . checkAvailability($conn, $system['id'], date('Y-m-d', strtotime('+2 days')), $time) . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php endforeach; ?>
</body>
</html>