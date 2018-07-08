<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){    
    try {
        $connection = new PDO($dsn, $username, $password, $options);
        
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = 'Please enter a username.';
        } else{
            $username = trim($_POST["username"]);
        }
        
        // Check if password is empty
        if(empty(trim($_POST['password']))){
            $password_err = 'Please enter your password.';
        } else{
            $password = trim($_POST['password']);
        }
        
        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $sql = "SELECT username, password FROM users WHERE username = :username";
            
            if($stmt = $connection->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
                
                // Set parameters
                $param_username = trim($_POST["username"]);
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Check if username exists, if yes then verify password
                    if($stmt->rowCount() == 1){
                        if($row = $stmt->fetch()){
                            $hashed_password = $row['password'];
                            if(password_verify($password, $hashed_password)){
                                /* Password is correct, so start a new session and
                                 save the username to the session */
                                session_start();
                                $_SESSION['username'] = $username;
                                header("location:public/index.php");
                            } else{
                                // Display an error message if password is not valid
                                $password_err = 'The password you entered is invalid.';
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $username_err = 'No account found with that username.';
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            
            // Close statement
            unset($stmt);
        }
        
        // Close connection
        unset($connection);
    } catch(PDOException $error) {
        echo $sql . "<br>" . $error->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>MediBook Login</title>
  <link rel="icon" href="favicon.png"/>
  <link rel="stylesheet" href="login/css/style.css">
</head>

<body>

  <div class="login-page">
  <center><h2 style="color:#002B70">MediBook Clinic Application</h2></center>
  <div class="form">
    <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
          <input type="text" name="username" class="form-control" placeholder="username" value="<?php echo $username; ?>">
          <span style="display:inline-block; padding-bottom: 15px; color:red;"><?php echo $username_err; ?></span>
      </div> 
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
          <input type="password" name="password" class="form-control" placeholder="password" value="<?php echo $password; ?>">
          <span style="display:inline-block; padding-bottom: 15px; color:red;"><?php echo $password_err; ?></span>
      </div>            
      <button type="submit" value="Submit">login</button>
    </form>
  </div>
  </div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script  src="login/js/index.js"></script>
</body>

</html>