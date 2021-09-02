var MAX_IMAGE_SIZE = 200; //kb
var cbxTglSurat = $("#tgl_survey");
var txtKeterangan = $("#keterangan_survey");
var scanSurat = $("#scanSurat");
var frmSurvey = $("#frmSurvey");
var review_gm = $("#review_gm");

cbxTglSurat.datepicker({
  format: "dd-mm-yyyy"
});

$("#scanSurat").change(function (e){
  validasiFile($(this), $("#lblScanSurat"), (MAX_IMAGE_SIZE*1024), "image/jpeg", $("#fbScanSurat"));
});

/********************** GENERAL FUNCTIONS SECTION *****************************/

function prosesUsulan(id_kelompok, nama_kelompok){
  if(confirm("Anda yakin akan memproses usulan RDKK Kelompok Tani " + nama_kelompok + " ?")){
    $.ajax({
      url: js_base_url + "List_skk/proses",
      data: {
        id_kelompok: id_kelompok,
        step: 1
      },
      dataType: "json",
      type: "GET",
      success: function(response){
        $("#tblList").DataTable().ajax.url(js_base_url + "List_skk/getAllRequest").load();
      }
    })
  }
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

function validasiForm(){
  if(cbxTglSurat.val() != "" && txtKeterangan.val() != "" && scanSurat.val() != ""){
    return true;
  } else{
    if(cbxTglSurat.val() == "") alert("Tanggal survey tidak boleh kosong!");
    if(txtKeterangan.val() == "") alert("Keterangan survey harus diisi!");
    if(scanSurat.val() == "") alert("Scan SKK harus diinput!");
  }
  return false;
}

function setRdkk(id_kelompok,status_skk){
  var formData = new FormData(frmSurvey[0]);
  formData.append("status_skk",status_skk);
  formData.append("id_kelompok",id_kelompok);
  if(validasiForm()){
    $.ajax({
      url: js_base_url + "Resume_skk/simpan",
      enctype: "multipart/form-data",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      type: "POST",
      success: function(response){
        window.location.replace(js_base_url + "List_skk");
      }
    })
  }
}



/************************ DATATABLE SECTION ***********************************/

$("#tblList").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling">f>tpl',
  ajax: {
    url: js_base_url + "List_skk/getAllRequest",
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + 1;
      }
    },
    {data: "nama_kelompok"},
    {
      data: "kategori",
      render: function(data, type, row, meta){
        var katg = "";
        switch(data){
          case "1":
            katg = "PC";
            break;
          case "2":
            katg = "RT 1";
            break;
          case "3":
            katg = "RT 2";
            break;
          case "4":
            katg = "RT 3";
            break;
        }
        return katg;
      },
      className: "text-center"
    },
    {
      data: "tahun_giling",
      render: function(data, type, row, meta){
        var tahun_tanam = parseInt(data) - 1;
        var str_tahungiling = data.toString().substr(2,2);
        var str_tahuntanam = tahun_tanam.toString().substr(2,2);
        return str_tahuntanam + "/" + str_tahungiling;
      },
      className: "text-center"
    },
    {data: "nama_wilayah"},
    {data: "mt"},
    {data: "nama_varietas"},
    {data: "luas",
      render: function(data, type, row, meta){
        return data.toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha"
      },
      className: "text-right"
    },
    {
      data: "",
      render: function(data, type, row, meta){
        var label_status = "";
        switch (row.status) {
          case "0":
            label_status = "<span class='tag tag-yellow'>Usulan RDKK</span>";
            break;
          case "1":
            label_status = "<span class='tag tag-orange'>Proses survey</span>";
            break;
          case "2":
            label_status = "<span class='tag tag-cyan'>Menunggu validasi GM</span>";
            break;
          case "3":
            label_status = "<span class='tag tag-green'>RDKK disetujui</span>";
            break;
          case "4":
            label_status = "<span class='tag tag-red'>Tidak layak</span>";
            break;
          case "5":
            label_status = "<span class='tag text-white bg-dark'>RDKK ditolak</span>";
            break;
        }
        return label_status;
      },
      className: "text-center"
    },
    {data: "button",
      render: function(data, type, row, meta){
        //return actionButtonView(row.id_kelompok);
        var btn_aksi = "";
        btn_aksi = "<a class='btn btn-sm btn-cyan' href='Rdkk_view?id_kelompok=" + row.id_kelompok + "' title='Lihat data'><i class='fe fe-user'></i></a> "
        switch(row.status){
          case "0":
            return btn_aksi + '<a class="btn btn-sm btn-cyan" href="#" onclick="prosesUsulan('+
              row.id_kelompok+','+'\x27'+row.nama_kelompok+'\x27'+');"><i class="fe fe-settings"></i> Proses usulan</a>';
            break;
          case "1":
            return btn_aksi + '<a class="btn btn-sm btn-cyan" href="'+js_base_url+"Resume_skk?id_kelompok="+row.id_kelompok+'" onclick=""><i class="fe fe-settings"></i> Buat resume survey</a>';
            break;
          case "2":
            if(priv_level == "GM"){
              return btn_aksi + "<a class='btn btn-sm btn-cyan' href='#' onclick=viewSkk('"+row.id_kelompok+"') title='Lihat SKK'><i class='fe fe-settings'></i> Lihat SKK</a>";
            }
            break;
        }
        return btn_aksi;
      },
      className: "text-left"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "200px",
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
      var tahun_tanam = parseInt(currYear + i) - 1;
      var str_tahungiling = (currYear + i).toString().substr(2,2);
      var str_tahuntanam = tahun_tanam.toString().substr(2,2);
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + 'KTG ' + str_tahuntanam + '/' + str_tahungiling + '</option>';
    }
    $("div.cbxTahunGiling").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
    $('#tahun_giling').selectize({create: false, sortField: 'value'});
    $("#tahun_giling").on("change", function(){
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblList").DataTable().ajax.url(js_base_url + "Rdkk_list/getKelompokByTahun?tahun_giling=" + tahun_giling).load();
    });
  },
  language: {
    "search": ""
  }
});
