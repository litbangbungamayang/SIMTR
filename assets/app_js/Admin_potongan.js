$('#jenis_bahan').selectize({create: false, sortField: ''});
$('#satuan').selectize({create: false, sortField: 'text'});

var txt_potongan_karung = $("#potongan_karung");
var txt_potongan_tetes = $("#potongan_tetes");
var txt_potongan_admin = $("#potongan_admin");
var tahunGiling = $("#tahun_giling");
var cbxRelasiAktivitas =$("#relasi_aktivitas");
var satuan = $("#satuan");
var tblPotongan = $("#tblPotongan");
var formAddPotongan = $("#formAddPotongan");
var dialogAddPotongan = $("#dialogAddPotongan");
var btnTambahPotongan = $("#btnTambahPotongan");
var edit = false;
var edit_id = null;

function koreksiDesimal(textBox){
  var val_textbox = textBox.val();
  val_textbox = parseInt(val_textbox.replace(/[^0-9.]/,""));
  (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
  //textBox.val(val_textbox.toLocaleString(undefined, {maximumFractionDigits:0}));
  textBox.val(val_textbox);
}

txt_potongan_karung.keyup(function(){
  koreksiDesimal(txt_potongan_karung);
})

txt_potongan_tetes.keyup(function(){
  koreksiDesimal(txt_potongan_tetes);
})

txt_potongan_admin.keyup(function(){
  koreksiDesimal(txt_potongan_admin);
})

btnTambahPotongan.on("click", function(){
    tahunGiling[0].selectize.enable();
    tahunGiling[0].selectize.clear();
})

dialogAddPotongan.on("hide.bs.modal", function(){
  txt_potongan_karung.val("");
  txt_potongan_tetes.val("");
  txt_potongan_admin.val("");
  tblPotongan.DataTable().ajax.reload();
})

function hapusData(id){
  if(confirm("Anda yakin akan menghapus data ini?")){
    $.ajax({
      url: js_base_url + "Admin_potongan/hapus",
      type: "POST",
      data: {id_potongan: id},
      dataType: "text",
      success: function(resp){
        alert(resp);
        tblPotongan.DataTable().ajax.reload();
      }
    });
  }
}

function editData(id){
  dialogAddPotongan.modal("toggle");
  $.ajax({
    url: js_base_url + "Admin_potongan/getPotonganById",
    type: "GET",
    data: {id_potongan: id},
    dataType: "json",
    success: function(response){
      tahunGiling[0].selectize.setValue(response.tahun_giling,true);
      tahunGiling[0].selectize.disable();
      txt_potongan_karung.val(response.potongan_karung);
      txt_potongan_tetes.val(response.potongan_tetes);
      txt_potongan_admin.val(response.potongan_admin);
      edit = true;
      edit_id = id;
    }
  });
}

function simpanEditData(id){
  if (txt_potongan_karung.val() != "" && txt_potongan_tetes.val() != "" &&
    txt_potongan_admin.val() != "" && edit){
      txt_potongan_karung.removeClass("is-invalid");
      txt_potongan_tetes.removeClass("is-invalid");
      txt_potongan_admin.removeClass("is-invalid");
    $.ajax({
      url: js_base_url + "Admin_potongan/editPotongan",
      type: "POST",
      dataType: "text",
      data: {
        id_potongan: id,
        potongan_karung: txt_potongan_karung.val(),
        potongan_tetes: txt_potongan_tetes.val(),
        potongan_admin: txt_potongan_admin.val(),
      },
      success: function(data){
        alert(data);
        dialogAddPotongan.modal("toggle");
      }
    });
  } else {
    (namaBahan.val() == "") ? namaBahan.addClass("is-invalid") : "";
    (jenisBahan.val() == "") ? jenisBahan.addClass("is-invalid") : "";
    (satuan.val() == "") ? satuan.addClass("is-invalid") : "";
    (dosisBahan.val() == "") ? dosisBahan.addClass("is-invalid") : "";
    (kemasanBahan.val() == "") ? kemasanBahan.addClass("is-invalid") : "";
  }
}

$("#btnSimpanPotongan").on("click", function(){
  if(!edit){
    if (txt_potongan_karung.val() != "" && txt_potongan_tetes.val() != "" &&
      txt_potongan_admin.val() != "" && !edit){
      txt_potongan_karung.removeClass("is-invalid");
      txt_potongan_tetes.removeClass("is-invalid");
      txt_potongan_admin.removeClass("is-invalid");
      $.ajax({
        url: js_base_url + "Admin_potongan/getPotonganByTahunGiling",
        type: "get",
        dataType: "json",
        data: formAddPotongan.serialize(),
        success: function(data){
          if(data.length == 0){
            $.ajax({
              url: js_base_url + "Admin_potongan/addPotongan",
              type: "post",
              dataType: "text",
              data: formAddPotongan.serialize(),
              success: function(data){
                tblPotongan.DataTable().ajax.reload();
                dialogAddPotongan.modal("toggle");
              }
            })
          } else {
            alert("Sudah terdapat data untuk tahun giling " + tahunGiling.val() + "!");
          }
        },
        error: function(textStatus){
          console.log(textStatus);
        }
      });
    } else {
      (txt_potongan_karung.val() == "") ? txt_potongan_karung.addClass("is-invalid") : "";
      (txt_potongan_tetes.val() == "") ? txt_potongan_tetes.addClass("is-invalid") : "";
      (txt_potongan_admin.val() == "") ? txt_potongan_admin.addClass("is-invalid") : "";
    }
  } else {
    simpanEditData(edit_id);
  }
})

$("#tblPotongan").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"spacer"><"cbxTahunGilingList">f>tpl',
  ajax: {
    url: js_base_url + "Admin_potongan/getAllPotongan",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "tahun_giling", className: "text-center"},
    {
      data: "potongan_karung",
      className: "text-right",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2})
      },
    },
    {
      data: "potongan_tetes",
      className: "text-right",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2})
      },
    },
    {
      data: "potongan_admin",
      className: "text-right",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2})
      },
    },
    {data: "button",
      render: function(data, type, row, meta){
        return '<button type="button" onclick="editData('+row.id_potongan+')" class="btn btn-warning btn-sm" id="btnEditBahan" name="btnEditBahan" title="Ubah Data"><i class="fe fe-edit"></i></button>  ' +
        '<button type="button" onclick="hapusData('+row.id_potongan+')" class="btn btn-danger btn-sm" name="hapus_data" value="'+row.id_bahan+'" title="Hapus Data"><i class="fe fe-trash-2"></i></button>'
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
    for (i=-1; i < 4; i++){
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + parseInt(currYear + i) + '</option>';
    }
    $("div.cbxTahunGilingList").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.spacer").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
    //console.log($("#tahun_giling").selectize()[0].selectize.getValue());
    $('#tahun_giling').selectize({create: false, sortField: 'value'});
    $("#tahun_giling").on("change", function(){
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblPotongan").DataTable().ajax.url(js_base_url + "Admin_potongan/getPotonganByTahunGiling?tahun_giling=" + tahun_giling).load();
    });
  },
  language: {
    "search": ""
  }
});

$("#tahun_giling").selectize({
  sortField: "text",
  maxItems: 1,
  create: false,
  placeholder: "Pilih tahun giling"
});
