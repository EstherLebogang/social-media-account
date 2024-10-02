<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$mysqli = require __DIR__ . "/database.php";
	
	$sql = sprintf("SELECT * FROM user
					WHERE email = '%s'",
					$mysqli->real_escape_string($_POST["email"]));
	
	$result = $mysqli->query($sql);
	
	$user = $result->fetch_assoc();
	
	if ($user){
		if ( password_verify($_POST["password"], $user["password_hash"])) {
			session_start();
			
			session_regenerate_id();
			
			$_SESSION["user_id"] = $user["id"];
			
			header("Location: index.php");
			exit;
		}
	}
	
	$is_invalid = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Login</title>
	
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

	</style>
	
</head>
<body>


    <form method="post">
	
	<h1>Login</h1>
	
	<?php if ($is_invalid): ?>
		<em>Invalid Login</em>
	<?php endif; ?>
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST["name"] ?? "") ?>" required>
        </div>

        <div>
            <label for="email">email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required>
        </div>

        <div>
            <label for="password">password</label>
            <input type="password" id="password" name="password">
        </div>
		
		<button>Log in</button>
		<p>Dont have an account?<a href="signup.php">sign up</a></p>
		<p>forgot password?<a href="reset-password.php">Resset password</a></p>
		
    </form>


</body>
</html>  
