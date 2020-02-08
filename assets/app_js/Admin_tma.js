var $cbxNamaDesa, cbxNamaDesa;
var $cbxNamaKab, cbxNamaKab;
var $cbxTahunGiling;
var txtBiaya = $("#biaya");

$("#btnSimpanBiayaTma").on("click", function(){
  var formResult = validasiForm();
  if(formResult != null){
    var controller = "Admin_tma/addBiayaTma";
    $.ajax({
      url: js_base_url + controller,
      type: "POST",
      dataType: "json",
      data: formResult,
      success: function (response){
        if(response != ""){
          alert("Data berhasil disimpan!");
          $("#dialogAddTma").modal("toggle");
          $("#tblBiayaTma").DataTable().ajax.reload();
        }
      }
    })
  }
})

$("#dialogAddTma").on("hide.bs.modal", function(){
  resetForm();
})

txtBiaya.bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9]/g,""));
  ($(this).val() != "") ? $(this).val("Rp " + parseInt($(this).val()).toLocaleString()) : "";
});

$cbxTahunGiling = $("#tahun_giling").selectize({
  create: false,
  sortField: "text"
})

$cbxNamaDesa = $("#namaDesa").selectize({
  valueField: "id_wilayah",
  labelField: "deskripsi",
  sortField: "deskripsi",
  searchField: "deskripsi",
  maxItems: 1,
  create: false,
  placeholder: "Pilih desa"
})

$.ajax({
  url: js_base_url + "Rdkk_add/getAllKabupaten",
  type: "GET",
  dataType: "json",
  success: function(response){
    $cbxNamaKab =  $("#namaKab").selectize({
      valueField: "id_wilayah",
      labelField: "nama_wilayah",
      sortField: "nama_wilayah",
      searchField: "nama_wilayah",
      maxItems: 1,
      create: false,
      placeholder: "Pilih kabupaten",
      options: response,
      onChange: function(value){
        cbxNamaDesa.disable();
        cbxNamaDesa.clearOptions();
        cbxNamaDesa.load(function (callback){
          $.ajax({
            url: js_base_url + "Rdkk_add/getDeskripsiDesaByIdKabupaten",
            type: "GET",
            dataType: "json",
            data: "idKab=" + value,
            success: function(response){
              cbxNamaDesa.enable();
              callback(response);
            }
          })
        })
      }
    });
    cbxNamaKab = $cbxNamaKab[0].selectize;
  }
});

function validasiForm(){
  if(cbxTahunGiling.getValue() != "" && txtBiaya.val() != "" && cbxNamaDesa.getValue() != ""){
    var biayaValue = txtBiaya.val().replace(/[^0-9]/g,"");
    var result = {id_wilayah: cbxNamaDesa.getValue(), tahun_giling: cbxTahunGiling.getValue(), biaya: biayaValue};
    resetForm();
    return result;
  } else {
    (cbxTahunGiling.getValue == "") ? $cbxTahunGiling.addClass("is-invalid") : $cbxTahunGiling.removeClass("is-invalid");
    (txtBiaya.val() == "") ? txtBiaya.addClass("is-invalid") : txtBiaya.removeClass("is-invalid");
    (cbxNamaDesa.getValue() == "") ? $cbxNamaDesa.addClass("is-invalid") : $cbxNamaDesa.removeClass("is-invalid");
  }
}

function resetForm(){
  cbxNamaDesa.clearOptions();
  cbxNamaDesa.clear();
  cbxNamaKab.clear();
  txtBiaya.val("");
  txtBiaya.removeClass("is-invalid");
  $cbxNamaKab.removeClass("is-invalid");
  $cbxNamaDesa.removeClass("is-invalid");
}

$("#tblBiayaTma").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"spacer"><"cbxTahunGilingList">f>tpl',
  ajax: {
    url: js_base_url + "Admin_tma/getAllBiayaTma?tahun_giling=0",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "tahun_giling"},
    {data: "deskripsi"},
    {data: "kabupaten"},
    {
      data: "biaya",
      className: "text-right",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(row.biaya).toLocaleString({maximumFractionDigits: 0});
      }
    },
    {data: "button",
      render: function(data, type, row, meta){
        return '<button type="button" onclick="editData('+row.id_bahan+')" class="btn btn-warning btn-sm" id="btnEditBahan" name="btnEditBahan" title="Ubah Data"><i class="fe fe-edit"></i></button>  ' +
        '<button type="button" onclick="hapusData('+row.id_bahan+')" class="btn btn-danger btn-sm" name="hapus_data" value="'+row.id_bahan+'" title="Hapus Data"><i class="fe fe-trash-2"></i></button>'
      },
      className: "text-center"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "250px",
      "display": "inline-block",
      "margin": "0px 0px 0px 10px"
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
    $("div.cbxTahunGilingList").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.spacer").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
    $('#tahun_giling').selectize({create: false, sortField: 'value'});
    $("#tahun_giling").on("change", function(){
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblBiayaTma").DataTable().ajax.url(js_base_url + "Admin_tma/getAllBiayaTma?tahun_giling=" + tahun_giling).load();
    });
  },
  language: {
    "search": ""
  }
});

cbxNamaDesa = $cbxNamaDesa[0].selectize;
cbxTahunGiling = $cbxTahunGiling[0].selectize;
