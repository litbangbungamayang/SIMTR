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
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
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
      "display": ""
    }).attr("placeholder", "Cari");
    $(".dataTables_filter").css({
      "margin-bottom": "0px",
      "margin-top": "0px"
    });
    var currYear = parseInt(new Date().getFullYear());
    var i;
    var optionTahun = '<option value="0">Pilih tahun giling</option>';
    for (i=0; i < 4; i++){
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + parseInt(currYear + i) + '</option>';
    }
    $("div.cbxTahunGiling").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label class="form-label" style="margin: 10px 15px 0px 0px;"></label>');
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

function actionButtonView(id_kelompok){
  return  '<div class="dropdown"><button style="width: 80px" type="button" class="btn btn-secondary btn-sm btn-cyan dropdown-toggle" data-toggle="dropdown">' +
          '<i class="fe fe-settings mr-2"></i> Opsi' +
          '</button>' +
          '<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">' +
            '<a class="dropdown-item" href="Rdkk_view?idKelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat Data Kelompok</a>' +
            '<a class="dropdown-item" href="#"><i class="fe fe-sunset"></i> Permintaan Pupuk</a>' +
            '<a class="dropdown-item" href="#"><i class="fe fe-feather"></i> Permintaan Perawatan</a>' +
          '</div></div>';
}
