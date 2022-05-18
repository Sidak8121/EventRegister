<?php 

//Part 1: checking user loggedin here.
//Starting the session
session_start();

//Checking session is set. if session is not set, it will redirect to the login page
if(!isset($_SESSION['username'])){
  header("Location:login.php");
  exit();
}

  //create database connection
  include 'includes/library.php';
  $pdo =connectDB();
  //run query to get results for display
  $stmt=$pdo->query("select name, nomCount from oscar_ReportingResults order by nomCount DESC");
  //deal with possibilty of no results
  if(!$stmt)
    die("Database pull did not return data");
   
    //query for total to simplify table foot.
  $stmt2=$pdo->query("select sum(nomCount) as total from oscar_ReportingResults");
  $total=$stmt2->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Academy Awards People&apos;s Choice Secret Results</title>
    <link rel="stylesheet" href="styles/master.css" />
</head>
<body>
	 <?php include "includes/header.php";?>
   <!--header goes here -->
    <main>
      <!--nav goes here -->
      <?php include "includes/nav.php";?>
      <section>
       <div id="tablewrap">
          	<table id="results" cellspacing="0">
              	<thead>
                	<tr>
                    <th>Movie</th>
                    <th>Number of Votes</th>
                	</tr>
                </thead>
                <tfoot>
                  <tr>
                  	<td>Total Votes</td>
                    <td><?php echo $total //output total ?></td>
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach ($stmt as $row):   //loop through result set ?>
                      <tr>
                      	<td><?php echo $row['name']  //output prof name ?></td>
                        <td><?php echo $row['nomCount']  //output number of votes ?></td>
                      </tr>
                       
            			<?php endforeach; ?>
                </tbody>
             	</table>
          </div>
      </section>
    </main>
  
  <!-- footer goes here- -->
  <?php include "includes/footer.php";?>
    
</body>
</html>