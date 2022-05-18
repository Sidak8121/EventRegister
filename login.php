<?php
 //PART 2
 //1. Getting username and password from $_POST array and saving them
 $user = $_POST['username'] ?? null;
 $userpass = $_POST['password'] ?? null;

 //2. declaring an empty array ($errors)
 $errors = array();
 
  if (isset($_POST['submit'])){
   //Including library
   include 'includes/library.php';
   //Making a database connection
   $pdo = connectDB();

   //building query to select data from oscar_AdminUsers table
   $query1 = "SELECT userid,username,userpass FROM oscar_AdminUsers WHERE username = ?";
   $stmtCheck = $pdo->prepare($query1);
   $stmtCheck->execute([$user]);
   $result=$stmtCheck->fetch(); //fetching the results
   
   //If the username entered by the user doesn't exist
   if(!$result){
    $errors['login'] = true; //login error is true
   }
   else{
      //verifying password
      if (password_verify($userpass, $result['userpass'])) {
        //starting the session
        session_start();
        
        //putting the username and userid into the session array
        $_SESSION['username'] = $user;
        $_SESSION['userid'] = $result['userid'];

        //redirecting to result.php page
        header("Location: results.php");
        exit();
      }
      else{
        //if password is not verified, error login will be true to print the error message in the html.
        $errors['login'] = true;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Academy Awards People&apos;s Choice Login</title>
    <link rel="stylesheet" href="styles/master.css" />
</head>
<body>
	 <?php include "includes/header.php";?>
   <!--header goes here -->
    <main>
      <!--nav goes here -->
      <?php include "includes/nav.php";?>
      <section>
         <form id="login" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" >
            <div>
              <label for="username">Username:</label> 
              <input type="text" id="username" name="username" size="40" value="<?php echo $user?>" />
            </div>
            <div>
              <label for="password">Password:</label> 
              <input type="password" id="password" name="password" size="40" />
            </div>
            <div>
            <span class="error <?=!isset($errors['login']) ? 'hidden' : "";?>">Your username or password was invalid</span>
            </div>
            <div id="checkbox">
               <input type="checkbox" name="remember" value="remember" />
              <label for="remember">Remember me</label>
            </div>
            <div id="buttons'">    
               <button type="submit" name="submit">Login</button>
            </div>
           </form>
      </section>
    </main>

  <!-- footer goes here- -->
  <?php include "includes/footer.php";?> 

</body>
</html>