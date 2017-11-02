jQuery(document).ready(function($){

    $("#form_last_name").bind("propertychange change click keyup input paste", function (event) {
      validateForm();
  });
  $("#form_first_name").bind("propertychange change click keyup input paste", function (event) {
      validateForm();
  });
  $("#form_email_mail").bind("propertychange change click keyup input paste", function (event) {
      validateForm();
  });

  function validateEmail($email) {
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      return emailReg.test($email);
  };

  function validateForm() {
      if (($.trim($("#form_last_name").val()).length <= 0) && ($.trim($("#form_first_name").val()).length <= 0) && ($.trim($("#form_email_mail").val()).length <= 0)) {
          $("#err_msg_text").text("Please enter a first name, last name, or email ID to start searching");
          $("#form_submit_main").attr("disabled", true);
      } else if (!validateEmail($.trim($("#form_email_mail").val()))) {
          $("#err_msg_text").text("Not a valid email");
          $("#form_submit_main").attr("disabled", true);
      } else {
          $("#form_submit_main").attr("disabled", false);
          $("#err_msg_text").text("");
      }
  };

});
