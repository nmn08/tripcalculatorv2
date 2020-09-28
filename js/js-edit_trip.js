$(document).ready(function() {
    // Jquery Steps Form Format
    $("#stepForm").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slide",
        onStepChanging: function (event, currentIndex, newIndex){
            // Reset Status
            $("status").html("");
            $("exp_status").html("");
            // Disallow to skip step 2
            if (currentIndex == 0 && newIndex == 2) {
                alert("Please go to review Expense Step!!!");
                return false;
            }
            // Update Expenses Step
            else if (currentIndex == 0) {
                $.ajax
                ({
                    url: 'processes.php',
                    type: 'post',
                    data:
                    {
                        getTraveller : 1
                    },
                    dataType:"json",
                    success: function(data){
                        $(".payer").html(data.selectTravellers);
                        $("#expenses tbody").html(data.updateExpenses)
                    }
                });
                return true;
            }
            // Allow to go to previous step
            if (currentIndex > newIndex)
            {
                return true;
            }
            // Analyze Expenses and print the summary report
            if (currentIndex == 1) {
                $.ajax
                ({
                    url: 'processes.php',
                    type: 'post',
                    data:
                    {
                        ananlyzeExpenses : 1
                    },
                    success: function(data){
                        console.log(data);
                    }
                });

                // Print the summary report
                $.ajax
                ({
                    url: 'processes.php',
                    type: 'post',
                    data:
                    {
                        printReport : 1
                    },
                    success: function(data){
                        $("#report tbody").html(data);
                    }
                });
                return true;
            }
        },
        onFinished: function (event, currentIndex)
        {
            $.ajax
            ({
                url: 'processes.php',
                type: 'post',
                data:
                {
                    getTripcode : 1
                },
                success: function(data){
                    var href = "/search_trip.php?tripcode=" + data;
                    console.log(href);
                    window.location.href= href;
                }
            });
        }
    });
     // Add Method to validate Select Object
    jQuery.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "Please Select Something");
    // Add Method to allow only letters, numbers and underscores
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^\w+$/i.test(value);
    }, "Please enter letters, numbers, and underscores only");

    // Validating Travellers Form/Step 1
    $("#travellersForm").validate({
        errorPlacement: function(error, element) {
            error.appendTo('.errorDiv');
        },
        rules:{
            name: {
                required: true,
                minlength: 2,
                alphanumeric: true
            }
        },
        submitHandler: function(form) {
            traveller_post();
            return false;
        }
    });

    // Validating Expenses Form/Step 1
    $(".expensesForm").validate({
        errorPlacement: function(error, element) {
            if (element.attr("name") == "date" )
                error.appendTo("#dateError");
            else if (element.attr("name") == "category" )
                error.appendTo("#categoryError");
            else if (element.attr("name") == "amount" )
                error.appendTo("#amountError");
            else if (element.attr("name") == "payer" )
                error.appendTo("#payerError");
        },
        rules:{
            date: {
                required: true,
                date: true
            },
            category: {
                required: true
            },
            amount: {
                required: true
            },
            payer: {
                valueNotEquals: "0"
            }
        },
        submitHandler: function(form) {
            expense_post();
            return false;
        }
    });
});

// Traveller Post Function
function traveller_post() {
    var name = $("#name").val();
    $.ajax
    ({
        url: 'processes.php',
        type: 'post',
        data:
        {
            name : name
        },
        dataType:"json",
        success: function(data){
            $("#status").html(data.msg);
            if (data.status == 1) {
                $("#name").html("");
                var newTextBoxRow = $(document.createElement('tr')).attr("id", 'row_' + data.id);
                var addContent =
                "<td>" + name + "</td>"+
                "<td><a onclick=\"del(" + data.id + ")\" class=\"btn btn-danger\">Delete</a></td>" +
                "<td><a onclick=\"edit(" + data.id + ",'" + data.name + "')\" class=\"btn btn-info\">Edit</a></td>";
                newTextBoxRow.append(addContent);
                newTextBoxRow.appendTo("#travellers");
            }
        }
    });
    // return false; 
}

