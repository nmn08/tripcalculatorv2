<!DOCTYPE html>
<html>

<head>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- jQuery Step-->
    <script src="js/jquery.steps.min.js"></script>
    <link href="styles/jquery-steps.css" rel="stylesheet">
    <!-- Jquery Validator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.js"></script>

    <!-- Bootstrap Javascript-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/jquery-steps.css">

    <link rel="stylesheet" href="styles/styles.css">
    <?php
    session_start();
    include "config.php";
    if ($_SESSION['status']) {
        //Traveller Data
        $sql_travellers = 
        "SELECT * 
        FROM traveller 
        WHERE trip_id= '{$_SESSION['tripID']}' AND status=1";


        // Categories Data
        $sql_categories = "SELECT * 
        FROM categories";
    }
    ?>

</head>

<body>
    <?php
    if ($_SESSION['status']==false) {
        header("Location: index.php");
    } else { ?>
    <div class="container" id="stepForm">
        <!-- Tab 1: Travellers Information -->
        <h3>Travellers Information</h3>
        <section>
            <div class="form-group mx-auto">
                <form  id="travellersForm" method="post">
                    <div class="form-row text-center justify-content-center">
                        <label class="col-sm-2 col-form-label" for="name">Name</label>
                        <input class="form-control col-sm-4" id="name" name="name" type="text">
                    </div>
                    <div class="form-row text-center justify-content-center errorDiv"></div>
                    <div class="form-row text-center justify-content-center">
                        <input type="submit" id="submit" class="btn btn-success"></input>
                    </div>
                </form>
                <div class="d-flex justify-content-center">
                    <p id="status"> </p>
                </div>
            </div>
            <div class="container-fluid">
                <table class="table" id="travellers">
                    <thead>
                        <tr>
                            <th colspan="1">Name</th>
                            <th colspan="1">Delete</th>
                            <th colspan="1">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $travellers = $mysqli->query($sql_travellers) or die($mysqli->error);
                        while ($traveller = $travellers->fetch_assoc()) {
                            echo "
                            <tr id=\"row_" . $traveller['id'] . "\">
                                <td>" . $traveller['name'] . "</td>
                                <td>
                                    <a onclick=\"del(" . $traveller['id'] . ")\" class=\"btn btn-danger\">Delete</a>
                                </td>
                                <td>
                                    <a onclick=\"edit(" . $traveller['id'] . ",'" . $traveller['name'] . "')\" class=\"btn btn-info\">Edit</a>
                                </td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Tab 2: Expenses -->
        <h3>Expenses</h3>
        <section>
            <div class="form-group mx-auto">
                <form class="expensesForm" method="post">
                    <div class="form-row text-center">
                        <label class="col-sm-2 col-form-label" for="date">Date</label>
                        <input class="form-control col-sm-4" type="date" id="date" name="date" placeholder="mm/dd/yyyy">
                        <label class="col-sm-2 col-form-label" for="category">Category</label>
                        <select class="form-control col-sm-4" name="category" id="category">
                            <option value="0" selected disabled>Please Select</option>
                            <?php
                            $categories = $mysqli->query($sql_categories) or die($mysqli->error);
                            while ($category = $categories->fetch_assoc()) {
                                echo "<option value=" . $category['id'] . ">" . $category['cat_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-row text-center">
                        <div class="col-sm-6" id="dateError"></div>
                        <div class="col-sm-6" id="categoryError"></div>
                    </div>
                    <div class="form-row text-center">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <input class="form-control col-sm-4" id="desc" name="desc" type="text">
                        <label class="col-sm-2 col-form-label" for="amount">Amount</label>
                        <input class="form-control col-sm-4" type="number" id="amount" name="amount" placeholder="0.00" min="0.00">
                    </div>
                    <div class="form-row text-center">
                        <div class="col-sm-6" id="descError"></div>
                        <div class="col-sm-6" id="amountError"></div>
                    </div>
                    <div class="form-row text-center">
                        <label class="col-sm-2 col-form-label" for="payer">Payer</label>
                        <select class="form-control col-sm-2 payer" id="payer" name="payer">
                        </select>
                        <?php
                        echo "<label class=\"col-sm-2 col-form-label\" for=\"exclude\">Not Split To</label>";
                        for ($i = 1; $i <= 3; $i = $i + 1) {
                            echo "<select class=\"form-control col-sm-2 payer\" id=\"excl_" . $i . "\">";
                            echo "</select> ";
                        }
                        ?>
                    </div>
                    <div class="form-row text-center">
                        <div class="col-sm-4" id="payerError"></div>
                    </div>
                    <div class="form-row text-center justify-content-center">
                        <input type="submit" id="exp_submit" class="btn btn-success"></input>
                    </div>
                </form>
                <div class="d-flex justify-content-center">
                    <p id="exp_status"></p>
                </div>
            </div>
            <div class="container-fluid">
                <table id="expenses" class="table">
                    <thead>
                        <tr class="d-flex">
                            <th class="col-2">Date</th>
                            <th class="col-2">Category</th>
                            <th class="col-2">Description</th>
                            <th class="col-1">Amount</th>
                            <th class="col-1">Payer</th>
                            <th class="col-3" colspan="3">Not Split To</th>
                            <th class="col-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Tab 3: Trip Summary -->
        <h3>Trip Summary</h3>
        <section>
            <div class="row justify-content-center">
                <div class="col-10">
                    <table id="report" class="table">
                        <thead>
                            <tr>
                                <th style="width: 20%">Name</th>
                                <th style="width: 20%">Trip Cost</th>
                                <th style="width: 20%">Total Payout</th>
                                <th style="width: 20%">Owes You</th>
                                <th style="width: 20%">You Owe</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <?php } ?>
</body>
<!-- Modal -->
<!-- Delete Traveller Modal -->
<div id="delete_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-body">
        <p>Are you sure?</p>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_delete_modal">Delete</button>
        <button type="button" data-dismiss="modal" class="btn btn-info" id="btn_cancel_modal">Cancel</button>
    </div>
</div>

<!-- Edit Traveller Modal -->
<div id="edit_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-body">
        <label for="name">Name</label>
        <input class="mb-3 form-control" id="edit_name" name="name" type="text">
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-success" id="save_modal">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-info" id="cancel_modal_2">Cancel</button>
        <p id="edit_status"></p>
    </div>
</div>

<!-- Edit Traveller Modal -->
<div id="edit_exp_modal" class="modal" tabindex="-1" role="dialog">
    <div class="container modal-body">
        <div class="row text-center">
            <label class="col-sm-2 col-form-label" for="date">Date</label>
            <input class="form-control col-sm-4" type="date" id="edit_date" name="date" placeholder="mm/dd/yyyy">
            <label class="col-sm-2 col-form-label" for="category">Category</label>
            <select class="form-control col-sm-4" id="edit_category" name="category">
                <option value="0" selected disabled>Please Select</option>
                <?php
                $categories = $mysqli->query($sql_categories) or die($mysqli->error);
                while ($category = $categories->fetch_assoc()) {
                    echo "<option value=" . $category['id'] . ">" . $category['cat_name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="row text-center">
            <label class="col-sm-2 col-form-label">Description</label>
            <input class="form-control col-sm-4" id="edit_desc" type="text" name="desc">
            <label class="col-sm-2 col-form-label" for="amount">Amount</label>
            <input class="form-control col-sm-4" type="number" id="edit_amount" name="amount" placeholder="0.00" min="0.00">
        </div>
        <div class="row text-center">
            <label class="col-sm-2 col-form-label" for="payer">Payer</label>
            <select class="form-control col-sm-2 payer" id="edit_payer" name="payer">
            </select>
            <?php
            echo "<label class=\"col-sm-2 col-form-label\" for=\"exclude\">Not Split To</label>";
            for ($i = 1; $i <= 3; $i = $i + 1) {
                echo "<select class=\"form-control col-sm-2 payer\" id=\"edit_excl_" . $i . "\">";
                echo "</select> ";
            }
            ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-success" id="save_exp_modal">Save</button>
        <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete_exp_modal">Delete</button>
        <button type="button" data-dismiss="modal" class="btn btn-info" id="cancel_exp_modal">Cancel</button>
        <p id="edit_exp_status"></p>
    </div>
</div>

<!-- Custom script -->
<script type="text/javascript" src="js/js-edit_trip.js"></script>

<?php
$mysqli->close();
?>

</html>
