<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link rel="stylesheet" type="text/css" href="styless.css">
</head>
<body>
    <header>
        <h1>Book Your Stay</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>
    <section>
        <h2>Booking Form</h2>
        <form action="process_booking.php" method="POST">
            <?php
            include 'db.php';

            // Sanitize hotel_id to prevent SQL injection
            if (isset($_GET['hotel_id'])) {
                $hotel_id = intval($_GET['hotel_id']); // Ensure the hotel_id is an integer
            ?>
                <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                
                <label for="check_in">Check-in Date:</label>
                <input type="date" id="check_in" name="check_in" required>

                <label for="check_out">Check-out Date:</label>
                <input type="date" id="check_out" name="check_out" required>

                <label for="room_type">Room Type:</label>
                <select id="room_type" name="room_type" required>
                    <!-- Fetch and display room types from database securely -->
                    <?php
                    // Use prepared statements to prevent SQL injection
                    $stmt = $conn->prepare("SELECT id, room_type, price FROM rooms WHERE hotel_id = ?");
                    $stmt->bind_param("i", $hotel_id); // Bind the hotel_id as an integer
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if there are rooms available
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Safely output room type and price
                            echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['room_type'])." - $".htmlspecialchars($row['price'])."</option>";
                        }
                    } else {
                        echo "<option disabled>No rooms available</option>";
                    }

                    // Close the statement and connection
                    $stmt->close();
                    $conn->close();
                    ?>
                </select>
                <button type="submit">Book Now</button>
            <?php
            } else {
                echo "<p>Invalid request. Please select a hotel.</p>";
            }
            ?>
        </form>
    </section>
    <footer>
        <p>&copy; 2024 Hotel Booking</p>
    </footer>
</body>
</html>