function del(travellerID) {
    $('#delete_modal').modal();
    $("#btn_cancel_modal").on('click',function(e){
     e.preventDefault();
     $('#delete_modal').modal.model('hide');
    });
    $("#btn_delete_modal").on('click',function(e){
        $.ajax
        ({
            url: 'processes.php',
            type: 'post',
            data:
            {
                del_travellerID : travellerID
            },
            dataType:"json",
            success: function(data){
                $("#status").html(data.msg);
                if (data.status == 1) {
                    var rowID = "#row_" + travellerID;
                    $(rowID).remove();
                }
            }
        });
    });
}

function edit(travellerID, travellerName) {
    $('#edit_modal').modal('show');
    $("#edit_name").val(travellerName);
    // Click Cancel Button
    $("#cancel_modal_2").on('click',function(e){
        e.preventDefault();
        $('#edit_modal').modal('hide');
    });
    // Click Save Button
    $("#save_modal").on('click',function(e){
        var new_name = $("#edit_name").val();
        $.ajax
        ({
            url: 'processes.php',
            type: 'post',
            data:
            {
                edit_travellerID : travellerID,
                old_name: travellerName,
                new_name : new_name
            },
            dataType:"json",
            success: function(data){
                if (data.status == 0) {
                    $("#edit_status").html(data.msg);
                }
                else {
                    $("#status").html(data.msg);
                    var element = "td:contains(\"" + travellerName + "\")"
                    $(element).html(new_name);
                    $('#edit_modal').modal('hide');
                }
            }
        });
    });
}

// Expense Post Function
function expense_post() {
    var date = $("#date").val();
    var category = $("#category").val();
    var cat_name = $("#category option:selected").text();
    var desc = $("#desc").val();
    var amount = $("#amount").val();
    var payer_id = $("#payer").val();
    var payer_name = $("#payer option:selected").text();
    var excl_id_1 = $("#excl_1").val();
    var excl_name_1 = ($("#excl_1").val()==0) ? "" : $("#excl_1 option:selected").text();
    var excl_id_2 = $("#excl_2").val();
    var excl_name_2 = ($("#excl_2").val()==0) ? "" : $("#excl_2 option:selected").text();
    var excl_id_3 = $("#excl_3").val();
    var excl_name_3 = ($("#excl_3").val()==0) ? "" : $("#excl_3 option:selected").text();
    $.ajax
    ({
        url: 'processes.php',
        type: 'post',
        data:
        {
            expense : 1,
            date : date,
            category : category,
            desc : desc,
            amount : amount,
            payer_id : payer_id,
            excl_id_1 : excl_id_1,
            excl_id_2 : excl_id_2,
            excl_id_3 : excl_id_3,
        },
        dataType:"json",
        success: function(data){
            $("#exp_status").html(data.msg);
            var newTextBoxRow = $(document.createElement('tr')).attr({ "class": "d-flex", "id": 'expRow_' + data.id});
            var addContent = 
            "<td class=\"col-2\">" + date + "</td>"+
            "<td class=\"col-2\">" + cat_name + "</td>"+
            "<td class=\"col-2\">" + desc + "</td>"+
            "<td class=\"col-1\">" + amount + "</td>"+
            "<td class=\"col-1\">" + payer_name + "</td>"+
            "<td class=\"col-1\" colspan=\"1\">" + excl_name_1 + "</td>"+
            "<td class=\"col-1\" colspan=\"1\">" + excl_name_2 + "</td>"+
            "<td class=\"col-1\" colspan=\"1\">" + excl_name_3 + "</td>"+
            "<td><a onclick=\"expense_edit(" + data.id + ")\" class=\"btn btn-info\">Edit</a></td>";
            newTextBoxRow.append(addContent);
            newTextBoxRow.appendTo("#expenses");
        },
        error:function(data){
            console.log(data);
        }
    });
}

