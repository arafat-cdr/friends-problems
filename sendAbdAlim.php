<?php
#---------------------------------------------------

$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "abdul_alim_problem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function pr($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
function dbSelectRaw($sql){
    global $conn;
    $res = $conn->query($sql);
    $data = array();
    // pr($sql);
    if($res) {
        while ($row = mysqli_fetch_object($res)) {
            $data[] = $row;
        }
        return $data;
    } else {
        return FALSE;
    }
}


#---------------------------------------------------

#---------------------------------
# Here Comes the Search Date
#---------------------------------

$date_1 = "2020-02-01";
$date_2 = "2020-02-30";

#---------------------------------
# End here
#---------------------------------


$policy = array();

$ques = dbSelectRaw("SELECT * FROM ques");

function convert_values_to_int($v) {
  return (int) $v;
}
if($ques){
    # Getting the ques id and running a query for that ques id
    # we will condition the start and end date also

    foreach($ques as $k => $v){
        $id = $v->id;
        $policy_arrays = dbSelectRaw("SELECT ques_1, ques_2, ques_3, ques_4, ques_5 FROM policy WHERE  (date BETWEEN '$date_1%' AND '$date_2%') AND (ques_1 = $id OR ques_2 = $id OR ques_3 = $id OR ques_4 = $id OR ques_5 = $id)");
        // pr($policy_arrays);
        // let's do some magic here ...
        if($policy_arrays){
            // we do not know the depth of the array
            // so we will use loop
            foreach($policy_arrays as $policy_array) {
                 // cast the data into array if it is object
                // beacause we need the data as array
                 $policy_array = (array) $policy_array;
                  // cast the value in integer
                  // with out integer it will throw error
                 $policy_array = (array_map("convert_values_to_int",$policy_array));
                 // pr($policy_array);
                 // Here we do the main thing ... for more search in phpdocs
                 // search it --> array_count_values()
                  $total_count = array_count_values($policy_array);
                  // pr($total_count);
                  // the data return by the query is not correct
                  // Here we calculating the correct ques id
                  if(isset($policy[$id])){
                      $policy[$id] += $total_count[$id];
                  }else{
                    $policy[$id] = $total_count[$id];
                  }
            }
        }
    }
    // pr($policy);
}

#-----------------------------------------
# print the data in a table
#-----------------------------------------
?>
<style>
#customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
<table id="customers">
    <tr>
       <th>Name</th>
       <th>Id</th>
       <th>Count</th>
       <th>Start Date</th>
       <th>End Date</th>
     </tr>
    <?php
        if($ques){
            foreach ($ques as $v) {
                echo "<tr>";
                echo "<td>".$v->name."</td>";
                echo "<td>".$v->id."</td>";
                if(isset($policy[$v->id])){
                    echo "<td>".$policy[$v->id]."</td>";
                }else{
                    echo "<td> 0 </td>";
                }
                echo "<td>".$date_1."</td>";
                echo "<td>".$date_2."</td>";
                echo "</tr>";
            }
        }
    ?>

</table>