$(document).ready(function () {
    console.log("brands.js loaded");

    // Brand name validation function
    function validateBrandName($B_name, $B_nameErr) {
        let name = $B_name.val().trim();
        let namePattern = /^[A-Za-z -]+$/;
        let isValid = true;

        if (name === "") {
            $B_name.addClass("is-invalid");
            $B_nameErr.text("Name is required");
            isValid = false;
        } else if (name.length < 4) {
            $B_name.addClass("is-invalid");
            $B_nameErr.text("Must be at least 4 characters");
            isValid = false;
        } else if (!namePattern.test(name)) {
            $B_name.addClass("is-invalid");
            $B_nameErr.text("Only letters, hyphens, and spaces are allowed");
            isValid = false;
        } else {
            $B_name.removeClass("is-invalid");
            $B_nameErr.text("");
        }
        return isValid;
    }

    // ---
    // Add Form Submission (already using AJAX)
    // ---
    $("#addForm").on("submit", function (e) {
        e.preventDefault();

        let nameOk = validateBrandName($("#brandNameAdd"), $("#nameErrAdd"));

        if (!nameOk) {
            $("#msg").html(`
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Please clear the errors above and then proceed.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            `);
        } else {
            // AJAX request to submit the form
            $.ajax({
                url: $(this).attr("action"),
                type: $(this).attr("method"),
                data: $(this).serialize(),
                success: function (response) {
                    // Show success alert from the server
                    $("#msg").html(response); 
                    
                    // Wait 3 seconds, then refresh the page
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                },
                error: function (xhr, status, error) {
                    // Show error message
                    $("#msg").html(`
                        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            An error occurred. Please try again.
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    `);
                }
            });
        }
    });

    // ---
    // Update Form Submission (updated to use AJAX)
    // ---
    $("#updateForm").on("submit", function (e) {
        e.preventDefault();
        
        // Validate the brand name
        let nameOk = validateBrandName($("#brandNameUpdate"), $("#nameErrUpdate"));

        if (!nameOk) {
            $("#msg").html(`
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Please clear the errors above and then proceed.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            `);
        } else {
            // Use AJAX for the update action
            $.ajax({
                url: $(this).attr("action"),
                type: $(this).attr("method"),
                data: $(this).serialize(),
                success: function (response) {
                     // Check if the response contains a success message
                    if (response.includes("Successfully updated")) {
                        $("#msg").html(`
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                Updated successfully.
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        `);
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    } else {
                        // Handle server-side errors
                         $("#msg").html(`
                            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                ${response}
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        `);
                    }
                },
                error: function (xhr) {
                    $("#msg").html(`
                        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            An error occurred: ${xhr.responseText}.
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    `);
                }
            });
        }
    });
});
