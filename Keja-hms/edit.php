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

// Ensure a valid room ID is provided
if (!isset($_GET['id'])) {
    echo "<script>alert('Room ID is missing!'); window.location.href='manage_rooms.php';</script>";
    exit();
}

$room_id = $_GET['id'];

// Fetch existing room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();

if (!$room) {
    echo "<script>alert('Room not found!'); window.location.href='manage_rooms.php';</script>";
    exit();
}

// Handle Update Room
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Debugging Output
    error_log("Updating Room ID: $room_id with Room Number: $room_number, Type: $room_type, Price: $price, Status: $status");

    $update_stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, price = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("ssdsi", $room_number, $room_type, $price, $status, $room_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Room updated successfully!'); window.location.href='manage_rooms.php';</script>";
    } else {
        error_log("Update failed: " . $conn->error);  // Log the error for debugging
        echo "<script>alert('Update failed! Please try again.');</script>";
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <h2>Hotel Management System</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_rooms.php">Manage Rooms</a>
            <a href="manage_bookings.php">Manage Bookings</a>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="manage_users.php">Manage Users</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <h1>Edit Room</h1>
        <form method="POST">
            <label for="room_number">Room Number:</label>
            <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>

            <label for="room_type">Room Type:</label>
            <input type="text" id="room_type" name="room_type" value="<?php echo htmlspecialchars($room['room_type']); ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($room['price']); ?>" required>

            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="Available" <?php if ($room['status'] == 'Available') echo 'selected'; ?>>Available</option>
                <option value="Occupied" <?php if ($room['status'] == 'Occupied') echo 'selected'; ?>>Occupied</option>
            </select>

            <button type="submit" name="update">Update Room</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this room?');">Delete Room</button>
        </form>
    </div>
</body>
</html>
