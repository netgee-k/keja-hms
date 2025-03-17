<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'hotel_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $_SESSION['user_id'], $room_id, $check_in, $check_out);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->close();
}

$rooms_result = $conn->query("SELECT * FROM rooms WHERE status = 'available'");
$bookings_result = $conn->query("SELECT bookings.*, rooms.room_number FROM bookings JOIN rooms ON bookings.room_id = rooms.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Hotel Management System</title>
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
        <h1>Manage Bookings</h1>
        <form action="manage_bookings.php" method="post">
            <label for="room_id">Room:</label>
            <select id="room_id" name="room_id" required>
                <?php while ($row = $rooms_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['room_number']; ?> (<?php echo $row['room_type']; ?>)</option>
                <?php endwhile; ?>
            </select>

            <label for="check_in">Check-in Date:</label>
            <input type="date" id="check_in" name="check_in" required>

            <label for="check_out">Check-out Date:</label>
            <input type="date" id="check_out" name="check_out" required>

            <button type="submit" class="button">Book Room</button>
        </form>

        <h2>Booking List</h2>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Actions</th>
<td><a href="cancel_booking.php?id=<?php echo $row['id']; ?>" class="button-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a></td>


                </tr>
            </thead>
            <tbody>
                <?php while ($row = $bookings_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['room_number']; ?></td>
                        <td><?php echo $row['check_in']; ?></td>
                        <td><?php echo $row['check_out']; ?></td>
                        <td>
                            <a href="cancel_booking.php?id=<?php echo $row['id']; ?>" class="button button-danger">Cancel</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
