jQuery(document).ready(function () {
  // Get the 'lang' parameter from the URL
  var urlParams = new URLSearchParams(window.location.search);
  var langParam = urlParams.get("lang");

  // Set the selected option based on the 'lang' parameter
  if (langParam) {
    $("#language-selector").val(langParam);
  }

  $(".language-selector li").click(function () {
    var selectedLanguage = $(this).data("lang");
    // Get the current URL
    var currentUrl = window.location.href;

    // Define the new lang parameter value
    var newLangParam = selectedLanguage; // Replace with 'en' or another value as needed

    // Create a regular expression to match the lang parameter
    var langParamRegex = /[?&]lang=[^&]*/;

    // Check if the lang parameter already exists in the URL
    if (langParamRegex.test(currentUrl)) {
      // If it exists, replace it with the new value
      var updatedUrl = currentUrl.replace(langParamRegex, "?lang=" + newLangParam);
    } else {
      // If it doesn't exist, append the new lang parameter
      updatedUrl =
        currentUrl + (currentUrl.indexOf("?") !== -1 ? "&" : "?") + "lang=" + newLangParam;
    }

    // Reload the page with the updated URL
    window.location.href = updatedUrl;
  });

  $(".current-lang").click(function (e) {
    e.stopPropagation();
    if (!$(".language-selector").hasClass("showLang")) {
      $(".language-selector").addClass("showLang");
      $(".language-selector").css("display", "block");
    }
  });

  $("body").click(function (e) {
    e.stopPropagation();
    if ($(".language-selector").hasClass("showLang")) {
      $(".language-selector").removeClass("showLang");
      $(".language-selector").css("display", "none");
    }
  });
});
