<?php
require "conn.php";

//-----------------------------
// Functions
//-----------------------------

function postReturn( $key, $default="" )
{
	if( !empty( $_POST[ $key ] ) )
	{ 
		return htmlspecialchars( $_POST[ $key ], ENT_QUOTES );
	} else {
		return $default;
	}
}




//-----------------------------

$errors = array();

if  (isset($_POST['submit']))
{
    if(    !empty($_POST["member_registration_username"]) 
        && !empty($_POST["member_registration_password"])
        && !empty($_POST["member_registration_password_confirmation"])
        && !empty($_POST["member_registration_email"])
        && !empty($_POST["member_registration_email_confirmation"])
        && !empty($_POST["member_registration_forename"])
        && !empty($_POST["member_registration_surname"])
    )
    {
        $member_registration_account_activation = 0;
        $member_registration_random_numbers = random_int(0, 9999999999);
        
        $member_registration_username = trim($_POST["member_registration_username"]);
        $member_registration_forename = trim($_POST["member_registration_forename"]);
        $member_registration_surname  = trim($_POST["member_registration_surname"]);
        $member_registration_password = trim($_POST["member_registration_password"]);
        $member_registration_password_confirmation = trim($_POST["member_registration_password_confirmation"]);
        $member_registration_email    = trim($_POST["member_registration_email"]);
        $member_registration_email_confirmation    = trim($_POST["member_registration_email_confirmation"]);

        $member_registration_account_activation_code = trim("$member_registration_random_numbers");      

        $member_registration_username = mysqli_real_escape_string($conn,$_POST["member_registration_username"]);
        $member_registration_forename = mysqli_real_escape_string($conn,$_POST["member_registration_forename"]);
        $member_registration_surname  = mysqli_real_escape_string($conn,$_POST["member_registration_surname"]);
        $member_registration_password = mysqli_real_escape_string($conn,$_POST["member_registration_password"]);
        $member_registration_password_confirmation = mysqli_real_escape_string($conn,$_POST["member_registration_password_confirmation"]);
        $member_registration_email = mysqli_real_escape_string($conn,$_POST["member_registration_email"]);
        $member_registration_email_confirmation = mysqli_real_escape_string($conn,$_POST["member_registration_email_confirmation"]);       
        $member_registration_account_activation_code = mysqli_real_escape_string($conn,$member_registration_account_activation_code);    
               
        if($member_registration_email != $member_registration_email_confirmation)
        {
            $errors[] = "Your email inputs do not match! Try inputting again and then re-submit.";
        }

        if($member_registration_password != $member_registration_password_confirmation)
        {
            $errors[] = "Your password inputs do not match! Try inputting again and then re-submit.";
        }
        
        $sql_check_username_in_pending_users = "SELECT * FROM pending_users WHERE Username='".$member_registration_username."'";
        $result_username_in_pending_users = mysqli_query($conn,$sql_check_username_in_pending_users);
        if(mysqli_num_rows($result_username_in_pending_users)>0)
        {
            $errors[] = "That Username $member_registration_username is pending registration!";
        }
                       
        $sql_check_username_in_users = "SELECT * FROM users WHERE Username='".$member_registration_username."'";
        $result_username_in_users = mysqli_query($conn,$sql_check_username_in_users);
        if(mysqli_num_rows($result_username_in_users)>0)
        {
            $errors[] = "That Username $member_registration_username is already registered!";
        }

        $sql_check_email_in_pending_users = "SELECT * FROM pending_users WHERE Email='".$member_registration_email."'";
        $result_email_in_pending_users = mysqli_query($conn,$sql_check_email_in_pending_users);
        if(mysqli_num_rows($result_email_in_pending_users)>0)
        {
            $errors[] = "That Email $member_registration_email is pending registration!";
        }

        $sql_check_email_in_users = "SELECT * FROM users WHERE Email='".$member_registration_email."'";
        $result_email_in_users = mysqli_query($conn,$sql_check_email_in_users);
        if(mysqli_num_rows($result_email_in_users)>0)
        {
            $errors[] = "That Email $member_registration_email is already registered!";
        }

        $sql = "INSERT INTO pending_users(Username,Password,Email,Forename,Surname,Account_Activation_Code,Account_Activation) VALUES('".$member_registration_username."','".$member_registration_password."','".$member_registration_email."','".$member_registration_forename."','".$member_registration_surname."','".$member_registration_account_activation_code."','".$member_registration_account_activation."')";
        if($conn->query($sql)===TRUE)
        {
            echo "Data insertion into table success!";
        }
        else    
        {
            echo "Data insertion into table failure!";
            $conn->close();
            exit();
        }
       
        $to = "$member_registration_email";
        $subject = "loudgobs Browser Account Activation!";
        $body = "$member_registration_forename $member_registration_surname,\n\n You need to click the following link to confirm your email address and activate your account.\n\n\
        http://www.loudgobs.com/loudgobs-browse ... ation_code";
        $from = "admin_loudgobs-browser@loudgobs.com";
        $message = "from: $from";

        mail($to,$subject,$body,$message);
        
        echo '<h1 id="super big success message">Check your email for further instructions!</h1>';
        exit; // Don't allow the form to show again after successful signup.
    }
    else
    {
        $errors[] = "You must fill-in all input fields!";
    }
}

?><!DOCTYPE html>
<html>
<head>
<title>Signup Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<h2>
			Loud Gobs Browser Signup Form
		</h2>
		
<?
if( !empty( $errors ) )
{
	echo "<h3>There were problems with the information you provided:</h3>";
	// I usually have an arrayToList function for this next part.
	echo "<ul>";
	foreach( $errors as $e )
	{
		echo "<li>$e</li>";
	}
	echo "</ul>";
}
?>
		
		<form method="post" action="">
			<div class="form-group">
				<label for="username">Username:</label> 
				<input type="text" class="form-control" id="user" placeholder="Enter a unique Username" name="member_registration_username" value="<?=postReturn('member_registration_username')?>">
			</div>
			<div class="form-group">
				<label for="password">Password:</label> 
				<input type="password" class="form-control" id="pwd" placeholder="Enter new Password" name="member_registration_password">
			</div>
			<div class="form-group">
				<label for="password">Repeat Password:</label> 
				<input type="password" class="form-control" id="member_registration_repeat_pwd" placeholder="Repeat new Password" name="member_registration_password_confirmation">
			</div>
			<div class="form-group">
				<label for="forename">First Name:</label> 
				<input type="text" class="form-control" id="member_registration_first_name" placeholder="Enter your First Name" name="member_registration_forename" value="<?=postReturn('member_registration_forename')?>">
			</div>
			<div class="form-group">
				<label for="surname">Surname:</label> 
				<input type="text" class="form-control" id="member_registration_last_name" placeholder="Enter your Surname" name="member_registration_surname" value="<?=postReturn('member_registration_surname')?>">
			</div>
			<div class="form-group">
				<label for="email">Email:</label> 
				<input type="email" class="form-control" id="member_registration_email" placeholder="Enter your Email" name="member_registration_email" value="<?=postReturn('member_registration_email')?>">
			</div>
			<div class="form-group">
				<label for="email">Repeat Email:</label> 
				<input type="email" class="form-control" id="member_registration_repeat_email" placeholder="Repeat your Email" name="member_registration_email_confirmation">
			</div>
			<button type="submit" class="btn btn-default" name="submit">Register!</button> <b>Already have an account ?</b><br>
			<a href="login.php">Login here!</a> 
		</form>
	</div>
</body>
</html>