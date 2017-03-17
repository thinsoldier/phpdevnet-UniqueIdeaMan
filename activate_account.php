<?php

// ... I'm not touching this one.

session_start();
require "conn.php";

    //Grab account activator's email and account activation code from account activation link's url.
       
if(!isset($_GET["email"], $_GET["member_registration_account_activation_code"]) === TRUE)
{
        echo "<script>alert('Invalid Email Address! Invalid Account Activation Link! This email is not registered! Try registering an account!')</script>";
    $conn->close();    
        header("location:register.php");
        exit();
}
else
{
        $confirmed_email = trim($_GET["email"]);
        $member_registration_account_activation_code = trim($_GET["member_registration_account_activation_code"]);
       
        $confirmed_email = mysqli_real_escape_string($conn,$confirmed_email);
        $member_registration_account_activation_code = mysqli_real_escape_string($conn,$member_registration_account_activation_code);
       
       
        //Check User's Username (against users tbl) if it has already been taken or not whilst User was in midst of activating his/her account.
   
    $query = "SELECT * FROM users WHERE Email = '".$confirmed_email."'";
    $result = mysqli_query($conn,$query);
        $numrows = mysqli_num_rows($result);
        if($numrows != 0)
    {  
        echo "<script>alert('That email '".$confirmed_email."' is already registered!')</script>";
                $conn->close();
                exit();
        }
        else
    {
        //Grab User details from table "pending_users". Search data with confirmed Email Address.
                       
                $query = "SELECT * FROM pending_users WHERE Email = '".$confirmed_email."'";
                $result = mysqli_query($conn,$query);
                $numrows = mysqli_num_rows($result);
                if($numrows = 0)
                {              
                        echo "<script>alert('Invalid Email Address! Invalid Account Activation Link! This email is not registered! Try registering an account!')</script>";
                        $conn->close();
                        exit();
                }
                else
                {
                    while($row = mysqli_fetch_assoc($result))
                    {    
                                $db_id = $row["Id"];
                                $db_username = $row["Username"];
                                $db_password = $row["Password"];
                                $db_email = $row["Email"];
                                $db_forename = $row["Forename"];
                                $db_surname = $row["Surname"];
                                $db_account_activation_code = $row["Account_Activation_Code"];
                                $db_account_activation = $row["Account_Activation"];               
           
                                if($db_account_activation != 0)
                                {
                                        echo "<script>alert('Since your account is already activated, why are you trying to activate it again ?')</script>";
                                        $conn->close();
                                        exit();
                                }
                                else
                                {
                                        $conn->query("UPDATE pending_users SET Account_Activation 1 WHERE Email = '".$confirmed_email."'");            
                            echo "Activating your account! Wait to be auto-logged-in to your account as that will be the sign that your account has been activated.";
                                        echo "Your email '".$confirmed_email."' has now been confirmed!";
                                    echo "Activating your account! Wait to be auto-logged-in to your account as that will be the sign that your account has been activated.";
               
               
                                        //Create table under $username to hold user account activity data.

                                        $sql = "CREATE TABLE $db_username (
                                        Id INT(6) UNSIGNED AUTO_INCREMENT, PRIMARY KEY
                                        Username varchar(30) NOT NULL,
                                        Email varchar(50) NOT NULL,
                                        Forename varchar(30) NOT NULL,
                                        Surname varchar(30) NOT NULL,
                                        Password varchar(32) NOT NULL,
                                        Profile_Pic (longblob) NOT NULL,
                                        Bio varchar(250) NOT NULL,
                                        Status varchar(100) NOT NULL)";
         
                                        if ($conn->query($sql) != TRUE)
                                        {
                                            echo "Error creating table: " . mysqli_error($conn);
                                                $conn->close();
                    }
                                        else
                                        {
                        echo "Table $db_username created successfully";
                                                                       
                       
                                                //Copy $user's registration data from table "pending_users" to table user.
       
                                                $sql = "INSERT INTO $db_username(Username,Password,Email,Forename,Surname,Account_Activation_Code) VALUES('$db_username','$db_password','$db_email','$db_forename','$db_surname','$db_account_activation_code')";

                                                if($conn->query($sql) != TRUE)
                                                {
                                                        echo "inserting data into table $db_username failed! " . mysqli_error($conn);
                                                        $conn->close();
                                                       
                                                }
                                                else
                                                {      
                                                        echo "inserted data into table $db_username!";
                                       
                               
                                                        //Copy $user's registration data from table "pending_users" to table users.
       
                                                        $sql = "INSERT INTO users (Username,Password,Email,Forename,Surname,Account_Activation_Code) VALUES('$db_username','$db_password','$db_email','$db_forename','$db_surname','$db_account_activation_code')";

                                                        if($conn->query($sql) != TRUE)
                                                        {
                                                                echo "inserting data into table users failed! " . mysqli_error($conn);
                                                                $conn->close();
                                                               
                                                        }
                                                        else
                                                        {      
                                                                echo "inserted data into table users!";
                                               
                                               
                                                                //Redirect newly activated user to his/her account homepage.
                                                               
                                                                $user = $db_username;
                                                                $userid = $db_id;
                                                                $_SESSION["user"] = $user;
                                                               
                                                                header("location: home.php");
                                                        }
                                                }      
                                        }      
                                }
                        }
                }
    }
}

?>