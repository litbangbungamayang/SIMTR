var arrayDataTebu;

function formatTgl(dateObj){
  if(dateObj != null){
    return dateObj.getFullYear() + "-" + ("0" + (dateObj.getMonth()+1)) + "-" + ("0" + dateObj.getDate()).slice(-2);
  }
  return "";
}

function formatTglStr(dateObj){
  if(dateObj != null){
    return ("0" + dateObj.getDate()).slice(-2) + "-" + ("0" + (dateObj.getMonth()+1)) + "-" + dateObj.getFullYear();
  }
  return "";
}

$("#btnTransferTma").on("click", function(){
  console.log($("#tblTebuMasukSkrg").DataTable().rows().data());
})

$.ajax({
  //url: "http://localhost/simpg/index.php/api_buma/getDataTimbangPeriodeGroup?tgl_timbang_awal=2010-01-01&tgl_timbang_akhir=2030-01-01&afd=" + id_afd,
  url: js_base_url + "List_tebu_masuk/getApiDataTimbangPeriodeGroup",
  dataType: "json",
  type: "get",
  data:{tgl_timbang_awal: "2010-01-01", tgl_timbang_akhir: "2030-01-01"},
  success: function(response){
    arrayDataTebu = response;

  }
})

$("#tblTebuMasukSkrg").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling"><"dtpTglAwal"><"dtpTglAkhir">f>tpl',
  ajax: {
    //url: "http://simpgbuma.ptpn7.com/index.php/dashboardtimbangan/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    //url: "http://localhost/index.php/api_buma/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    //url: "http://localhost/simpg/index.php/api_buma/getDataTimbangPeriodeGroup?tgl_timbang_awal=2010-01-01&tgl_timbang_akhir=2030-01-01&afd=" + id_afd,
    url: js_base_url + "List_tebu_masuk/getApiDataTimbangPeriodeGroup?tgl_timbang_awal=2010-01-01&tgl_timbang_akhir=2030-01-01",
    dataSrc: ""
  },
  //data: arrayDataTebu,
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "kode_blok"},
    {data: "no_kontrak"},
    {data: "nama_kelompok"},
    {data: "nama_wilayah"},
    {
      data: "netto",
      render: function(data, type, row, meta){
        return parseFloat(data/1000).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " TON";
      },
      className: "text-right"
    },
    {
      data: "tgl_timbang",
      render: function(data, type, row, meta){
        return data;
      },
      className: "text-right"
    },
    {
      data: "biaya",
      render: function(data, type, row, meta){
        if(data == null){
          return "<span class='tag tag-red'>Data Biaya TMA belum ada!</span>";
        } else {
          return "Rp" + parseInt(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2});
        }
      },
      className: "text-center"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "250px",
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
    $("div.cbxTahunGiling").html('<select style="width: 150px; margin-bottom: 10px; margin-left: 10px" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label style="margin-left: 10px; height: 37px; margin-bottom: 10px; display: block; background: red"></label>');
    $('#tahun_giling').selectize({create: false, sortField: 'value'});
    function refreshTable(){
      var tgl_awal = $("#dtpAwal").datepicker("getDate");
      var tgl_akhir = $("#dtpAkhir").datepicker("getDate");
      if(tgl_awal != null && tgl_akhir != null){
        tgl_awal = formatTgl(tgl_awal);
        tgl_akhir = formatTgl(tgl_akhir);
        tahun_giling = parseInt($("#tahun_giling").val()) || 0;
        $("#tblTebuMasukSkrg").DataTable().ajax.url(js_base_url + "List_tebu_masuk/getApiDataTimbangPeriodeGroup?tgl_timbang_awal=" + tgl_awal +"&tgl_timbang_akhir=" + tgl_akhir).load();
        console.log("triggered");
      }
    }
    $("#tahun_giling").on("change", function(){
      refreshTable();
    });
    $("div.dtpTglAwal").html("<input autocomplete='off' type='text' class='form-control text-center' placeholder='Tanggal Awal' id='dtpAwal' style='width: 120px; margin-left: 10px;'>");
    $("#dtpAwal").datepicker({
      format: "dd-mm-yyyy"
    });
    $("div.dtpTglAkhir").html("<input autocomplete='off' type='text' class='form-control text-center' placeholder='Tanggal Akhir' id='dtpAkhir' style='width: 120px; margin-left: 10px; margin-bottom: 10px'>");
    $("#dtpAkhir").datepicker({
      format: "dd-mm-yyyy"
    });
    $("#dtpAwal").on("change", function(){
      refreshTable();
    });
    $("#dtpAkhir").on("change", function(){
      refreshTable();
    })
  }
});
