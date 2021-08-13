var txtRafaksiLain = $("#ton_rafaksiLain");

txtRafaksiLain.bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9]/g,""));
  ($(this).val() != "") ? $(this).val(parseInt($(this).val()).toLocaleString()) : "";
});
