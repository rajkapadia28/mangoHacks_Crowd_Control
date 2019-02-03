<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Page Title</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="carnival.css" />
  <script src="main.js"></script>
</head>
<body>

<?php
ob_start();
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carnival_testing";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (mysqli_connect_error()) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "<div id='one'>
    <table id='t1'>
      <thead>
        <tr id='head'><th colspan='4'>TOTAL</th>
        <th></th>
        <th colspan='8'>TIME SLOT</th>
        </tr>
        <tr>
          <th id='c_id'>C_ID</th>
          <th id='name'>NAME</th>
          <th id='avg'>AVG</th>
          <th id='prio'>PRIORITY</th>
          <th id='rating'></th>
          <th id='t_avg' colspan='2'>12PM - 1PM</th>
          <th id='t_avg' colspan='2'>1PM - 2PM</th>
          <th id='t_avg' colspan='2'>7PM - 8PM</th>
          <th id='t_avg' colspan='2'>8PM - 9PM</th>
        </tr>
      </thead>
      <tbody>";


$average_ratings = array();
$rating12 = array();
$rating1 = array();
$rating7 = array();
$rating8 = array();

for($x = 1; $x <= 200; $x++)
{
  //****************************************************************************
  /* Display the customer's first name, last name, and ID *****************************************************************************/
    $view = "SELECT first_name, last_name FROM customers
    WHERE customer_ID = $x";
    $result = $mysqli->query($view);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $x . "</td>";
            echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        }
    }
  //****************************************************************************
  /* Display the total average rating per customer     *****************************************************************************/
    $view1 = "SELECT AVG(category_rating.rating) AS average FROM category_rating WHERE customer_ID = $x";
    $result1 = $mysqli->query($view1);
    if ($result1->num_rows > 0) {
        while($row1 = $result1->fetch_assoc()) {
            array_push($average_ratings, $row1['average']);
            echo "<td>" . (round ($row1['average'],1)) . "</td>";
        }
    }
  //****************************************************************************
  /* Display the total priority per customer *****************************************************************************/
    $view2 = "SELECT COUNT(rating) AS count, customer_ID FROM category_rating WHERE customer_ID = $x AND rating < " . $average_ratings[$x-1];
    $result2 = $mysqli->query($view2);
    if ($result2->num_rows > 0) {
        while($row2 = $result2->fetch_assoc()) {
            echo "<td>" . $row2['count'] . "</td>";
            echo "<td></td>";
        }
    }
  //****************************************************************************
  /* Display the average rating for 12PM time slot per customer *****************************************************************************/
    $view3 = "SELECT AVG(rating) AS average FROM category_rating, events WHERE events.start_time = '12:00:00' AND category_rating.customer_ID = $x AND  category_rating.category_ID = events.category_ID ORDER BY category_rating.customer_ID ASC, category_rating.category_ID ASC";
    $result3 = $mysqli->query($view3);
    if ($result3->num_rows > 0){
        while($row3 = $result3->fetch_assoc()){
            array_push($rating12, $row3['average']);
            echo "<td id='12avg'>" . (round ($row3['average'],1));
            echo "<td></td>";
        }
    }
  //****************************************************************************
  /* Display the average rating for 1PM time slot per customer *****************************************************************************/
    $view4 = "SELECT AVG(rating) AS average FROM category_rating, events WHERE events.start_time = '13:00:00' AND category_rating.customer_ID = $x AND  category_rating.category_ID = events.category_ID ORDER BY category_rating.customer_ID ASC, category_rating.category_ID ASC";
    $result4 = $mysqli->query($view4);
    if ($result4->num_rows > 0){
        while($row4 = $result4->fetch_assoc()){
            array_push($rating1, $row4['average']);
            echo "<td id='1avg'>" . (round ($row4['average'],1));
            echo "<td></td>";
        }
    }
  //****************************************************************************
  /* Display the average rating for 7PM time slot per customer *****************************************************************************/
    $view5 = "SELECT AVG(rating) AS average FROM category_rating, events WHERE events.start_time = '19:00:00' AND category_rating.customer_ID = $x AND  category_rating.category_ID = events.category_ID ORDER BY category_rating.customer_ID ASC, category_rating.category_ID ASC";
    $result5 = $mysqli->query($view5);
    if ($result5->num_rows > 0){
        while($row5 = $result5->fetch_assoc()){
            array_push($rating7, $row5['average']);
            echo "<td id='7avg'>" . (round ($row5['average'],1));
            echo "<td></td>";
        }
    }
  //****************************************************************************
  /* Display the average rating for 8PM time slot per customer *****************************************************************************/
    $view6 = "SELECT AVG(rating) AS average FROM category_rating, events WHERE events.start_time = '20:00:00' AND category_rating.customer_ID = $x AND  category_rating.category_ID = events.category_ID ORDER BY category_rating.customer_ID ASC, category_rating.category_ID ASC";
    $result6 = $mysqli->query($view6);
    if ($result6->num_rows > 0){
        while($row6 = $result6->fetch_assoc()){
            array_push($rating8, $row6['average']);
            echo "<td id='8avg'>" . (round ($row6['average'],1));
            echo "<td></td>";
        }
    }
    echo "</tr>";


    $weight = 0;
    $priority_count = 0;
    $priority = array();
    $reset_check = 1;
    $max_p = array();

  //****************************************************************************
  /* Prioritization Algorithm: Find the gap in customer preference for 8PM  *****************************************************************************/
    $view10 = "SELECT category_rating.customer_ID, category_rating.category_ID, category_rating.rating FROM category_rating, events WHERE category_rating.customer_ID = $x AND category_rating.category_ID = events.category_ID AND events.start_time = '20:00:00' ORDER BY category_rating.customer_ID";
    $result10 = $mysqli->query($view10);
    if($result10->num_rows > 0){
      while($row10 = $result10->fetch_assoc()){

            //if the customer ID is not the same as the previous data we had, that means we can reset the counter.
            if($reset_check != $row10['customer_ID']){
                $priority_count = 0;
            }

            if($row10['customer_ID'] == $x) {

                //weight is equal to the average rating
                $weight = ($row10['rating']);
                //to check if there are values less than the ratings itself. If there are then add to counter to set
                //priority
                if($weight < $rating8[$x -1]){
                    echo "The rating ". $weight . " is less than  " . $rating8[$x -1] . " for customer ID: " . $x;
                    echo "<br>";

                    //For the array index, representing customer ID, increase count if rating less than average is found.
                    $priority[$x] = ++$priority_count;
                }
            }
            //keep check of customer ID we are working with to avoid resetting the counter every time.
            $reset_check = $row10['customer_ID'];
        }
    }
  }
echo "</table></div>";

?>
</body>
</html>

