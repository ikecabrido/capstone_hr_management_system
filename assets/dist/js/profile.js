$(document).on("submit", "#passwordForm", function (e) {
  e.preventDefault();

  $.ajax({
    url: "../user_profile/update_user.php",
    method: "POST",
    data: $(this).serialize(),
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        $(document).Toasts("create", {
          class: "bg-success",
          title: "Success",
          body: response.message,
          autohide: true,
          delay: 3000,
        });

        $("#globalModal").modal("hide");
      } else {
        $(document).Toasts("create", {
          class: "bg-danger",
          title: "Error",
          body: response.message,
          autohide: true,
          delay: 3000,
        });
      }
    },
  });
});
