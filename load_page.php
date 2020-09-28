<?php
    include "config.php";
    $tripID = $_POST["tripID"];
    $sql = "SELECT * 
    FROM trip 
    INNER JOIN traveller ON traveller.trip_id=$tripID";
    $result = $mysqli->query($sql) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) :
        echo "<tr>" .
        "<td>1</td>" .
        "<td>". $row['name'] . "</td>".
        "<td>".
            '<a href="edit_trip.php?tripcode='. $tripID .'&edit=' . $row['id'] . '" class="btn btn-info">Edit</a>'.
            '<a href="edit_trip.php?tripcode='. $tripID .'&delete=' . $row['id'] . '" class="btn btn-danger">Delete</a>'.
        "</td>";
    endwhile;
    echo "</tr>";
?>