<?php
// Enable error reporting at the top of the script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables and errors
$name = $email = "";
$errors = [];

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"])) || !preg_match("/^[a-zA-Z\s]+$/", trim($_POST["name"]))) {
        $errors['name'] = "Please enter a valid name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"])) || !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"])) || strlen(trim($_POST["password"])) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    // Validate password confirmation
    if (empty(trim($_POST["password_confirmation"])) || $_POST["password"] !== $_POST["password_confirmation"]) {
        $errors['password_confirmation'] = "Passwords do not match.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // Include database connection
        $mysqli = require __DIR__ . "/database.php";

        // Check if the email already exists in the database
        $sql_check_email = "SELECT id FROM user WHERE email = ? LIMIT 1";
        $stmt_check = $mysqli->stmt_init();

        if (!$stmt_check->prepare($sql_check_email)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Email already exists
            $errors['email'] = "This email address is already registered.";
        } else {
            // Prepare the SQL statement for inserting a new user
            $sql = "INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)";

            $stmt = $mysqli->stmt_init();

            if (!$stmt->prepare($sql)) {
                die("SQL prepare error: " . $mysqli->error);
            }

            // Bind parameters
            $stmt->bind_param("sss", $name, $email, $password_hash);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to login page upon successful registration
                header("Location: login.php");
                exit();
            } else {
                $errors['general'] = "An error occurred. Please try again.";
            }

            // Close the statement
            $stmt->close();
        }

        // Close the statement for email check
        $stmt_check->close();

        // Close the database connection
        $mysqli->close();
    }

    // Debugging: Print errors
    if (!empty($errors)) {
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
    }
}
?>

