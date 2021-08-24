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

function approve(id_dokumen){
  $.ajax({
    url: js_base_url + "List_bon_perawatan/validasiDokumen",
    dataType: "text",
    type: "POST",
    data: "id_dokumen=" + id_dokumen,
    success: function(response){
      if (response = "SUCCESS"){
        tahun_giling = parseInt($("#tahun_giling").val()) || 0;
        $("#tblListPpk").DataTable().ajax.url(js_base_url + "List_bon_perawatan/getAllPpk?tahun_giling=0").load();
        alert("Dokumen berhasil divalidasi!");
      }
    }
  });
}

function approveAskep(id_dokumen){
  $.ajax({
    url: js_base_url + "List_bon_perawatan/validasiDokumenAskep",
    dataType: "text",
    type: "POST",
    data: "id_dokumen=" + id_dokumen,
    success: function(response){
      if (response = "SUCCESS"){
        tahun_giling = parseInt($("#tahun_giling").val()) || 0;
        $("#tblListPpk").DataTable().ajax.url(js_base_url + "List_bon_perawatan/getAllPpk?tahun_giling=0").load();
        alert("Dokumen berhasil divalidasi!");
      }
    }
  });
}

$("#tblListBasteb").DataTable({
  bFilter: true,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  autoWidth: false,
  ajax: {
    url: js_base_url + "List_ba_tebang/getAllBasteb?tahun_giling=0" ,
    dataSrc: ""
  },
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling">f>tpl',
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + 1;
      }
    },
    {
      data: "no_dokumen"
    },
    {
      data: "tgl_buat",
      className: "text-center"
    },
    { data: "nama_kelompok" },
    { data: "no_kontrak" },
    { data: "nama_wilayah"},
    {
      render: function(data, type, row, meta){
        var buttonDetail = '<form id="view_basteb" action="' + js_base_url + "Aff_kebun" + '" method="POST"><input type="hidden" name="id_kelompok" value="'+row.id_kelompok+'"><input type="hidden" name="kode_blok" value="'+row.kode_blok+'"><a class="btn btn-sm btn-cyan" href="#" onclick="document.getElementById(\'view_basteb\').submit();" title="Lihat Detail"><i class="fe fe-book-open"></i></a></form> ';
        return buttonDetail;
      },
      className: "text-left"
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
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblListBasteb").DataTable().ajax.url(js_base_url + "List_ba_tebang/getAllBasteb?tahun_giling=" + tahun_giling).load();
    }
    $("#tahun_giling").on("change", function(){
      refreshTable();
    });
  },
  footerCallback: function (row, data, start, end, display){
    var api = this.api(), data;
    var intRupiah = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    totalRupiah = api.column(5).data().reduce( function (a, b) {
        return intRupiah(a) + intRupiah(b);
    },0);
    $(api.column(5).footer()).html('<font color="white" size="3">' + "Rp " + totalRupiah.toLocaleString({maximumFractionDigits: 0}) + ' </font>');
  },
  language: {
    "search": ""
  }
});
