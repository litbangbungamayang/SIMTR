$('#jenis_bahan').selectize({create: false, sortField: ''});
$('#satuan').selectize({create: false, sortField: 'text'});

var namaBahan = $("#nama_bahan");
var jenisBahan = $("#jenis_bahan");
var satuan = $("#satuan");
var tblBahan = $("#tblBahan");
var formAddBahan = $("#formAddBahan");
var dialogAddBahan = $("#dialogAddBahan");
var edit = false;

dialogAddBahan.on("hide.bs.modal", function(){
  namaBahan.val("");
  jenisBahan[0].selectize.clear();
  satuan[0].selectize.clear();
  tblBahan.DataTable().ajax.reload();
})

function hapusData(id){
  $.ajax({
    url: js_base_url + "Admin_bahan/getBahanById",
    type: "GET",
    data: "idBahan=" + id,
    dataType: "json",
    success: function(response){
      if (confirm("Anda yakin akan menghapus data bahan " + response.nama_bahan + "?")){
        $.ajax({
          url: js_base_url + "Admin_bahan/hapusBahan",
          type: "POST",
          dataType: "text",
          data: {id_bahan: id},
          success: function(result){
            console.log(result);
            alert(result);
            tblBahan.DataTable().ajax.reload();
          }
        });
      }
    }
  });
}

function editData(id){
  dialogAddBahan.modal("toggle");
  $.ajax({
    url: js_base_url + "Admin_bahan/getBahanById",
    type: "GET",
    data: "idBahan=" + id,
    dataType: "json",
    success: function(response){
      namaBahan.val(response.nama_bahan);
      jenisBahan[0].selectize.setValue(response.jenis_bahan, true);
      satuan[0].selectize.setValue(response.satuan, true);
      $("#btnSimpanBahan").on("click", function(){simpanEditData(response.id_bahan)});
      edit = true;
    }
  });
}

function simpanEditData(id){
  console.log("Simpan edit = " + id);
  if (namaBahan.val() != "" && jenisBahan.val() != "" && satuan.val() != "" && edit){
    namaBahan.removeClass("is-invalid");
    jenisBahan.removeClass("is-invalid");
    satuan.removeClass("is-invalid");
    $.ajax({
      url: js_base_url + "Admin_bahan/editBahan",
      type: "POST",
      dataType: "text",
      data: {
        id_bahan: id,
        nama_bahan: namaBahan.val(),
        jenis_bahan: jenisBahan.val(),
        satuan: satuan.val()
      },
      success: function(data){
        namaBahan.val("");
        jenisBahan[0].selectize.clear();
        satuan[0].selectize.clear();
        tblBahan.DataTable().ajax.reload();
        edit = false;
        dialogAddBahan.modal("toggle");
      },
      error: function(textStatus){
        console.log(textStatus);
      }
    });
  } else {
    (namaBahan.val() == "") ? namaBahan.addClass("is-invalid") : "";
    (jenisBahan.val() == "") ? jenisBahan.addClass("is-invalid") : "";
    (satuan.val() == "") ? satuan.addClass("is-invalid") : "";
  }
  edit = false;
}

$("#btnSimpanBahan").on("click", function(){
  console.log("klik");
  if (namaBahan.val() != "" && jenisBahan.val() != "" && satuan.val() != "" && !edit){
    namaBahan.removeClass("is-invalid");
    jenisBahan.removeClass("is-invalid");
    satuan.removeClass("is-invalid");
    $.ajax({
      url: js_base_url + "Admin_bahan/addBahan",
      type: "POST",
      dataType: "text",
      data: formAddBahan.serialize(),
      success: function(data){
        namaBahan.val("");
        jenisBahan[0].selectize.clear();
        satuan[0].selectize.clear();
        tblBahan.DataTable().ajax.reload();
      },
      error: function(textStatus){
        console.log(textStatus);
      }
    });
  } else {
    (namaBahan.val() == "") ? namaBahan.addClass("is-invalid") : "";
    (jenisBahan.val() == "") ? jenisBahan.addClass("is-invalid") : "";
    (satuan.val() == "") ? satuan.addClass("is-invalid") : "";
  }
})

$("#tblBahan").DataTable({
  bFilter: true,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  ajax: {
    url: js_base_url + "Admin_bahan/getAllBahan",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "nama_bahan"},
    {data: "jenis_bahan"},
    {data: "satuan"},
    {data: "button",
      render: function(data, type, row, meta){
        return '<button type="button" onclick="editData('+row.id_bahan+')" class="btn btn-warning btn-sm" id="btnEditBahan" name="btnEditBahan">Ubah Data</button>  ' +
        '<button type="button" onclick="hapusData('+row.id_bahan+')" class="btn btn-danger btn-sm" name="hapus_data" value="'+row.id_bahan+'" >Hapus Data</button>'
      },
      className: "text-center"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "250px",
      "display": "inline-block",
      "margin": "10px"
    }).attr("placeholder", "Cari");
    $(".dataTables_filter").css({
      "margin": "0px"
    });
  },
  language: {
    "search": ""
  }
})
