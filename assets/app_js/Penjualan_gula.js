var frm_basteb = $("#frm_basteb");
var frm_penjualanGula = $("#frm_penjualanGula");
var dialogAddPenjualan = $("#dialogAddPenjualan");
var arr_input_gula = [];
var arr_gula_90 = [];
var arr_kuota_gula = [];
var harga_jual = $("#harga_jual");
var tblListBasteb_filter = $("#tblListBasteb_filter");
var $cbxPembeli, cbxPembeli;
var dialogAddPembeli = $("#dialogAddPembeli");
var txtNamaPembeli = $("#txtNamaPembeli");
var txtAlamat = $("#txtAlamat");
var txtNoIdentitas = $("#txtNoIdentitas");
var frm_addPembeli = $("#formAddPembeli");

function formatTgl(dateObj){
  if(dateObj != null){
    return dateObj.getFullYear() + "-" + ("0" + (dateObj.getMonth()+1)) + "-" + ("0" + dateObj.getDate()).slice(-2);
  }
  return "";
}

function formatTglStr(dateObj){
  if(dateObj != null){
    return ("0" + dateObj.getDate()).slice(-2) + "-" + ("0" + (dateObj.getMonth()+1)) + "-" + dateObj.getFullYear();
  }
  return "";
}

function resetAddPembeli(){
  txtNamaPembeli.val('');
  txtAlamat.val('');
  txtNoIdentitas.val('');
}

dialogAddPembeli.on("hide.bs.modal", function(){
  resetAddPembeli();
});

function addPembeli(){
  dialogAddPembeli.modal("toggle");
}

function simpanPembeli(){
  if(txtNamaPembeli.val() != '' && txtNoIdentitas.val() != '' && txtAlamat.val() != ''){
    $.ajax({
      url: js_base_url + "Penjualan_gula/addPembeli",
      type: "POST",
      dataType: "json",
      data: frm_addPembeli.serialize(),
      success: function(response){
        alert("Data berhasil disimpan!");
        dialogAddPembeli.modal("toggle");
        cbxPembeli.destroy();
        refreshNamaPembeli();
      }
    });
  }
}

function refreshNamaPembeli(){
  $.ajax({
    url: js_base_url + "Penjualan_gula/getAllPembeli",
    type: "GET",
    dataType: "json",
    success: function(response){
      $cbxPembeli = $("#txt_pembeli").selectize({
        valueField: "id_pembeli",
        labelField: "nama",
        sortField: "nama",
        searchField: "nama",
        maxItems: 1,
        create: false,
        placeholder: "Pilih calon pembeli",
        options: response
      });
      cbxPembeli = $cbxPembeli[0].selectize;
    }
  });
}

