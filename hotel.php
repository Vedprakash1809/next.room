<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Details</title>
    <link rel="stylesheet" type="text/css" href="styless.css">
    <!-- FontAwesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <h1>Next.Room</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <section>
        <h2>Hotel Details</h2>
        <div class="hotel-detail">
            <?php
            include 'db.php';  // Include the database connection file
            $hotel_id = $_GET['id'];  // Get the hotel ID from the URL

            // Fetch hotel details
            $sql = "SELECT * FROM hotels WHERE id = $hotel_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<img src='".$row['image']."' alt='".$row['name']."'>";
                echo "<h3>".$row['name']."</h3>";
                echo "<p>".$row['location']."</p>";
                echo "<p>".$row['description']."</p>";
            } else {
                echo "<p>Hotel details not found.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="hotel-rooms">
        <h2>Hotel Rooms</h2>
        <div id="rooms-section">
            <?php
            // Fetch room details
            $sql_rooms = "SELECT * FROM rooms WHERE hotel_id = $hotel_id";
            $result_rooms = $conn->query($sql_rooms);

            if ($result_rooms->num_rows > 0) {
                while($room = $result_rooms->fetch_assoc()) {
                    echo "<div class='room'>";
                    echo "<h3>".$room['room_type']."</h3>";
                    echo "<p>Price: $".$room['price']." per night</p>";
                    echo "<p>Availability: ".$room['availability']."</p>";
                    echo "<a href='booking.php?room_id=".$room['id']."' class='book-now'>";
                    echo "<i class='fas fa-shopping-cart'></i> Book Now</a>";
                    echo "</div><hr>";
                }
            } else {
                echo "<p>No rooms available for this hotel.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Flats Section -->
    <section class="hotel-flats">
        <h2>Hotel Flats</h2>
        <div id="flats-section">
            <?php
            // Fetch flat details
            $sql_flats = "SELECT * FROM flats WHERE hotel_id = $hotel_id";
            $result_flats = $conn->query($sql_flats);

            if ($result_flats->num_rows > 0) {
                while($flat = $result_flats->fetch_assoc()) {
                    echo "<div class='flat'>";
                    echo "<h3>".$flat['flat_type']."</h3>"; // Assuming 'flat_type' column exists
                    echo "<p>Price: $".$flat['price']." per month</p>"; // Assuming 'price' column exists
                    echo "<p>Availability: ".$flat['availability']."</p>"; // Assuming 'availability' column exists
                    echo "<a href='booking.php?flat_id=".$flat['id']."' class='book-now'>";
                    echo "<i class='fas fa-shopping-cart'></i> Book Now</a>";
                    echo "</div><hr>";
                }
            } else {
                echo "<p>No flats available for this hotel.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Room Booking</p>
    </footer>
</body>
</html>
