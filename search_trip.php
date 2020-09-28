<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    session_start();
    include "config.php";
    $tripcode = $_GET['tripcode'];
    
    $sql = "SELECT * FROM trip WHERE tripcode = '$tripcode'";
    $results = $mysqli->query($sql);
    $result = $results->fetch_assoc();
    $_SESSION['tripID'] = $result['id'];
    $_SESSION['tripCode'] = $tripcode;

    if ($results->num_rows > 0) {
        $status = true;
        $sql_travellers =
        "SELECT id, name, cast(expense as decimal(10,2)) as expense, cast(payout as decimal(10,2)) as payout,
        cast(cashin as decimal(10,2)) as cashin, cast(cashout as decimal(10,2)) as cashout
        FROM traveller 
        WHERE trip_id={$_SESSION['tripID']} AND status=1";

        $sql_expenses =
        "SELECT expense.id, expense.trip_id, expense.status, expense.date, categories.cat_name,
        expense.amount, expense.description, traveller.name as payer_name, t1.name as excl_1_name,
        t2.name as excl_2_name, t3.name as excl_3_name, t4.name as excl_4_name 
        FROM expense 
        LEFT JOIN categories ON expense.categories_id=categories.id 
        LEFT JOIN traveller ON expense.traveller_id=traveller.id 
        LEFT JOIN traveller t1 ON expense.exclude_1=t1.id 
        LEFT JOIN traveller t2 ON expense.exclude_2=t2.id 
        LEFT JOIN traveller t3 ON expense.exclude_3=t3.id 
        LEFT JOIN traveller t4 ON expense.exclude_4=t4.id 
        WHERE expense.trip_id= '{$_SESSION['tripID']}' AND expense.status=1";
    } else {
        $status = false;
    }
    ?>
</head>

<body>
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <table class="table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th colspan="1">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($status) {
                        echo "
                        <tr>
                            <td>" . $result['location'] . "</td>
                            <td>" . $result['startdate'] . "</td>
                            <td>" . $result['enddate'] . "</td>
                            <td> <a href=\"edit_trip.php\" class=\"btn btn-info\">Edit</a> </td>
                        </tr>";
                    }
                    else {
                        echo "There is no result!";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if ($status) { ?>
        <h2>Trip Summary</h2>
        <h3>Expenses</h3>
        <div class="row justify-content-center">
            <div class="col-10">
                <table class="table" >
                    <thead>
                        <tr class="d-flex">
                            <th style="width: 15%">Date</th>
                            <th style="width: 15%">Category</th>
                            <th style="width: 20%">Description</th>
                            <th style="width: 10%">Amount</th>
                            <th style="width: 10%">Payer</th>
                            <th style="width: 30%" colspan="3">Excluding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $expenses = $mysqli->query($sql_expenses) or die($mysqli->error);
                        while ($expense = $expenses->fetch_assoc()) {
                            echo
                            "<tr class=\"d-flex\" id=\"expRow_" . $expense['id'] . "\">
                                <td style=\"width: 15%\">" . $expense['date'] . "</td>
                                <td style=\"width: 15%\">" . $expense['cat_name'] . "</td>
                                <td style=\"width: 20%\">" . $expense['description'] . "</td>
                                <td style=\"width: 10%\">" . $expense['amount'] . "</td>
                                <td style=\"width: 10%\">" . $expense['payer_name'] . "</td>
                                <td style=\"width: 10%\" colspan=\"1\">" . $expense['excl_1_name'] . "</td>
                                <td style=\"width: 10%\" colspan=\"1\">" . $expense['excl_2_name'] . "</td>
                                <td style=\"width: 10%\" colspan=\"1\">" . $expense['excl_3_name'] . "</td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>   
                </table>
            </div>
        </div>
        <h3>Travellers</h3>
        <div class="row justify-content-center">
            <div class="col-10">
                <table class="table" >
                    <thead>
                        <tr>
                            <th style="width: 20%">Name</th>
                            <th style="width: 20%">Trip Cost</th>
                            <th style="width: 20%">Total Payout</th>
                            <th style="width: 20%">Cash In</th>
                            <th style="width: 20%">Cash Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $travellers = $mysqli->query($sql_travellers) or die($mysqli->error);
                        while ($traveller = $travellers->fetch_assoc()) {
                            echo 
                            "<tr>
                                <td style=\"width: 20%\">" . $traveller['name'] . "</th>
                                <td style=\"width: 20%\">$" . $traveller['expense'] . "</th>
                                <td style=\"width: 20%\">$" . $traveller['payout'] . "</th>
                                <td style=\"width: 20%\">$" . $traveller['cashin'] . "</th>
                                <td style=\"width: 20%\">$" . $traveller['cashout'] . "</th>
                            </tr>";
                        }
                        ?>
                    </tbody>   
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
<?php
$mysqli->close();
?>

</html>