var dialogAddPermintaanPupuk = $("#dialogAddPermintaanPupuk");

$("#luas_aplikasi").bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9. ]/g,""));
});
$("#luas_aplikasi").on("blur", function(){
  if ($(this).val() != ""){
    $(this).val(parseFloat($(this).val()));
    var luas_maks = parseFloat($("#luas").val().replace(/[^0-9. ]/g,""));
    console.log(luas_maks);
    (parseFloat($(this).val()) > luas_maks) ? $(this).val(luas_maks) : "";
  }
})

$("#tblList").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling">f>tpl',
  ajax: {
    url: js_base_url + "Rdkk_list/getAllKelompok",
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
    {data: "no_kontrak"},
    {
      data: "tahun_giling",
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
    {data: "button",
      render: function(data, type, row, meta){
        //return '<form action="Rdkk_view" method="get"><button type="submit" class="btn btn-info btn-sm" name="idKelompok" value="'+row.id_kelompok+'" title="Lihat Data Kelompok"><i class="fe fe-external-link"></i></button></form>'
        return actionButtonView(row.id_kelompok);
      }
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
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + parseInt(currYear + i) + '</option>';
    }
    $("div.cbxTahunGiling").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
    //console.log($("#tahun_giling").selectize()[0].selectize.getValue());
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

function addPupuk(id_kelompok){
  dialogAddPermintaanPupuk.modal("toggle");
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    type: "GET",
    dataType: "json",
    data: {
      id_kelompok: id_kelompok
    },
    success: function(response){
      console.log(response[0].id_kelompok);
      $("#namaKelompok").val(response[0].nama_kelompok);
      $("#luas").val(parseFloat(response[0].luas).toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha");
      $("#tblPupuk").DataTable().ajax.url(js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok=" + response[0].id_kelompok).load();
      $.ajax({
        url: js_base_url + "Admin_bahan/getBahanByJenis",
        type: "GET",
        dataType: "json",
        data: {
          jenis_bahan: "PUPUK"
        },
        success: function(response){
          $("#jenis_pupuk").selectize({
            valueField: "id_bahan",
            labelField: "nama_bahan",
            sortField: "nama_bahan",
            searchField: "nama_bahan",
            maxItems: 1,
            create: false,
            placeholder: "Pilih jenis pupuk",
            options: response
          });
        }
      });
    },
    error: function(textStatus){
      console.log(textStatus);
    }
  })
}

function actionButtonView(id_kelompok){
  return  '<div class="btn-group"><button style="width: 80px" type="button" class="btn btn-secondary btn-sm btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
          '<i class="fe fe-settings mr-2"></i> Opsi' +
          '</button>' +
          '<div class="dropdown-menu dropdown-menu-right">' +
            '<a class="dropdown-item" href="Rdkk_view?idKelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat Data Kelompok</a>' +
            '<div class="dropdown-divider"></div>' +
            '<a class="dropdown-item" href="#" onclick="addPupuk(' + id_kelompok + ')"><i class="fe fe-sunset"></i> Buat Permintaan Pupuk</a>' +
            '<a class="dropdown-item" href="#"><i class="fe fe-feather"></i> Buat Permintaan Perawatan</a>' +
          '</div></div>';
}

$("#tblPupuk").DataTable({
  bFilter: false,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  ajax: {
    url: js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok="+0,
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "no_transaksi"},
    {data: "tgl_transaksi"},
    {data: "nama_bahan"},
    {data: "kuanta",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2}) + " " +row.satuan;
      },
      className: "text-right"
    }
  ]
});
