<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <div class="container login-container">
        <div class="row">
            <div class="col-md-6 login-form-1">
                <h3>Create A New Trip</h3>
                <form method="post" id="createForm">
                    <div class="form-group">
                        <input type="text" class="form-control" id="location" name="location" placeholder="Location *" value="" />
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" id="startDate" name="startDate" placeholder="Starting Date" value="" />
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" id="endDate" name="endDate" placeholder="Ending Date" value="" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="passcode" name="passcode" placeholder="Passcode *" value="" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btnSubmit" value="Create" />
                    </div>
                    <div class="form-group">
                        <p style="color:white" id="createStatus"> </p>
                    </div>
                </form>
            </div>
            <div class="col-md-6 login-form-2">
                <h3>Already Have a Passcode</h3>
                <form action="search_trip.php" id="searchTrip">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Trip Passcode" name="tripcode" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btnSubmit" value="Search" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

<!-- Custom script -->
<script type="text/javascript" src="js/js-login.js"></script>
</html>
