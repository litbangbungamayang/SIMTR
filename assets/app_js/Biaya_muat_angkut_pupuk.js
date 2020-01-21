function formatTgl(dateObj){
  if(dateObj != null){
    /*
    return dateObj.getFullYear() + "-" + (dateObj.getMonth()+1) + "-" + ("0" + dateObj.getDate()).slice(-2) + " " +
      ("0" + dateObj.getHours()).slice(-2) + ":" + ("0" + dateObj.getMinutes()).slice(-2) + ":" + ("0" + dateObj.getSeconds()).slice(-2);
    */
    return dateObj.getFullYear() + "-" + ("0" + (dateObj.getMonth()+1)) + "-" + ("0" + dateObj.getDate()).slice(-2);
  }
  return "";
}

var nama_kelompok = null;
var no_kontrak = null;
var tahun_giling = null;
var luas = null;

/*
function getDataKelompok(id_kelompok){
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    type: "GET",
    dataType: "json",
    async: false,
    data: {
      id_kelompok: id_kelompok
    }
  }).done(function(data){
    nama_kelompok = data.nama_kelompok;
    no_kontrak = data.no_kontrak;
    tahun_giling = data.tahun_giling;
    luas = data.luas;
  });
}

$("#").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling"><"dtpTglAwal"><"dtpTglAkhir">f>tpl',
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
    {
      data: "nama_kelompok",
      render: function(data, type, row, meta){
        getDataKelompok(row.id_kelompok);
        return nama_kelompok;
      }
    },
    {
      data: "no_kontrak",
      render: function(data, type, row, meta){
        return no_kontrak;
      }
    },
    {
      data: "tahun_giling",
      render: function(data, type, row, meta){
        return tahun_giling;
      },
      className: "text-center"
    },
    {data: "luas",
      render: function(data, type, row, meta){
        //return data.toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha"
        return parseFloat(luas).toLocaleString({maximumFractionDigits: 2}) + " Ha";
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
      },
      className: "text-right"
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
      },
      className: "text-right"
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
    },
    {
      data: "biaya_muat",
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
      }
    },
  ],
  initComplete: function(){
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
    //$("div.dtpTglAwal").html("<input type='text' class='form-control' data-provide='datepicker' style='width: 200px; margin-left: 10px;'>");
    $("div.dtpTglAwal").html("<input type='text' class='form-control text-center' placeholder='Tanggal Awal' id='dtpAwal' style='width: 120px; margin-left: 10px;'>");
    $("#dtpAwal").datepicker({
      format: "dd-mm-yyyy"
    });
    $("div.dtpTglAkhir").html("<input type='text' class='form-control text-center' placeholder='Tanggal Akhir' id='dtpAkhir' style='width: 120px; margin-left: 10px; margin-bottom: 10px'>");
    $("#dtpAkhir").datepicker({
      format: "dd-mm-yyyy"
    });
    //<input type='text' class="form-control" data-provide="datepicker" style='width: 300px;' >
  },
  language: {
    "search": ""
  }
});
*/

$("#btnBuatPBMA").on("click", function(){
  if (confirm("Buat pengajuan biaya untuk daftar tersebut?")){
    var url_string = $("#tblListPupuk").DataTable().ajax.url();
    var url = new URL(url_string);
    var tgl_awal = url.searchParams.get("tgl_awal");
    var tgl_akhir = url.searchParams.get("tgl_akhir");
    $.ajax({
      url: js_base_url + "Biaya_muat_angkut_pupuk/buatPbma",
      dataType: "text",
      type: "POST",
      data: "tipe_dokumen=PBMA&tgl_awal=" + tgl_awal + "&tgl_akhir=" + tgl_akhir,
      success: function(response){
        alert(response);
        $("#tblListPupuk").DataTable().ajax.reload();
      }
    });
    console.log("StartDate = " + tgl_awal + "; EndDate = " + tgl_akhir);
  }
})



