<?php
$conn = new mysqli('localhost', 'root', '', 'hotel_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT room_number, check_in, check_out FROM bookings";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => 'Room ' . $row['room_number'],
        'start' => $row['check_in'],
        'end' => $row['check_out'],
        'color' => '#3498db'
    ];
}

$conn->close();
echo json_encode($events);
?>