function expense_edit(expenseID) {
    $('#edit_exp_modal').modal('show');
    // Load current data from the database
    $.ajax
    ({
        url: 'processes.php',
        type: 'post',
        data:
        {
            getExpense: 1,
            expenseID: expenseID,
        },
        dataType: "json",
        success: function (data) {
            exclude_1 = (data.exclude_1 != null) ? data.exclude_1 : '';
            exclude_2 = (data.exclude_2 != null) ? data.exclude_2 : '';
            exclude_3 = (data.exclude_3 != null) ? data.exclude_3 : '';
            $("#edit_date").val(data.date);
            $("#edit_category").val(data.categories_id);
            $("#edit_desc").val(data.description);
            $("#edit_amount").val(data.amount);
            $("#edit_payer").val(data.traveller_id);
            $("#edit_excl_1").val(exclude_1);
            $("#edit_excl_2").val(exclude_2);
            $("#edit_excl_3").val(exclude_3);
        }
    });

    // When click Cancel Button
    $("#cancel_exp_modal").on('click',function(e){
        e.preventDefault();
        $('#edit_exp_modal').modal('hide');
    });

    // When click Save Button
    $("#save_exp_modal").on('click',function(e){
        var date = $("#edit_date").val();
        var category = $("#edit_category").val();
        var cat_name = $("#edit_category option:selected").text();
        var desc = $("#edit_desc").val();
        var amount = $("#edit_amount").val();
        var payer_id = $("#edit_payer").val();
        var payer_name = $("#edit_payer option:selected").text();
        var excl_id_1 = ($("#edit_excl_1").val() == null) ? 0 : $("#edit_excl_1").val();
        var excl_name_1 = ($("#edit_excl_1").val() == null) ? "" : $("#edit_excl_1 option:selected").text();
        var excl_id_2 = ($("#edit_excl_2").val() == null) ? 0 : $("#edit_excl_2").val();
        var excl_name_2 = ($("#edit_excl_2").val() == null) ? "" : $("#edit_excl_2 option:selected").text();
        var excl_id_3 = ($("#edit_excl_3").val() == null) ? 0 : $("#edit_excl_3").val();
        var excl_name_3 = ($("#edit_excl_3").val() == null) ? " " : $("#edit_excl_3 option:selected").text();        
        $.ajax
        ({
            url: 'processes.php',
            type: 'post',
            data:
            {
                editExpense: 1,
                expenseID: expenseID,
                date: date,
                category: category,
                desc: desc,
                amount: amount,
                payer_id: payer_id,
                excl_id_1: excl_id_1,
                excl_id_2: excl_id_2,
                excl_id_3: excl_id_3,
            },
            dataType: "json",
            success: function (data) {
                $("#exp_status").html(data.msg);
                var rowID = "#expRow_" + expenseID;
                $(rowID).html(
                "<td class=\"col-2\">" + date + "</td>" +
                "<td class=\"col-2\">" + cat_name + "</td>" +
                "<td class=\"col-2\">" + desc + "</td>" +
                "<td class=\"col-1\">" + amount + "</td>" +
                "<td class=\"col-1\">" + payer_name + "</td>" +
                "<td class=\"col-1\" colspan=\"1\">" + excl_name_1 + "</td>" +
                "<td class=\"col-1\" colspan=\"1\">" + excl_name_2 + "</td>" +
                "<td class=\"col-1\" colspan=\"1\">" + excl_name_3 + "</td>" +
                "<td><a onclick=\"expense_edit(" + expenseID + ")\" class=\"btn btn-info\">Edit</a></td>");
                $('#edit_exp_modal').modal('hide');
                console.log(data.sql);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    // When click Delete Button
    $("#delete_exp_modal").on('click', function () {
        $.ajax
        ({
            url: 'processes.php',
            type: 'post',
            data:
            {
                deleteExpense: 1,
                expenseID: expenseID,
            },
            dataType: "json",
            success: function (data) {
                $("#exp_status").html(data.msg);
                var rowID = "#expRow_" + expenseID;
                $(rowID).remove();
                $('#edit_exp_modal').modal('hide');
            }
        });
    });
}

