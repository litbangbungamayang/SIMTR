$("#tblListPupuk").DataTable({
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
    {
      data: "urea",
      render: function(data, type, row, meta){
        var currentCell = $("#tblListPupuk").DataTable().cells({"row": meta.row, "column": meta.col}).nodes(0);
        $.ajax({
          url: js_base_url + "Biaya_muat_angkut_pupuk/getTransaksiBahanByIdKelompokNamaBahanPeriode",
          type: "GET",
          dataType: "json",
          async: true,
          data: {
            id_kelompok: row.id_kelompok,
            nama_bahan: "UREA"
          }
        }).done(function(data){
          if(data.length > 0){
            $(currentCell).text(parseInt(data[0].kuanta).toLocaleString(undefined, {maximumFractionDigits:0}) + " " + data[0].satuan);
          }
        });
        return null;
      }
    },
    {
      data: "tsp",
      render: function(data, type, row, meta){
        var currentCell = $("#tblListPupuk").DataTable().cells({"row": meta.row, "column": meta.col}).nodes(0);
        $.ajax({
          url: js_base_url + "Biaya_muat_angkut_pupuk/getTransaksiBahanByIdKelompokNamaBahanPeriode",
          type: "GET",
          dataType: "json",
          async: true,
          data: {
            id_kelompok: row.id_kelompok,
            nama_bahan: "TSP"
          }
        }).done(function(data){
          if(data.length > 0){
            $(currentCell).text(parseInt(data[0].kuanta).toLocaleString(undefined, {maximumFractionDigits:0}) + " " + data[0].satuan);
          }
        });
        return null;
      }
    },
    {
      data: "kcl",
      render: function(data, type, row, meta){
        var currentCell = $("#tblListPupuk").DataTable().cells({"row": meta.row, "column": meta.col}).nodes(0);
        $.ajax({
          url: js_base_url + "Biaya_muat_angkut_pupuk/getTransaksiBahanByIdKelompokNamaBahanPeriode",
          type: "GET",
          dataType: "json",
          async: true,
          data: {
            id_kelompok: row.id_kelompok,
            nama_bahan: "KCL"
          }
        }).done(function(data){
          if(data.length > 0){
            $(currentCell).text(parseInt(data[0].kuanta).toLocaleString(undefined, {maximumFractionDigits:0}) + " " + data[0].satuan);
          }
        });
        return null;
      },
      className: "text-right"
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
      $("#tblListPupuk").DataTable().ajax.url(js_base_url + "Rdkk_list/getKelompokByTahun?tahun_giling=" + tahun_giling).load();
    });
  },
  language: {
    "search": ""
  }
});
