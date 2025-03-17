<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hotel Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <h2>Hotel Management System</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
        <p>You are logged in as <strong><?php echo $role; ?></strong>.</p>

        <h2>Quick Actions</h2>
        <div>
            <a href="manage_rooms.php" class="button">Manage Rooms</a>
            <a href="manage_bookings.php" class="button">Manage Bookings</a>
            <?php if ($role == 'admin'): ?>
                <a href="manage_users.php" class="button">Manage Users</a>
            <?php endif; ?>
        </div>

<h2>Rooms Overview</h2>
<div class="dashboard-section">
    <div class="table-container">
        <h3>Room Status</h3>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli('localhost', 'root', '', 'hotel_management');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $result = $conn->query("SELECT room_number, status FROM rooms");
                $status_count = ['available' => 0, 'occupied' => 0, 'maintenance' => 0, 'booked' => 0];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_count[strtolower($row['status'])]++;
                        echo "<tr><td>{$row['room_number']}</td><td class='".strtolower($row['status'])."'>".ucfirst($row['status'])."</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No rooms found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="graph-container">
        <h3>Room Availability</h3>
        <canvas id="roomChart"></canvas>
    </div>

    <div class="calendar-container">
        <h3>Booking Calendar</h3>
        <div id="calendar"></div>
    </div>
</div>

<!-- Include Chart.js and FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
// Room Status Graph
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("roomChart").getContext("2d");
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Available", "Occupied", "Maintenance", "Booked"],
            datasets: [{
                data: [<?php echo $status_count['available']; ?>, <?php echo $status_count['occupied']; ?>, <?php echo $status_count['maintenance']; ?>, <?php echo $status_count['booked']; ?>],
                backgroundColor: ["#2ecc71", "#e74c3c", "#f39c12", "#3498db"]
            }]
        }
    });

    // Booking Calendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: "fetch_bookings.php"
    });
    calendar.render();
});
</script>


    </div>
</body>
</html>
