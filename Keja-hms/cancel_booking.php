<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hotel_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if booking ID is provided
if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);

    // Verify booking exists and belongs to user/admin
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Delete booking
        $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete_stmt->bind_param("i", $booking_id);
        if ($delete_stmt->execute()) {
            $_SESSION['message'] = "Booking successfully canceled.";
        } else {
            $_SESSION['error'] = "Error canceling booking.";
        }
        $delete_stmt->close();
    } else {
        $_SESSION['error'] = "Booking not found.";
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request.";
}

$conn->close();
header("Location: manage_bookings.php");
exit();
?>
