<?php
// Start session to access session variables
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['Email'])) {
    header("Location: SignIn.php");
    exit();
}

// Database connection code here
include "connect.php";

function validateName($name) {
    // Name can only contain letters and numbers
    return preg_match('/^[a-zA-Z0-9]+$/', $name);
}

function validateEmail($email) {
    // Email should match a standard email pattern
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    // Password should be at least 8 characters long, containing at least one uppercase letter, one lowercase letter, one number, and one special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Fetch user details from the database based on email stored in session
$email = $_SESSION['Email'];
$getUserDetails = "SELECT * FROM users WHERE Email = '$email'";
$result = $conn->query($getUserDetails);

// Initialize user details
$user_id = null;
$userName = '';
$userEmail = '';

// Check if we have a valid user
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['Id'];
    $userName = $row['Name'];
    $userEmail = $row['Email'];
}

// Check if form is submitted for editing user details
if (isset($_POST["editProfile"])) {
    // Process form data and update user details in the database
    $newName = $_POST['newName'];
    $newEmail = $_POST['newEmail'];

    if (!validateName($newName)) {
        echo "Invalid name format.<br>";
    } elseif (!validateEmail($newEmail)) {
        echo "Invalid email format.<br>";
    } else {
        $updateDetails = "UPDATE users SET Name = '$newName', Email = '$newEmail' WHERE Email = '$email'";
        if ($conn->query($updateDetails) === TRUE) {
            // Update session variable with new email
            $_SESSION['Email'] = $newEmail;
            // Refresh the page to reflect changes
            header("Location: dashboard.php?section=profile");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Check if form is submitted for changing password
if (isset($_POST["changePassword"])) {
    // Process form data and update password in the database
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Fetch the password from the database
    $checkPassword = "SELECT Password FROM users WHERE Email = '$email'";
    $result = $conn->query($checkPassword);
    $row = $result->fetch_assoc();
    $passwordFromDB = $row['Password'];

    // Verify if the current password provided by the user matches the password from the database
    if ($currentPassword === $passwordFromDB) {
        // Check if new password and confirm password match
        if ($newPassword === $confirmPassword) {
            // Validate new password
            if (!validatePassword($newPassword)) {
                echo "Invalid password format.<br>";
            } else {
                // Update password in the database
                $updatePassword = "UPDATE users SET Password = '$newPassword' WHERE Email = '$email'";
                if ($conn->query($updatePassword) === TRUE) {
                    echo "Password updated successfully.";
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            }
        } else {
            echo "New password and confirm password do not match.";
        }
    } else {
        echo "Incorrect current password.";
    }
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
    $times = ["09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00"];
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Get booking details
    $sql = "SELECT * FROM bookings WHERE id = '$booking_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();

    if ($booking) {
        // Delete booking
        $deleteBooking = "DELETE FROM bookings WHERE id = '$booking_id'";
        if ($conn->query($deleteBooking) === TRUE) {
            // Update system status for the specific day
            updateSystemStatus($conn, $booking['system_id'], $booking['booking_date']);
            echo "Booking cancelled successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Booking not found or you don't have permission to cancel this booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <!-- Left navigation bar -->
    <div class="sidenav">
        <a href="dashboard.php?section=profile">Profile</a>
        <a href="dashboard.php?section=mybookings">My Bookings</a>
        <a href="book.php">Book Now</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main content -->
    <div class="content">
        <?php
        // Display different sections based on selected section
        $section = isset($_GET['section']) ? $_GET['section'] : 'profile';
        switch ($section) {
            case 'mybookings':
                // Display My Bookings page
                echo "<h2>My Bookings</h2>";
                
                // Fetch user's bookings
                $sql = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY booking_date DESC, start_time DESC";
                $bookings = $conn->query($sql);

                if ($bookings->num_rows > 0) {
                    echo "<table border='1'>
                            <tr>
                                <th>System ID</th>
                                <th>Booking Date</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>";

                    while ($booking = $bookings->fetch_assoc()) {
                        echo "<tr>
                                <td>{$booking['system_id']}</td>
                                <td>{$booking['booking_date']}</td>
                                <td>{$booking['start_time']}</td>
                                <td>{$booking['duration']}</td>
                                <td>
                                    <form method='post' action='dashboard.php?section=mybookings'>
                                        <input type='hidden' name='booking_id' value='{$booking['id']}'>
                                        <button type='submit' name='cancel_booking'>Cancel</button>
                                    </form>
                                </td>
                              </tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>No bookings found.</p>";
                }
                
                break;
            case 'profile':
            default:
                // Display Profile page by default
                echo "<h2>Profile</h2>";
                // Display user details fetched from the database
                echo "<p>Name: " . htmlspecialchars($userName) . "</p>";
                echo "<p>Email: " . htmlspecialchars($userEmail) . "</p>";
                // Add form for editing user details
                echo "<form method='post' action=''>";
                echo "<label for='newName'>New Name:</label>";
                echo "<input type='text' id='newName' name='newName' value='" . htmlspecialchars($userName) . "'><br>";
                echo "<label for='newEmail'>New Email:</label>";
                echo "<input type='email' id='newEmail' name='newEmail' value='" . htmlspecialchars($userEmail) . "'><br>";
                echo "<input type='submit' value='Save Changes' name='editProfile'>";
                echo "</form>";

                // Form for changing password
                echo "<h3>Change Password</h3>";
                echo "<form method='post' action=''>";
                echo "<label for='currentPassword'>Current Password:</label>";
                echo "<input type='password' id='currentPassword' name='currentPassword'><br>";
                echo "<label for='newPassword'>New Password:</label>";
                echo "<input type='password' id='newPassword' name='newPassword'><br>";
                echo "<label for='confirmPassword'>Confirm Password:</label>";
                echo "<input type='password' id='confirmPassword' name='confirmPassword'><br>";
                echo "<input type='submit' value='Change Password' name='changePassword'>";
                echo "</form>";
                break;
        }
        ?>
    </div>
</body>
<script>
    // This script will check for URL parameters to display messages
    document.addEventListener("DOMContentLoaded", function() {
        const params = new URLSearchParams(window.location.search);
        if (params.has('error')) {
            const error = params.get('error');
            alert(error);
        } else if (params.has('success')) {
            alert('Registration successful. Welcome!');
            // Redirect if needed or clear the search parameters
            window.location.href = 'login.html';
            
            // After successful login
            localStorage.setItem("loggedIn", "true");

        }
    });
</script>
</html>
