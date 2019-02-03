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
echo "<table border='1'>
    <tr>
    <th>C_ID</th>
    <th>AVG</th>
    <th>PRIO</th>
    </tr>";

            echo "<tr>";
$average_ratings = array();
$customer_rating = array();
$average_weight = array();
$capacity = array();

for($x = 1; $x <=200; $x++)
{
    $view = "SELECT AVG(rating) AS average FROM category_rating  WHERE customer_ID = $x";
    $result = $mysqli->query($view);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($average_ratings, $row['average']);
            echo "<td align='center'>" . $x . "</td>";
            echo "<td align='center'>" . (round ($row['average'],1)) . "</td>";
        }
    }
    $view2 = "SELECT COUNT(rating) AS count, customer_ID FROM category_rating WHERE customer_ID = $x AND  rating < " . $average_ratings[$x-1]; 
    $result2 = $mysqli->query($view2);
    if ($result2->num_rows > 0) {
        while($row2 = $result2->fetch_assoc()) {
            echo "<td align='center'>" . $row2['count'] . "</td>";
            echo "</tr>";
        }
    }

}

for($y = 1; $y < 200; $y++){

    $viewTrial = "SELECT category_rating.customer_ID, category_rating.category_ID, AVG(category_rating.rating) as average FROM category_rating, events WHERE category_rating.customer_ID = $y AND category_rating.category_ID = events.category_ID AND events.start_time = '20:00:00' ORDER BY category_rating.customer_ID";
    $resultv = $mysqli->query($viewTrial);
    if ($resultv->num_rows > 0){
        while($row3 = $resultv->fetch_assoc()){
            // if($row3['customer_ID'] == 2){
            $customer_rating[$row3['customer_ID']] = $row3['average'];
            
            
        }
    }
    
}
 
$weight = 0;
$priority_count = 0;
$priority = array();
$reset_check = 1;
$max_p = array();
for($z = 1; $z < 200; $z++){

    $viewRate = "SELECT category_rating.customer_ID, category_rating.category_ID, category_rating.rating  FROM category_rating, events WHERE category_rating.customer_ID = $z AND category_rating.category_ID = events.category_ID AND events.start_time = '20:00:00' ORDER BY category_rating.customer_ID";
    $result5 = $mysqli->query($viewRate);
    if($result5->num_rows > 0){
        while($row4 = $result5->fetch_assoc()){
            
            //if the customer ID is not the same as the previous data we had, that means we can reset the counter.
            if($reset_check != $row4['customer_ID']){
                $priority_count = 0;
            }
            
            if($row4['customer_ID'] == $z) {
                 
                //weight is equal to the average rating 
                $weight = ($row4['rating']); 
                //to check if there are values less than the ratings itself. If there are then add to counter to set
                //priority
                if($weight < $customer_rating[$z]){
                    echo "The rating ". $weight . " is less than  " . $customer_rating[$z] . " for customer ID: " . $z;
                    echo "<br>";
                    
                    //For the array index, representing customer ID, increase count if rating less than average is found.
                    $priority[$z] = ++$priority_count;
                }
            }
            //keep check of customer ID we are working with to avoid resetting the counter every time.
            $reset_check = $row4['customer_ID'];

        }
    }


}


$maximum_cust_rating = 0;

for($m = 1; $m < 200; $m++){


    $newView = "SELECT category_rating.customer_ID, category_rating.category_ID, MAX(category_rating.rating) AS max_rating FROM category_rating, events WHERE category_rating.customer_ID = $m AND category_rating.category_ID = events.category_ID AND events.start_time = '20:00:00' ORDER BY category_rating.customer_ID";
        $resultN = $mysqli->query($newView);
        if($resultN ->num_rows > 0){

            while($rowN = $resultN -> fetch_assoc()){
                
                $maximum_cust_rating = $rowN['max_rating'];
                echo "This is the max " .$rowN['max_rating'];
                
            
            

            }

        }
    }
print_r($customer_rating);
echo "<br>";
print_r($priority);

