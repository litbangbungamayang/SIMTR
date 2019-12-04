
loadNamaBahan();
loadNamaVendor();
$('#kode_transaksi').selectize({create: false, sortField: 'text'});

var txtKuantaBahan = $("#kuanta_bahan");
var lblSatuanBahan = $("#satuan_bahan");
var cbxNamaBahan = $("#nama_bahan");

txtKuantaBahan.bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9]/g,""));
  ($(this).val() != "") ? $(this).val(parseInt($(this).val()).toLocaleString()) : "";
});

function loadNamaBahan(){
  $.ajax({
    url: js_base_url + "Admin_bahan/getAllBahan",
    type: "GET",
    dataType: "json",
    success: function(response){
      cbxNamaBahan.selectize({
        valueField: "id_bahan",
        labelField: "nama_bahan",
        sortField: "nama_bahan",
        searchField: "nama_bahan",
        maxItems: 1,
        create: false,
        placeholder: "Pilih bahan",
        options: response,
        onChange: function(value){
          var selCbxNamaBahan = cbxNamaBahan.selectize()[0].selectize;
          lblSatuanBahan.val(selCbxNamaBahan.options[value]["satuan"]);
        }
      });
    }
  })
}

function loadNamaVendor(){
  $.ajax({
    url: js_base_url + "Admin_vendor/getAllVendor",
    type: "GET",
    dataType: "json",
    success: function(response){
      $("#nama_vendor").selectize({
        valueField: "id_vendor",
        labelField: "nama_vendor",
        sortField: "nama_vendor",
        searchField: "nama_vendor",
        maxItems: 1,
        create: false,
        placeholder: "Pilih vendor",
        options: response,
        onChange: function(value){
          console.log(value);
        }
      });
    }
  })
}
