var MAX_IMAGE_SIZE = 200; //kb
var tahunGiling = $("#tahun_giling");
var txt_nama_koordinator = $("#nama_koordinator");
var txt_no_ktp = $("#no_ktp");
var txt_nomor_telepon = $("#nomor_telepon");
var scan_ktp = $("#scanKtp");
var lblScanKtp = $("#lblScanKtp");
var tblKoordinator = $("#tblKoordinator");
var formAddKoord = $("#formAddKoord");
var dialogAddKoord = $("#dialogAddKoord");
var btnTambahKoord = $("#btnTambahKoord");
var edit = false;
var edit_id = null;

$("#scanKtp").change(function (e){
  validasiFile($(this), $("#lblScanKtp"), (MAX_IMAGE_SIZE*1024), "image/jpeg", $("#fbScanKtp"));
});

function koreksiDesimal(textBox){
  var val_textbox = textBox.val();
  val_textbox = parseInt(val_textbox.replace(/[^0-9.]/,""));
  (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
  //textBox.val(val_textbox.toLocaleString(undefined, {maximumFractionDigits:0}));
  textBox.val(val_textbox);
}

function validasiFile($fileInput, $lblFileInput, $maxFileSize, $fileType, $feedBackLabel){
  var inputFile = $fileInput;
  var selectedFile = $fileInput[0].files[0];
  var labelInput = $lblFileInput;
  var maxSize = $maxFileSize; //in byte
  var allowedType = $fileType;
  var feedbackLabel = $feedBackLabel;
  if (inputFile.val() != ""){
    if (selectedFile.type == $fileType && selectedFile.size <= (MAX_IMAGE_SIZE*1024)){
      feedbackLabel.hide();
      inputFile.removeClass("is-invalid");
      labelInput.text(selectedFile.name);
    } else {
      if (selectedFile.type != $fileType){
        feedbackLabel.show();
        feedbackLabel.html("Format image tidak sesuai!");
        inputFile.addClass("is-invalid");
        inputFile.val("");
        labelInput.text("Pilih file");
      } else {
        if (selectedFile.size > maxSize){
          feedbackLabel.show();
          feedbackLabel.html("Ukuran file melebihi batas maksimal! (Maks. 200kB)");
          inputFile.addClass("is-invalid");
          inputFile.val("");
          labelInput.text("Pilih file");
        }
      }
    }
  }
};

function resetForm(){
  tahunGiling[0].selectize.clear();
  txt_nama_koordinator.val("");
  txt_no_ktp.val("");
  txt_nomor_telepon.val("");
  scan_ktp.val("");
  scan_ktp.prop("disabled", false);
  txt_no_ktp.prop("disabled", false);
  scan_ktp.prop("disabled", false);
  txt_nama_koordinator.prop("disabled", false);
  lblScanKtp.text("Pilih file");
  tahunGiling.removeClass("is-invalid");
  txt_nama_koordinator.removeClass("is-invalid");
  txt_no_ktp.removeClass("is-invalid");
  txt_nomor_telepon.removeClass("is-invalid");
  scan_ktp.removeClass("is-invalid");
}

btnTambahKoord.on("click", function(){
    tahunGiling[0].selectize.enable();
    tahunGiling[0].selectize.clear();
})

dialogAddKoord.on("hide.bs.modal", function(){
  resetForm();
  tblKoordinator.DataTable().ajax.reload();
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
  dialogAddKoord.modal("toggle");
  $.ajax({
    url: js_base_url + "Admin_koordinator/getKoordById",
    type: "GET",
    data: {id_koordinator: id},
    dataType: "json",
    success: function(response){
      tahunGiling[0].selectize.setValue(response.tahun_giling,true);
      tahunGiling[0].selectize.disable();
      txt_nama_koordinator.val(response.nama_koordinator);
      txt_nomor_telepon.val(response.nomor_telepon);
      txt_no_ktp.val(response.no_ktp);
      txt_no_ktp.prop("disabled", true);
      scan_ktp.prop("disabled", true);
      txt_nama_koordinator.prop("disabled", true);
      edit = true;
      edit_id = id;
    }
  });
}

function simpanEditData(id){
  if (txt_nama_koordinator.val() != "" && txt_no_ktp.val() != "" &&
    txt_nomor_telepon.val() != "" && tahunGiling.val() !="" &&
    edit){
    $.ajax({
      url: js_base_url + "Admin_koordinator/editKoordinator",
      type: "POST",
      dataType: "text",
      data: {
        id_koordinator: id,
        nomor_telepon: txt_nomor_telepon.val(),
      },
      success: function(data){
        alert(data);
        dialogAddKoord.modal("toggle");
      }
    });
  } else {
    (txt_nomor_telepon.val() == "") ? txt_nomor_telepon.addClass("is-invalid") : "";
  }
}

$("#btnSimpanKoord").on("click", function(){
  if(!edit){
    if (txt_nama_koordinator.val() != "" && txt_no_ktp.val() != "" &&
      txt_nomor_telepon.val() != "" && tahunGiling.val() !="" &&
      scan_ktp.val() != ""  && !edit){
      let formData = new FormData(formAddKoord[0]);
      $.ajax({
        url: js_base_url + "Admin_koordinator/addKoordinator",
        type: "POST",
        enctype: "multipart/form-data",
        processData: false,
        contentType: false,
        dataType: "json",
        data: formData,
        success: function(data){
          if(data > 0){
            alert("Data berhasil disimpan!");
            dialogAddKoord.modal("toggle");
          }
        },
        error: function(textStatus){
          console.log(textStatus);
        }
      });
    } else {
      (scan_ktp.val() == "") ? scan_ktp.addClass("is-invalid") : "";
      (tahunGiling.val() == "") ? tahunGiling.addClass("is-invalid") : "";
      (txt_nama_koordinator.val() == "") ? txt_nama_koordinator.addClass("is-invalid") : "";
      (txt_nomor_telepon.val() == "") ? txt_nomor_telepon.addClass("is-invalid") : "";
      (txt_no_ktp.val() == "") ? txt_no_ktp.addClass("is-invalid") : "";
    }
  } else {
    simpanEditData(edit_id);
  }
})

$("#tblKoordinator").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"spacer"><"cbxTahunGilingList">f>tpl',
  ajax: {
    url: js_base_url + "Admin_koordinator/getAllKoordinator",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "tahun_giling", className: "text-center"},
    {
      data: "nama_koordinator",
      className: "text-left",
    },
    {
      data: "nomor_telepon",
      className: "text-center",
    },
    {data: "button",
      render: function(data, type, row, meta){
        return '<button type="button" onclick="editData('+row.id_koordinator+')" class="btn btn-warning btn-sm" id="btnEditBahan" name="btnEditBahan" title="Ubah Data"><i class="fe fe-edit"></i></button>  '
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
      $("#tblKoordinator").DataTable().ajax.url(js_base_url + "Admin_koordinator/getKoordByTahunGiling?" +
        "tahun_giling=" + tahun_giling).load();
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
