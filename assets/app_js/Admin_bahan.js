$('#jenis_bahan').selectize({create: false, sortField: ''});
$('#satuan').selectize({create: false, sortField: 'text'});

$("#btnSimpanBahan").on("click", function(){
  console.log("klik");
  var namaBahan = $("#nama_bahan");
  var jenisBahan = $("#jenis_bahan");
  var satuan = $("#satuan");
  var tblBahan = $("#tblBahan");
  var formAddBahan = $("#formAddBahan");
  if (namaBahan.val() != "" && jenisBahan.val() != "" && satuan.val() != ""){
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
        return '<form action="Admin_bahan/actions" method="get"><button type="submit" class="btn btn-warning btn-sm" name="edit_data" value="'+row.id_bahan+'" >Ubah Data</button>  ' +
        '<button type="submit" class="btn btn-danger btn-sm" name="hapus_data" value="'+row.id_bahan+'" >Hapus Data</button>' +
        '</form>'
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
