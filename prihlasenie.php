<?php
// Initialize the session
session_start();
// Include config file
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/order.php';

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>eshop - Profile</title>
    </head>
    <body>
        <div>
            <h2>Welcome</h2>
            <h3>Your orders</h3>
            <?php 
            $database = new Database();
            $db = $database->getConnection();

            $order = new Order($db);
            $order->user_id = $_SESSION["id"];

            $stmt = $order->read();

            $num = $stmt->rowCount();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                echo "id: " . $id . ", price:    " . $total_price . "€<br>";
            }
            ?>
            <p>Your token: <?php echo $_SESSION["token"]; ?></p>
            <a href="/eshop">to shop</a>
            <a href="prihlasenie.php"><?php echo $_SESSION["username"]; ?></a>
            <a href="odhlasenie.php">Sign out</a>
        </div>    
    </body>
    </html>
    <?php
    exit;
}
 

 
// Define variables and initialize with empty values
$input_email = $input_password = "";
$email_err = $password_err = "";
 
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } 
    else {
        $input_email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } 
    else {
        $input_password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $database = new Database();
        $db = $database->getConnection();

        // create product object
        $user = new User($db);

        // set properties of record to read
        $user->email = $input_email;

        $stmt = $user->read();

        $num = $stmt->rowCount();

        if($num == 1){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            echo md5($input_password) == $password;
            if(md5($input_password) == $password){
                // Password is correct, so start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["email"] = $email;
                $_SESSION["token"] = $token;
                $_SESSION["username"] = $username;

                
                // Redirect user to welcome page
                header("location: prihlasenie.php");
            } 
            else {
                // Display an error message if password is not valid
                $password_err = "Wrong password.";
            }
        }
        else {
            // Display an error message if email doesn't exist
            $email_err = "Account does not exist.";
        }
    }
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>eshop - Login</title>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary"  value="Login">
            </div>
            <p>Don't have an account? <a href="registracia.php">Sign up.</a></p>
            <a href="/eshop">to shop</a>
        </form>
    </div>    
</body>
</html>