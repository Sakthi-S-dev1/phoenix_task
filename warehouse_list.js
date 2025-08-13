$(document).ready(function () {
  // prefix validation
  $(document).on("input", ".prefix", function () {
    validatePrefix($(this));
  });

  // warehouse name validation
  $(document).on("input", ".warehouse", function () {
    validateWarehouse($(this));
  });

  // stock validation
  $(document).on("input", ".stock", function () {
    validateStock($(this));
  });

  function validatePrefix($input) {
    let prefix = $input.val().trim().toUpperCase();

    // Normalize and show uppercase in the field
    prefix = prefix.replace(/\s*-\s*/, "-");
    $input.val(prefix);

    const prefixPattern = /^[A-Z]{3}-\d{4}$/;
    let isValid = true;
    const $errorContainer = $input.closest(".col-md-4").find(".prefixErr");

    if (prefix === "") {
      $input.addClass("is-invalid");
      $errorContainer.text("Prefix is required");
      isValid = false;
    } else if (!prefixPattern.test(prefix)) {
      $input.addClass("is-invalid");
      $errorContainer.text("Format must be like ACT-1234 (3 letters, hyphen, 4 digits)");
      isValid = false;
    } else {
      $input.removeClass("is-invalid");
      $errorContainer.text("");
    }

    return isValid;
  }

  function validateWarehouse($input) {
    let warehouse = $input.val().trim();
    let warehousePattern = /^[A-Za-z ]+$/;
    let isValid = true;

    if (warehouse === "") {
      $input.addClass("is-invalid");
      $input.siblings(".invalid-feedback").text("Warehouse name is required");
      isValid = false;
    } else if (warehouse.length < 3) {
      $input.addClass("is-invalid");
      $input.siblings(".invalid-feedback").text("Must be at least 3 characters");
      isValid = false;
    } else if (!warehousePattern.test(warehouse)) {
      $input.addClass("is-invalid");
      $input.siblings(".invalid-feedback").text("Only letters and spaces allowed");
      isValid = false;
    } else {
      $input.removeClass("is-invalid");
      $input.siblings(".invalid-feedback").text("");
    }

    return isValid;
  }

  function validateStock($input) {
    let stock = $input.val().trim();
    let stockPattern = /^\d+$/;
    let isValid = true;

    if (stock === "") {
      $input.addClass("is-invalid");
      $input.siblings(".invalid-feedback").text("Stock is required");
      isValid = false;
    } else if (!stockPattern.test(stock)) {
      $input.addClass("is-invalid");
      $input.siblings(".invalid-feedback").text("Stock must be a number");
      isValid = false;
    } else {
      $input.removeClass("is-invalid");
      $input.siblings(".invalid-feedback").text("");
    }

    return isValid;
  }

  $("#warehouseEditForm").submit(function (e) {
    e.preventDefault();
    let allValid = true;

    const $form = $(this); // Only look inside the form being submitted

    $form.find(".prefix").each(function () {
      if (!validatePrefix($(this))) {
        allValid = false;
      }
    });

    $form.find(".warehouse").each(function () {
      if (!validateWarehouse($(this))) {
        allValid = false;
      }
    });

    $form.find(".stock").each(function () {
      if (!validateStock($(this))) {
        allValid = false;
      }
    });



    if (!allValid) {
      $("#message").html(`
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Please fix validation errors before update.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `);
      return;
    }

    $.ajax({
      url: "update_warehouse.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (response) {
        if (response.trim() === "success") {
          $("#message").html(`
              <div class='alert alert-success alert-dismissible fade show' role='alert'>
                 updated successfully
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>
            `);
        } else {
          $("#message").html(`
              <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Update failed
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>
            `);
        }
      },
      error: function () {
        alert("ajax err");
      }
    });
  });
});

