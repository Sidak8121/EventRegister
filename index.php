<?php

$errors = array(); //declare empty array to add errors too

//get name from post or set to NULL if doesn't exist
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$movie = $_POST['movie'] ?? null;
$country = $_POST['country'] ?? null;
$agree = $_POST['agree'] ?? null;


/*****************************************
 * Include library, make database connection,
 * and query for dropdown list information here
 ***********************************************/

include 'includes/library.php';
$pdo = connectDB();

$query = " SELECT movieId, name FROM oscar_ReportingResults order by name ASC";
$stmt = $pdo->query($query);

if(!$stmt)
    die("No Movies Available");

if (isset($_POST['submit'])) { //only do this code if the form has been submitted

    //validate user has entered a name
    if (!isset($name) || strlen($name) === 0) {
        $errors['name'] = true;
    }
    //validate and sanitize email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = true;
    }
    //check that the user chose a country
    if (empty($country)) {
        $errors['country'] = true;
    }
    if($movie==0){
        $errors['movie'] = true;
    }
    if(empty($agree)){
        $errors['agree'] = true;
    }

    if(count($errors)===0){

        /********************************************
         * Put the code to write to the database here
         ********************************************/

        $query = "INSERT into oscar_CompleteResults values (NULL, ?,?,?,?,?, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $email, $country, $agree, $movie]);

        $query = "UPDATE oscar_ReportingResults SET nomCount = nomCount + 1 WHERE movieId = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$movie]);


        header("Location: thanks.php");
    }


}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Academy Awards People&apos;s Choice Voting</title>
        <link rel="stylesheet" href="styles/master.css" />
    </head>
    <body>
       <?php include "includes/header.php";?>
        <main>
           <?php include "includes/nav.php";?>
            <section>
                <form id="voting" name="voting" method="post" novalidate>
                    <!-- email input -->
                    <div>
                        <label for="name">Name:</label>
                        <input type="name" name="name" id="name" value="<?=$name?>" required />
                         <span class="error <?=!isset($errors['name']) ? 'hidden' : "";?>">Please enter your name</span>
                    </div>
                    <!-- email input -->
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?=$email?>" required />
                        <span class="error <?=!isset($errors['email']) ? 'hidden' : "";?>">Please enter a correct email</span>
                    </div>
                    <!-- movie dropdown -->
                    <div>
                        <label for="movie">Movie Choice:</label>
                        <select name="movie" id="movie"  class="select-css">
                            <option value="0">Select One</option>
                              <!-- Put for loop for database results here. Use the one option left below as the template.  Replace the option value, and the comparisons value in the ternary operator (both of the "1"s) with a php echo of the database ID, and the contents of the option (Harry Potter...) with a echo of the database name  -->

                            <?php foreach ($stmt as $row): ?>
                                <option value="<?=$row['movieId']?>" <?=$movie == $row['movieId'] ? 'selected' : ''?>><?=$row['name']?></option>
                            <?php endforeach?>
                        </select>
                        <span class="error <?=!isset($errors['movie']) ? 'hidden' : "";?>">Please choose a movie</span>
                    </div>
                    <!-- Country radio buttons and fieldset -->
                    <fieldset>
                        <legend>Country</legend>
                        <div>
                            <input type="radio" name="country" id="Canada" value="Canada" <?=$country == "Canada" ? 'checked' : ''?>  />
                            <label for="Canada">Canada</label>
                        </div>
                        <div>
                            <input type="radio" name="country" id="US" value="US" <?=$country == "US" ? 'checked' : ''?>  />
                            <label for="US">US</label>
                        </div>
                        <span class="error <?=!isset($errors['country']) ? 'hidden' : "";?>">Please choose a country</span>
                    </fieldset>
                    <!-- Terms and Conditions checkbox -->
                    <div id="checkbox">
                        <input
                        type="checkbox"
                        name="agree"
                        id="agree"
                        <?=$agree == "Y" ? 'checked' : ''?> 
                        value="Y"
                        required
                        />
                        <label for="agree"
                        >Yes, I am 21 years of age or older, and I acknowledge having read
                        and accepted the <a href="rules.html">Official Contest Rules.</a>
                        </label>
                        
                    </div>
                    <span class="error <?=!isset($errors['agree']) ? 'hidden' : "";?>">You must agree to the terms</span>
                    
                    <!-- submit  button -->
                    <div id="buttons">
                        <button type="submit" name="submit">Submit Vote</button>
                    </div>
                </form>
            </section>
        </main>
       <?php include "includes/footer.php";?>
    </body>
</html>
