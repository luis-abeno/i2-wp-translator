document.addEventListener("DOMContentLoaded", function (event) {
  document.addEventListener("click", function (event) {
    if (event.target.matches('[id^="edit_"]')) {
      var editId = event.target.id;
      var transformedStr = editId.replace("edit_", "");
      var currentUrl = window.location.href;
      var newUrl = currentUrl + "&edit=" + transformedStr;
      window.location.href = newUrl;
    }
  });

  if (document.getElementById("cancel_edit")) {
    document.getElementById("cancel_edit").addEventListener("click", function () {
      window.location.href =
        "<?php echo home_url(); ?>/wp-admin/admin.php?page=i2-translation-settings";
    });
  }
});
