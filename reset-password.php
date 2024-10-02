<?php
// Initialize variables and errors
$email = $new_password = $confirm_password = "";
$errors = [];

// Database connection
$mysqli = require __DIR__ . "/database.php";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"])) || !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"])) || strlen(trim($_POST["new_password"])) < 6) {
        $errors['new_password'] = "Password must be at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate password confirmation
    if (empty(trim($_POST["confirm_password"])) || $_POST["confirm_password"] !== $new_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
    }

    // If no errors, proceed with updating the password
    if (empty($errors)) {
        // Fetch current password hash from the database
        $sql = "SELECT password_hash FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($current_password_hash);
                $stmt->fetch();

                // Hash the new password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $sql = "UPDATE user SET password_hash = ? WHERE email = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ss", $new_password_hash, $email);

                if ($stmt->execute()) {
                    // Success message with redirection
                    echo "<script>
                        alert('Password reset successfully. Redirecting to the login page...');
                        window.location.href = 'login.php';
                    </script>";
                    exit(); // Ensure no further code execution
                } else {
                    $errors['general'] = "An error occurred. Please try again.";
                }
            } else {
                $errors['email'] = "No user found with this email address.";
            }
        } else {
            $errors['general'] = "An error occurred. Please try again.";
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $mysqli->close();
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
	
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 320px;
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
        }

        div {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #6e8efb;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #6e8efb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5a73c9;
        }

        span {
            color: red;
            font-size: 14px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #6e8efb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #0056b3;
        }

        /* Animation for form */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Reset Password</h2>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <span><?php echo $errors['email'] ?? ''; ?></span>
            </div>
            <div>
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
                <span><?php echo $errors['new_password'] ?? ''; ?></span>
            </div>
            <div>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <span><?php echo $errors['confirm_password'] ?? ''; ?></span>
            </div>
            <button type="submit">Reset Password</button>
            <a href="login.php">Back to Login</a>
        </form>
    </div>
</body>
</html>