function cekInput(arg){
  var index_input = arg.id.substr(11,arg.id.length-11);
  var input_text = $("#"+arg.id);
  var val_textbox = input_text.val();
  val_textbox = parseInt(val_textbox.replace(/[^0-9.]/,""));
  (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
  if(val_textbox > arr_gula_90[index_input]){
    input_text.val(arr_gula_90[index_input])
  } else {
    input_text.val(val_textbox);
  }
}

function cekSatuan(arg){
  var index_input = arg.id.substr(11,arg.id.length-11);
  var input_text = $("#"+arg.id);
  var val_textbox = input_text.val();
  val_textbox = parseInt(val_textbox.replace(/[^0-9.]/,""));
  (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
  if(val_textbox > arr_kuota_gula[index_input]){
    input_text.val(arr_kuota_gula[index_input])
  } else {
    if((val_textbox % 50) == 0){
      input_text.val(val_textbox);
    } else {
      alert("Kuanta harus sesuai dengan satuan karung!");
      input_text.val(0);
    }
  }
}

harga_jual.keyup(function(){
  koreksiDesimal(harga_jual);
})

function koreksiDesimal(textBox){
  var val_textbox = textBox.val();
  val_textbox = parseInt(val_textbox.replace(/[^0-9.]/,""));
  (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
  textBox.val(val_textbox);
}

function konfirmasiPenjualanGula(){
  if(parseInt($("#tahun_giling").val()) != 0){
    $("#tblListBasteb").DataTable().search('').draw();
    frm_basteb.submit();
  } else {
    alert("Anda belum memilih tahun giling!");
  }
}

function getFormValues(){
  var inputs = document.getElementsByTagName('input');
  var input = [];
  arr_input_gula = [];
  arr_kuota_gula = [];
  arr_gula_90 = [];
  for(var i = 0; i < inputs.length; i++){
    input = inputs[i];
    if(input.id.substr(0,11) == 'input_gula_'){
      arr_input_gula.push(input.value);
    }
    if(input.id.substr(0,11) == 'kuota_gula_'){
      arr_kuota_gula.push(input.value);
    }
  }
  var gula_90 = document.getElementsByTagName('th');
  var el_gula_90 = [];
  for(var j = 0; j < gula_90.length; j++){
    el_gula_90 = gula_90[j];
    if(el_gula_90.id.substr(0,11) == 'kg_gula_90_'){
      arr_gula_90.push(el_gula_90.innerHTML);
    }
  }
  /*
  console.log(arr_input_gula);
  console.log(arr_gula_90);
  console.log(arr_kuota_gula);
  */
}

function simpanPenjualanGula(){
  console.log(cbxPembeli.getValue());
  getFormValues();
  var fail = 0;
  for(var i = 0; i < arr_input_gula.length; i++){
    if(isNaN(parseInt(arr_input_gula[i]))){
      fail ++;
    }
  }
  if(fail == 0 && (!isNaN(parseInt(harga_jual.val()))) && cbxPembeli.getValue() != ''){
    if(confirm("Apakah data sudah benar?")){
      frm_penjualanGula.submit();
    }
  } else {
    if(fail > 0){
      alert("Kuanta gula dijual tidak boleh kosong!");
    }
    if(isNaN(parseInt(harga_jual.val()))){
      alert("Harga jual harus diisi!");
    }
    if(cbxPembeli.getValue() == ''){
      alert("Calon pembeli harus diisi!");
    }
  }
}

$("#tblListBasteb").DataTable({
  bFilter: true,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  autoWidth: false,
  ajax: {
    url: js_base_url + "Penjualan_gula/getAllBasteb?tahun_giling=0" ,
    dataSrc: ""
  },
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling">f>tpl',
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + 1;
      }
    },
    {
      render: function(data, type, row, meta){
        var buttonDetail = "";
        if(parseInt(row.gula_terjual) < parseInt(row.kg_gula_90)){
          buttonDetail = '<input type="checkbox" name="id_dokumen[]" value="'+row.id_dokumen+'"> ';
        }
        return buttonDetail;
      },
      className: "text-left"
    },
    { data: "nama_kelompok" },
    { data: "no_kontrak" },
    {
      data: "ton_tebu_hitung",
      className: "text-right",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString(undefined, {maximumFractionDigits:2});
      },
    },
    {
      data: "kg_gula_90",
      className: "text-right",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2});
      }
    },
    {
      data: "gula_terjual",
      className: "text-right",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2});
      },
    },
    {
      data: "kg_tetes_ptr",
      className: "text-right",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2});
      },
    },
    {
      data: "tetes_terjual",
      className: "text-right",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2});
      },
    }
  ],
  rowId: "id_dokumen",
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "250px",
      "display": "",
      "margin-left": "10px"
    }).attr("placeholder", "Cari");
    $(".dataTables_filter").css({
      "margin": "0px"
    });
    var currYear = parseInt(new Date().getFullYear());
    var i;
    var optionTahun = '<option value="0">Pilih tahun giling</option>';
    for (i=0; i < 4; i++){
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + parseInt(currYear + i) + '</option>';
    }
    $("div.cbxTahunGiling").html('<select style="width: 150px; margin-bottom: 10px; margin-left: 10px" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label style="margin-left: 10px; height: 37px; margin-bottom: 10px; display: block; background: red"></label>');
    $('#tahun_giling').selectize({create: false, sortField: 'value'});
    function refreshTable(){
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblListBasteb").DataTable().ajax.url(js_base_url + "Penjualan_gula/getAllBasteb?tahun_giling=" + tahun_giling).load();
    }
    $("#tahun_giling").on("change", function(){
      refreshTable();
    });
  },
  footerCallback: function (row, data, start, end, display){
    var api = this.api(), data;
    var intRupiah = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    totalRupiah = api.column(5).data().reduce( function (a, b) {
        return intRupiah(a) + intRupiah(b);
    },0);
    $(api.column(5).footer()).html('<font color="white" size="3">' + "Rp " + totalRupiah.toLocaleString({maximumFractionDigits: 0}) + ' </font>');
  },
  language: {
    "search": ""
  }
});

$(document).ready(function() {
    // jQuery code goes here
    refreshNamaPembeli();
    getFormValues();
});