$("#tblListPupuk").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  autoWidth: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling"><"dtpTglAwal"><"dtpTglAkhir"><"btnSearch">f>tpl',
  ajax: {
    url: js_base_url + "Biaya_muat_angkut_pupuk/getRekapBiayaMuatAngkutPupuk?tahun_giling=0&tgl_awal=2000-01-01&tgl_akhir=2000-12-31" ,
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + 1;
      }
    },
    {
      width: "10%",
      data: "nama_kelompok",
      render: function(data, type, row, meta){
        return row.nama_kelompok;
      },
    },
    {
      data: "no_kontrak",
      render: function(data, type, row, meta){
        return row.no_kontrak;
      }
    },
    {
      data: "nama_wilayah",
      render: function(data, type, row, meta){
        return row.nama_wilayah;
      },
      className: "text-left"
    },
    {
      data: "tgl_transaksi",
      render: function(data, type, row, meta){
        return row.tgl_transaksi;
      },
      className: "text-center"
    },
    {data: "luas",
      render: function(data, type, row, meta){
        //return data.toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha"
        return parseFloat(row.luas).toLocaleString({maximumFractionDigits: 2}) + " Ha";
      },
      className: "text-right"
    },
    {
      data: "urea",
      render: function(data, type, row, meta){
        return parseInt((row.urea == null) ? 0 : row.urea).toLocaleString({maximumFractionDigits: 2}) + " KG";
      },
      className: "text-right"
    },
    {
      data: "tsp",
      render: function(data, type, row, meta){
        return parseInt((row.tsp == null) ? 0 : row.tsp).toLocaleString({maximumFractionDigits: 2}) + " KG";;
      },
      className: "text-right"
    },
    {
      data: "kcl",
      render: function(data, type, row, meta){
        return parseInt((row.kcl == null) ? 0 : row.kcl).toLocaleString({maximumFractionDigits: 2}) + " KG";;
      },
      className: "text-right"
    },
    {
      data: "jml",
      render: function(data, type, row, meta){
        return parseInt((row.jml == null) ? 0 : row.jml).toLocaleString({maximumFractionDigits: 2}) + " KG";;
      },
      className: "text-right"
    },
    {
      data: "biaya_muat",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(row.biaya_muat).toLocaleString({maximumFractionDigits: 2});;
      },
      className: "text-right"
    },
    {
      data: "biaya_angkut",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(row.biaya_angkut).toLocaleString({maximumFractionDigits: 2});;
      },
      className: "text-right"
    },
    {
      data: "total_biaya",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(row.total_biaya).toLocaleString({maximumFractionDigits: 2});;
      },
      className: "text-right"
    }
  ],
  initComplete: function(){
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
    function refreshTable(){
      var tgl_awal = $("#dtpAwal").datepicker("getDate");
      var tgl_akhir = $("#dtpAkhir").datepicker("getDate");
      tgl_awal = formatTgl(tgl_awal);
      tgl_akhir = formatTgl(tgl_akhir);
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblListPupuk").DataTable().ajax.url(js_base_url + "Biaya_muat_angkut_pupuk/getRekapBiayaMuatAngkutPupuk?tahun_giling=" + tahun_giling +
      "&tgl_awal=" + tgl_awal + "&tgl_akhir=" + tgl_akhir).load();
    }
    //console.log($("#tblListPupuk").DataTable().rowGroup.enable());
    //$("div.dtpTglAwal").html("<input type='text' class='form-control' data-provide='datepicker' style='width: 200px; margin-left: 10px;'>");
    $("div.dtpTglAwal").html("<input autocomplete='off' type='text' class='form-control text-center' placeholder='Tanggal Awal' id='dtpAwal' style='width: 120px; margin-left: 10px;'>");
    $("#dtpAwal").datepicker({
      format: "dd-mm-yyyy"
    });
    $("div.dtpTglAkhir").html("<input autocomplete='off' type='text' class='form-control text-center' placeholder='Tanggal Akhir' id='dtpAkhir' style='width: 120px; margin-left: 10px; margin-bottom: 10px'>");
    $("#dtpAkhir").datepicker({
      format: "dd-mm-yyyy"
    });
    $("div.btnSearch").html("<button style='margin-left: 10px; width: 100px;' id='btnSearch' type='button' class='btn btn-outline-secondary'>Tampilkan</button>");
    $("#btnSearch").on("click", function(){
      refreshTable();
    })
    $("div.btnBuatPBMA").html("<button style='margin-left: 10px; width: 150px;' id='btnBuatPBMA' type='button' class='btn btn-outline-secondary'>Buat PBMA</button>");
    $("#btnSearch").on("click", function(){
      refreshTable();
    });
  },
  language: {
    "search": ""
  },
  footerCallback: function (row, data, start, end, display){
    var api = this.api(), data;
    var intRupiah = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    var intKg = function(i){
      return typeof i === 'string' ? i.replace(/[\KG,]/g, '')*1 : typeof i === 'number' ? i : 0;
    }
    totalBiaya = api.column(12).data().reduce( function (a, b) {
        return intRupiah(a) + intRupiah(b);
    },0);
    totalPupuk = api.column(9).data().reduce(function (a,b){
      return intKg(a) + intKg(b);
    }, 0);
    $(api.column(9).footer()).html('<font color="white" size="3">' + totalPupuk.toLocaleString({maximumFractionDigits: 2}) + ' KG' + '</font>');
    $(api.column(12).footer()).html( '<font color="white" size="3">' + 'Rp '+ totalBiaya.toLocaleString({maximumFractionDigits: 0}) + '</font>');
    /*
    $(table.table().footer()).html('<tr><th class="w-1"></th><th><font color="white" size="3">TOTALE</font></th><th></th><th></th><th></th><th></th><th></th>'+
    '<th></th><th></th><th></th><th></th><th></th><th></th></tr>');
    */
    //$('tr:eq(1) th:eq(12)', api.table().footer()).html('<font color="white" size="3">' + 'Rp '+ totalBiaya.toLocaleString({maximumFractionDigits: 0}) + '</font>');
  }
});
