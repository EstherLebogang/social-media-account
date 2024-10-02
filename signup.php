
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 320px;
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

        h1 {
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

        input[type="text"],
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

        input[type="text"]:focus,
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

        em {
            color: red;
            font-style: normal;
            margin-bottom: 15px;
            display: block;
            text-align: center;
            font-size: 14px;
        }

        p {
            margin-top: 15px;
            text-align: center;
            color: #555;
        }

        p a {
            color: #6e8efb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        p a:hover {
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
		
		span {
            color: red;
            font-size: 14px;
        }
	
	</style>
</head>
<body>
    <div class="form-container">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<h2>Sign Up</h2>
            <div>
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <span><?php echo $errors['name'] ?? ''; ?></span>
            </div>
            <div>
                <label>Email:</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <span><?php echo $errors['email'] ?? ''; ?></span>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password">
                <span><?php echo $errors['password'] ?? ''; ?></span>
            </div>
            <div>
                <label>Confirm Password:</label>
                <input type="password" name="password_confirmation">
                <span><?php echo $errors['password_confirmation'] ?? ''; ?></span>
            </div>
            <button type="submit">Sign Up</button>
			<a href="login.php">Login</a>
            <span><?php echo $errors['general'] ?? ''; ?></span>
        </form>
    </div>
</body>
</html>

