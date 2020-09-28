<?php
include "config.php";
session_start();
?>

<?php
// Create a New Trip
if (isset($_POST['createTrip'])) {
    $location = $_POST['location'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $passcode = $_POST['passcode'];

    $sql_t = "SELECT * FROM trip WHERE tripcode = '{$passcode}'";
    $trips = $mysqli->query($sql_t);
    
    if (mysqli_num_rows($trips) > 0 ) {
        $returnArray = array(
            "msg" => "Passcode already exists. Please use another one!"
        );
    } else {
        $sql_trip = 
        "INSERT INTO trip (tripcode,startdate,enddate,location)
        VALUES ('$passcode','$startDate','$endDate','$location')";
        $mysqli->query($sql_trip) or die($mysqli->error);
        $returnArray = array(
            "msg" => "You Successfully Created A Trip"
        );
    }
    echo json_encode($returnArray);
}
// Add a New Traveller
if (isset($_POST['name'])) {
    $user_name = $_POST['name'];
    $tripID = $_SESSION['tripID'];

    $sql_u = "SELECT * FROM traveller WHERE name = '{$user_name}' AND status=1 AND trip_id={$tripID}";
    $traveller = $mysqli->query($sql_u);

    if (mysqli_num_rows($traveller) > 0 ) {
        $returnArray = array(
            "status" => 0,
            "msg" => "Traveller already exists"
        );

    } else {
        $sql = 
        "INSERT INTO traveller (trip_id,name,status,payout,cashout,cashin)
        VALUES ('$tripID','$user_name',1,0,0,0)";
        $mysqli->query($sql) or die($mysqli->error);
        $last_id = mysqli_insert_id($mysqli);
        $returnArray = array(
            "status" => 1,
            "msg" => "Traveller Added Succesfully",
            "id" => $last_id,
            "tripId" => $tripID,
            "name" => $user_name
        );
    }
    echo json_encode($returnArray);
}

// Delete Traveller Request
if (isset($_POST['del_travellerID'])) {
    $travellerID = $_POST['del_travellerID'];

    $sql = "UPDATE traveller SET status=0 WHERE id = '{$travellerID}'";
    $mysqli->query($sql) or die($mysqli->error);

    $returnArray = array(
        "status" => 1,
        "msg" => "Traveller Deleted Succesfully"
    );
    echo json_encode($returnArray);
}

// Edit Traveller Request
if (isset($_POST['edit_travellerID'])) {
    $travellerID = $_POST['edit_travellerID'];
    $user_name = $_POST['new_name'];

    $sql_u = "SELECT * FROM traveller WHERE name = '{$user_name}' AND status=1";
    $traveller = $mysqli->query($sql_u);

    if (mysqli_num_rows($traveller) > 0 ) {
        $returnArray = array(
            "status" => 0,
            "msg" => "Traveller already exists"
        );

    } else {
        $sql = "UPDATE traveller SET name='{$user_name}' WHERE id = '{$travellerID}'";
        $mysqli->query($sql) or die($mysqli->error);

        $returnArray = array(
            "status" => 1,
            "msg" => "Traveller Edited Succesfully"
        );
    }
    echo json_encode($returnArray);
}

// Post Expense Request
if (isset($_POST['expense'])) {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $desc = $_POST['desc'];
    $amount = $_POST['amount'];
    $payer_id = $_POST['payer_id'];
    $excl_id_1 = ($_POST['excl_id_1']!=0) ? $_POST['excl_id_1'] : "NULL";
    $excl_id_2 = ($_POST['excl_id_2']!=0) ? $_POST['excl_id_2'] : "NULL";
    $excl_id_3 = ($_POST['excl_id_3']!=0) ? $_POST['excl_id_3'] : "NULL";

    $tripID = $_SESSION['tripID'];

    $sql = 
    "INSERT INTO expense (trip_id,status,date,categories_id,amount,traveller_id,description,exclude_1,exclude_2,exclude_3)
    VALUES ($tripID, 1, '$date', $category, $amount, $payer_id, '$desc', $excl_id_1, $excl_id_2, $excl_id_3)";
    $mysqli->query($sql) or die($mysqli->error);
    $last_id = mysqli_insert_id($mysqli);
    $returnArray = array(
        "status" => 1,
        "msg" => "Expenses Added Succesfully",
        "id" => $last_id
    );
    echo json_encode($returnArray);
}

// Get Current Expense Data Request
if (isset($_POST['getExpense'])) {
    $tripID = $_SESSION['tripID'];
    $expenseID = $_POST['expenseID'];

    $sql_expense =
    "SELECT *
    FROM expense 
    WHERE expense.trip_id= '{$_SESSION['tripID']}' AND expense.id = '{$expenseID}'
    ";
    $expense = $mysqli->query($sql_expense) or die($mysqli->error);
    $expense = $expense->fetch_assoc();
    $returnArray = array(
        "date" => $expense['date'],
        "categories_id" => $expense['categories_id'],
        "description" => $expense['description'],
        "amount" => $expense['amount'],
        "traveller_id" => $expense['traveller_id'],
        "exclude_1" => $expense['exclude_1'],
        "exclude_2" => $expense['exclude_2'],
        "exclude_3" => $expense['exclude_3']
    );
    echo json_encode($returnArray);
}

// Edit Current Expense Data Request
if (isset($_POST['editExpense'])) {
    $tripID = $_SESSION['tripID'];
    $expenseID = $_POST['expenseID'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    $desc = $_POST['desc'];
    $amount = $_POST['amount'];
    $payer_id = $_POST['payer_id'];
    $excl_id_1 = ($_POST['excl_id_1'] != 0) ? $_POST['excl_id_1'] : "NULL";
    $excl_id_2 = ($_POST['excl_id_2'] != 0) ? $_POST['excl_id_2'] : "NULL";
    $excl_id_3 = ($_POST['excl_id_3'] != 0) ? $_POST['excl_id_3'] : "NULL";

    $sql_edit_expense =
    "UPDATE expense
    SET date = '$date', categories_id = $category, description = '$desc', amount=$amount,
    traveller_id = $payer_id, exclude_1 = $excl_id_1, exclude_2 = $excl_id_2, exclude_3 = $excl_id_3 
    WHERE expense.trip_id= {$tripID} AND expense.id = {$expenseID}
    ";

    $mysqli->query($sql_edit_expense) or die($mysqli->error);

    $returnArray = array(
        "status" => 1,
        "msg" => "Expense Edited Succesfully",
        "sql" => $sql_edit_expense
    );
    echo json_encode($returnArray);
}

// Delete Current Expense Data Request
if (isset($_POST['deleteExpense'])) {
    $tripID = $_SESSION['tripID'];
    $expenseID = $_POST['expenseID'];

    $sql_delete_expense =
    "UPDATE expense
    SET status=0 
    WHERE expense.trip_id= {$tripID} AND expense.id = {$expenseID}
    ";

    $mysqli->query($sql_delete_expense) or die($mysqli->error);

    $returnArray = array(
        "status" => 1,
        "msg" => "Expense Deleted Succesfully"
    );
    echo json_encode($returnArray);
}

// Update Expenses Steps
if (isset($_POST['getTraveller'])) {
    $tripID = $_SESSION['tripID'];

    // Html for select travellers in Create a new expense
    $selectTravellers = "<option value=0 selected>Please Select</option>";
    $sql_travellers = 
    "SELECT id, name 
    FROM traveller 
    WHERE trip_id={$tripID} AND status=1";
    $travellers = $mysqli->query($sql_travellers) or die($mysqli->error);
    while ($traveller = $travellers->fetch_assoc()) {
        $selectTravellers = $selectTravellers . "<option value=" . $traveller['id'] . ">" . $traveller['name'] . "</option>";
    }

    // List of id of deleted travellers
    $deletedTravellers = array();
    $sql_deleted_travellers = 
    "SELECT id 
    FROM traveller 
    WHERE trip_id={$tripID} AND status=0";
    $deleted_travellers = $mysqli->query($sql_deleted_travellers) or die($mysqli->error);
    while ($deleted_traveller = $deleted_travellers->fetch_assoc()) {
        array_push($deletedTravellers, $deleted_traveller['id']);
    }
    // Update the expense list
    $sql_expenses =
    "SELECT *
    FROM expense 
    WHERE expense.trip_id= '{$_SESSION['tripID']}' AND expense.status = 1";
    $expenses = $mysqli->query($sql_expenses) or die($mysqli->error);
    while ($expense = $expenses->fetch_assoc()) {
        // If a current payer is deleted, delete the expense
        if (in_array($expense['traveller_id'], $deletedTravellers)) {
            $sql_delete_expense =
            "UPDATE expense
            SET status=0 
            WHERE expense.trip_id= {$tripID} AND expense.id = {$expense['id']}";
            $mysqli->query($sql_delete_expense) or die($mysqli->error);
        }

        // If a excluded traveller is deleted, set the id to NULL
        if (in_array($expense['exclude_1'], $deletedTravellers)) {
            $sql_delete_excl1 =
            "UPDATE expense
            SET exclude_1=NULL
            WHERE expense.trip_id= {$tripID} AND expense.id = {$expense['id']}";
            $mysqli->query($sql_delete_excl1) or die($mysqli->error);

        }
        if (in_array($expense['exclude_2'], $deletedTravellers)) {
            $sql_delete_excl2 =
            "UPDATE expense
            SET exclude_2=NULL
            WHERE expense.trip_id= {$tripID} AND expense.id = {$expense['id']}";
            $mysqli->query($sql_delete_excl2) or die($mysqli->error);

        }
        if (in_array($expense['exclude_3'], $deletedTravellers)) {
            $sql_delete_excl3 =
            "UPDATE expense
            SET exclude_3=NULL
            WHERE expense.trip_id= {$tripID} AND expense.id = {$expense['id']}";
            $mysqli->query($sql_delete_excl3) or die($mysqli->error);
        }
    }

    // Update the current expenses table
    $updateExpenses = "";
    $sql_updated_expenses =
        "SELECT expense.id, expense.trip_id, expense.status, expense.date, categories.cat_name,
    expense.amount, expense.description, traveller.name as payer_name, t1.name as excl_1_name,
    t2.name as excl_2_name, t3.name as excl_3_name
    FROM expense 
    LEFT JOIN categories ON expense.categories_id=categories.id 
    LEFT JOIN traveller ON expense.traveller_id=traveller.id 
    LEFT JOIN traveller t1 ON expense.exclude_1=t1.id 
    LEFT JOIN traveller t2 ON expense.exclude_2=t2.id 
    LEFT JOIN traveller t3 ON expense.exclude_3=t3.id 
    WHERE expense.trip_id= '{$_SESSION['tripID']}' AND expense.status=1";

    $updated_expenses = $mysqli->query($sql_updated_expenses) or die($mysqli->error);
    while ($expense = $updated_expenses->fetch_assoc()) {
        $updateExpenses = $updateExpenses . "
        <tr class=\"d-flex\" id=\"expRow_" . $expense['id'] . "\">
            <td class=\"col-2\">" . $expense['date'] . "</td>
            <td class=\"col-2\">" . $expense['cat_name'] . "</td>
            <td class=\"col-2\">" . $expense['description'] . "</td>
            <td class=\"col-1\">" . $expense['amount'] . "</td>
            <td class=\"col-1\">" . $expense['payer_name'] . "</td>
            <td class=\"col-1\" colspan=\"1\">" . $expense['excl_1_name'] . "</td>
            <td class=\"col-1\" colspan=\"1\">" . $expense['excl_2_name'] . "</td>
            <td class=\"col-1\" colspan=\"1\">" . $expense['excl_3_name'] . "</td>
            <td>
                <a onclick=\"expense_edit(" . $expense['id'] . ")\" class=\"btn btn-info\">Edit</a>
            </td>
        </tr>
        ";
    }

    $returnArray = array(
        "selectTravellers" => $selectTravellers,
        "updateExpenses" => $updateExpenses
    );
    echo json_encode($returnArray);
}

// Analyze Expenses
if (isset($_POST['ananlyzeExpenses'])) {
    $tripID = $_SESSION['tripID'];

    // Get total travellers
    $totalTrallvers = 0;
    $sql_travellers = 
    "SELECT *
    FROM traveller 
    WHERE trip_id={$tripID} AND status=1";
    $travellers = $mysqli->query($sql_travellers) or die($mysqli->error);
    $totalTrallvers = mysqli_num_rows($travellers);
    // Create an Array List of travellers
    $list = array();
    while ($traveller = $travellers->fetch_assoc()) {
        $trav = array(
            "id" => $traveller['id'],
            "payout" => 0,
            "expense" => 0,
            "cashout" => 0,
            "cashin" => 0
        );
        array_push($list, $trav);
    }

    // Calculate amount of each traveller for each expense
    // Select all active expenses
    $sql_expenses =
    "SELECT *
    FROM expense 
    WHERE expense.trip_id= '{$_SESSION['tripID']}' AND expense.status = 1";
    $expenses = $mysqli->query($sql_expenses) or die($mysqli->error);
    while ($expense = $expenses->fetch_assoc()) {
        $excl_1 = ($expense['exclude_1'] == NULL) ? 0 : 1;
        $excl_2 = ($expense['exclude_2'] == NULL) ? 0 : 1;
        $excl_3 = ($expense['exclude_3'] == NULL) ? 0 : 1;
        $expTotalTrav = $totalTrallvers - $excl_1 - $excl_2 - $excl_3;
        $amount = $expense['amount']/$expTotalTrav;

        // Update travellers payment
        for ($i=0; $i < count($list); $i++) {
            $travID = $list[$i]["id"];
            if ($travID == $expense['traveller_id']) {
                $list[$i]["payout"] = $list[$i]["payout"] + $expense['amount'];
            }
            if ($travID != $expense['exclude_1'] && $travID != $expense['exclude_2']
            && $travID != $expense['exclude_1']) {
                $list[$i]["expense"] = $list[$i]["expense"] + $amount;
            }
        }
    }
    for ($i=0; $i < count($list); $i++) {
        // echo " id=" . $list[$i]['id'] . " and " . $list[$i]['payout']. " and " . $list[$i]['expense'];
        if ($list[$i]['payout'] > $list[$i]['expense']) {
            $list[$i]['cashin'] = $list[$i]['payout'] - $list[$i]['expense'];
        } else {
            $list[$i]['cashout'] = $list[$i]['expense'] - $list[$i]['payout'];
        }
    }
    // Update traveller databse
    for ($i=0; $i < count($list); $i++) {
        $sql_update_travellers = 
        "UPDATE traveller 
        SET payout = {$list[$i]['payout']}, expense={$list[$i]['expense']}, cashout={$list[$i]['cashout']}, cashin={$list[$i]['cashin']}
        WHERE id = {$list[$i]['id']}";
        $mysqli->query($sql_update_travellers) or die($mysqli->error);
    }
}

// Print Expenses Report
if (isset($_POST['printReport'])) {
    $tripID = $_SESSION['tripID'];
    $returnHtml = "";

    $sql_travellers =
    "SELECT name, cast(expense as decimal(10,2)) as expense, cast(payout as decimal(10,2)) as payout,
    cast(cashin as decimal(10,2)) as cashin, cast(cashout as decimal(10,2)) as cashout
    FROM traveller 
    WHERE trip_id={$tripID} AND status=1";
    $travellers = $mysqli->query($sql_travellers) or die($mysqli->error);
    while ($traveller = $travellers->fetch_assoc()) {
        $returnHtml = $returnHtml . 
        "<tr>
            <td style=\"width: 20%\">" . $traveller['name'] . "</th>
            <td style=\"width: 20%\">$" . $traveller['expense'] . "</th>
            <td style=\"width: 20%\">$" . $traveller['payout'] . "</th>
            <td style=\"width: 20%\">$" . $traveller['cashin'] . "</th>
            <td style=\"width: 20%\">$" . $traveller['cashout'] . "</th>
        </tr>";
    }
    echo $returnHtml;
}

// Get Tripcode
if (isset($_POST['getTripcode'])) {
    echo $_SESSION['tripCode'];
}
$mysqli->close();
?>
