
var nama_gudang = $("#nama_gudang");
var lokasi_gudang = $("#lokasi_gudang");
var deskripsi = $("#deskripsi");
var status_gudang = $("#status_gudang");
var formAddGudang = $("#formAddGudang");
var dialogAddGudang = $("#dialogAddGudang");
var btnSimpanGudang = $("#btnSimpanGudang");
var tblGudang = $("#tblGudang");
var edit = false;

function resetInputGudang(){
  nama_gudang.prop("disabled", false);
  nama_gudang.val("");
  lokasi_gudang.val("");
  deskripsi.val("");
  status_gudang[0].selectize.clear();
}

function removeError(){
  nama_gudang.removeClass("is-invalid");
  lokasi_gudang.removeClass("is-invalid");
  status_gudang.removeClass("is-invalid");
}

function hapusData(id){
  $.ajax({
    url: js_base_url + "Admin_gudang/getGudangById",
    type: "GET",
    data: "id_gudang=" + id,
    dataType: "json",
    success: function(response){
      if (confirm("Anda yakin akan menghapus data bahan " + response.nama_gudang + "?")){
        $.ajax({
          url: js_base_url + "Admin_gudang/hapusGudang",
          type: "POST",
          dataType: "text",
          data: {id_gudang: id},
          success: function(result){
            alert(result);
            tblGudang.DataTable().ajax.reload();
          }
        });
      }
    }
  });
}

function editData(id){
  dialogAddGudang.modal("toggle");
  $.ajax({
    url: js_base_url + "Admin_gudang/getGudangById",
    type: "GET",
    data: "id_gudang=" + id,
    dataType: "json",
    success: function(response){
      if(response !== null){
        nama_gudang.val(response.nama_gudang);
        nama_gudang.prop("disabled", true);
        lokasi_gudang.val(response.lokasi_gudang);
        deskripsi.val(response.deskripsi);
        status_gudang[0].selectize.setValue(response.status, true);
        $("#btnSimpanGudang").on("click", function(){simpanEditData(id)});
        edit = true;
      } else {
        alert("Data tidak ditemukan!");
      }
    }
  });
}

function simpanEditData(id){
  if (nama_gudang.val() != "" && lokasi_gudang.val() != "" && status_gudang.val() != "" && edit){
    removeError();
    $.ajax({
      url: js_base_url + "Admin_gudang/editGudang",
      type: "POST",
      dataType: "text",
      data: {
        id_gudang: id,
        nama_gudang: nama_gudang.val(),
        lokasi_gudang: lokasi_gudang.val(),
        deskripsi: deskripsi.val(),
        status: status_gudang.val()
      },
      success: function(data){
        resetInputGudang();
        tblGudang.DataTable().ajax.reload();
        edit = false;
        dialogAddGudang.modal("toggle");
      },
      error: function(textStatus){
        console.log(textStatus);
      }
    });
  } else {
    (lokasi_gudang.val() == "") ? lokasi_gudang.addClass("is-invalid") : "";
    (status_gudang.val() == "") ? status_gudang.addClass("is-invalid") : "";
  }
}

$("#btnSimpanGudang").on("click", function(){
  if (nama_gudang.val() != "" && lokasi_gudang.val() != "" && status_gudang.val() != "" && !edit){
    removeError();
    $.ajax({
      url: js_base_url + "Admin_gudang/addGudang",
      type: "POST",
      dataType: "text",
      data: formAddGudang.serialize(),
      success: function(data){
        resetInputGudang();
        tblGudang.DataTable().ajax.reload();
        dialogAddGudang.modal("toggle");
      },
      error: function(textStatus){
        console.log(textStatus);
      }
    });
  } else {
    (lokasi_gudang.val() == "") ? lokasi_gudang.addClass("is-invalid") : "";
    (status_gudang.val() == "") ? status_gudang.addClass("is-invalid") : "";
  }
})

$("#tblGudang").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"spacer">f>tpl',
  ajax: {
    url: js_base_url + "Admin_gudang/getAllGudang",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "nama_gudang"},
    {data: "lokasi_gudang"},
    {
      data: "status",
      render: function(data, type, row, meta){
        if(row.status == 0){
          return "<span class='tag tag-red'>Tidak aktif</span>";
        } else {
          return "<span class='tag tag-green'>Aktif</span>"
        }
      }
    },
    {data: "button",
      render: function(data, type, row, meta){
        return '<button type="button" onclick="editData('+row.id_gudang+')" class="btn btn-warning btn-sm" id="btnEditGudang" name="btnEditGudang" title="Ubah Data"><i class="fe fe-edit"></i></button>  ' +
        '<button type="button" onclick="hapusData('+row.id_gudang+')" class="btn btn-danger btn-sm" name="hapus_data" value="'+row.id_bahan+'" title="Hapus Data"><i class="fe fe-trash-2"></i></button>'
      },
      className: "text-center"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "250px",
      "display": "inline-block",
      "margin": "0px 0px 0px 0px"
    }).attr("placeholder", "Cari");
    $(".dataTables_filter").css({
      "margin": "0px"
    });
    $("div.spacer").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
  },
  language: {
    "search": ""
  }
});

$("#status_gudang").selectize({
  sortField: "text",
  maxItems: 1,
  create: false
});
