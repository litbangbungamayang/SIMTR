var frm_basteb = $("#frm_basteb");
var frm_penjualanGula = $("#frm_penjualanGula");
var dialogAddPenjualan = $("#dialogAddPenjualan");

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

function konfirmasiPenjualanGula(){
  if(parseInt($("#tahun_giling").val()) != 0){
    frm_basteb.submit();
  }
}

function simpanPenjualanGula(){
  frm_penjualanGula.submit();
}

$("#tblListBasteb").DataTable({
  bFilter: true,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  autoWidth: false,
  ajax: {
    url: js_base_url + "Surat_penjualan_gula/getAllBasteb?tahun_giling=0" ,
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
      render: function(data, type, row, meta){
        var buttonDetail = "";
        if(row.gula_terjual < row.kg_gula_90 ){
          buttonDetail = '<input type="checkbox" name="id_dokumen[]" value="'+row.id_dokumen+'"> ';
        }
        return buttonDetail;
      },
      className: "text-left"
    },
    { data: "nama_kelompok" },
    { data: "no_kontrak" },
    {
      data: "ton_tebu_hitung",
      className: "text-right"
    },
    {
      data: "kg_gula_90",
      className: "text-right"
    },
    {
      data: "gula_terjual",
      className: "text-right"
    },
    {
      data: "kg_tetes_ptr",
      className: "text-right"
    },
    {
      data: "tetes_terjual",
      className: "text-right"
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
