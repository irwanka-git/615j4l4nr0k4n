$(function () {
    $('.numeric').mask('0#');
    $('.decimal2').mask("#.##0,00", {reverse: true});
    $('[data-toggle="popover"]').popover();

    $('.koordinat').inputmask("decimal", { min: -180, max: 180, allowMinus: true });
});


jQuery.extend(jQuery.validator.messages, {
    required: "Kolom Ini Harus Diisi!.",
    remote: "Please fix this field.",
    email: "Masukan Alamat Email yg Valid.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Please enter a valid number.",
    digits: "Please enter only digits.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    minlength: jQuery.validator.format("Please enter at least {0} characters."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});

var showNotify = function($title, $message){
    swal($title, $message, "success");

}

var showAlert = function($title, $message){
    swal($title, $message, "error");
}

var disableButton = function ($btn){
    $($btn).attr('disabled','disabled');
}

var enableButton = function ($btn){
    $($btn).removeAttr('disabled');
}

 

var successNotify = function ($message){
    var message = $message;
    var type = "success";
    var duration = 3500;
    var ripple = false;
    var dismissible = true;
    var positionX = "right";
    var positionY = "top";
    window.notyf.open({
      type,
      message,
      duration,
      ripple,
      dismissible,
      position: {
        x: positionX,
        y: positionY
      }
    });
}

var errorNotify = function ($message){
    var message = $message;
    var type = "error";
    var duration = 3500;
    var ripple = false;
    var dismissible = true;
    var positionX = "right";
    var positionY = "top";
    window.notyf.open({
      type,
      message,
      duration,
      ripple,
      dismissible,
      position: {
        x: positionX,
        y: positionY
      }
    });
}
 