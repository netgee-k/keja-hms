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
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $room_number, $room_type, $price);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Hotel Management System</title>
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
        <h1>Manage Rooms</h1>
        <form action="manage_rooms.php" method="post">
            <label for="room_number">Room Number:</label>
            <input type="text" id="room_number" name="room_number" required>

            <label for="room_type">Room Type:</label>
            <input type="text" id="room_type" name="room_type" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <button type="submit" class="button">Add Room</button>
        </form>

        <h2>Room List</h2>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['room_number']; ?></td>
                        <td><?php echo $row['room_type']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <a href="edit_room.php?id=<?php echo $row['id']; ?>" class="button">Edit</a>
                            <a href="delete_room.php?id=<?php echo $row['id']; ?>" class="button button-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
