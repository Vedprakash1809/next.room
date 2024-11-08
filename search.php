<?php
session_start();
include 'db.php';

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$location = '';
$check_in = '';
$check_out = '';

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form inputs
    $location = htmlspecialchars($_POST['location']);
    $check_in = htmlspecialchars($_POST['check_in']);
    $check_out = htmlspecialchars($_POST['check_out']);

    // Prepare and execute the query using prepared statements
    $query = $conn->prepare("SELECT * FROM hotels WHERE location = ?");
    $query->bind_param("s", $location);
    $query->execute();
    $result = $query->get_result();
} else {
    // Handle the case where the page is accessed directly or via GET
    echo "Invalid request method. Please use the search form.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results for <?php echo htmlspecialchars($location); ?></title>
    <link rel="stylesheet" type="text/css" href="styless.css">
</head>
<body>
    <header>
        <h1>Search Results</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>
    <section>
        <h1>Search Results for "<?php echo htmlspecialchars($location); ?>"</h1>
        <div class="hotel-list">
            <?php
            if (isset($result) && $result->num_rows > 0) {
                // Loop through each hotel in the result set
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='hotel'>";
                    // Display the hotel image
                    echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['hotel_name']) . "' class='hotel-image' />";
                    echo "<h2>" . htmlspecialchars($row['hotel_name']) . "</h2>";
                    echo "<p>" . htmlspecialchars($row['address']) . "</p>";
                    echo "<p>Price per night: $" . htmlspecialchars($row['price']) . "</p>";
                    echo "<p>Rating: " . htmlspecialchars($row['rating']) . " / 5</p>";
                    echo "<p>Amenities: " . htmlspecialchars($row['amenities']) . "</p>";
                    echo "<p>Description: " . htmlspecialchars($row['description']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hotels found in this location.</p>";
            }
            ?>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 Hotel Booking</p>
    </footer>
    <style>
        .hotel-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .hotel {
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .hotel-image {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</body>
</html>
<?php
// Close the prepared statement and database connection
if (isset($query)) {
    $query->close();
}
$conn->close();
?>
