// Traveller Post Function
$(document).ready(function() {
    // Jquery Validator Add Method to allow only letters, numbers and underscores
    jQuery.validator.addMethod("nameLoc", function(value, element) {
        return this.optional(element) || /^[a-z][a-z\s]*$/i.test(value);
    }, "Please enter letters and spaces only");
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^\w+$/i.test(value);
    }, "Please enter letters, numbers, and underscores only");
    // Validate and Submit Create e New Trip Form
    $("#createForm").validate({
        errorElement: 'div',
        rules:{
            location: {
                required: true,
                minlength: 2,
                nameLoc: true
            },
            startDate: {
                required: true,
                date:true
            },
            endDate: {
                required: true,
                date:true
            },
            passcode: {
                required: true,
                minlength: 2,
                alphanumeric: true
            }
        },
        submitHandler: function(form) {
            var location = $("#location").val();
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();
            var passcode = $("#passcode").val();
            $.ajax
            ({
                url: 'processes.php',
                type: 'post',
                data:
                {
                    createTrip:1,
                    location:location,
                    startDate:startDate,
                    endDate:endDate,
                    passcode:passcode
                },
                dataType:"json",
                success: function(data){
                    $("#createStatus").html(data.msg);
                }
            });
        }
    });

    // Validate Search a Trip Form
    $("#searchTrip").validate({
        errorElement: 'div',
        rules:{
            tripcode: {
                required: true,
                minlength: 2,
                alphanumeric: true
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});
