
function approve(id_dokumen){
  $.ajax({
    url: js_base_url + "List_pbma/validasiDokumen",
    dataType: "text",
    type: "POST",
    data: "id_dokumen=" + id_dokumen,
    success: function(response){
      if (response = "SUCCESS"){
        tahun_giling = parseInt($("#tahun_giling").val()) || 0;
        $("#tblListPbma").DataTable().ajax.url(js_base_url + "List_pbma/getAllPbma?tahun_giling=" + tahun_giling).load();
        alert("Dokumen berhasil divalidasi!");
      }
    }
  });
}

function approveAskep(id_dokumen){
  $.ajax({
    url: js_base_url + "List_pbma/validasiDokumenAskep",
    dataType: "text",
    type: "POST",
    data: "id_dokumen=" + id_dokumen,
    success: function(response){
      if (response = "SUCCESS"){
        tahun_giling = parseInt($("#tahun_giling").val()) || 0;
        $("#tblListPbma").DataTable().ajax.url(js_base_url + "List_pbma/getAllPbma?tahun_giling=" + tahun_giling).load();
        alert("Dokumen berhasil divalidasi!");
      }
    }
  });
}

function cancel(id_dokumen){
  if(confirm("Anda yakin akan membatalkan dokumen ini?")){
    $.ajax({
      url: js_base_url + "List_pbma/batalkanDokumen",
      dataType: "text",
      type: "POST",
      data: "id_dokumen=" + id_dokumen,
      success: function(response){
        if (response = "SUCCESS"){
          tahun_giling = parseInt($("#tahun_giling").val()) || 0;
          $("#tblListPbma").DataTable().ajax.url(js_base_url + "List_pbma/getAllPbma?tahun_giling=" + tahun_giling).load();
          alert("Dokumen berhasil dibatalkan!");
        }
      }
    });
  }
}


$("#tblListPbma").DataTable({
  bFilter: false,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  autoWidth: false,
  ajax: {
    url: js_base_url + "List_pbma/getAllPbma?tahun_giling=0" ,
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
    {
      data: "total",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(row.total).toLocaleString({maximumFractionDigits: 0});
      },
      className: "text-right"
    },
    {
      data: "catatan",
      className: "text-center"
    },
    {
      data: "",
      render: function(data, type, row, meta){
        if((row.tgl_validasi_bagian == null && row.priv_level == "Asisten Bagian") || (row.priv_level == "Kepala Sub Bagian" && row.tgl_validasi_kasubbag == null)){
          return "<span class='tag tag-red'>Belum Divalidasi</span>";
        } else {
          if(row.tgl_validasi_bagian == null && row.tgl_validasi_kasubbag == null){
            return "<span class='tag tag-red'>Belum Divalidasi</span>";
          } else {
            if(row.tgl_validasi_bagian == null || row.tgl_validasi_kasubbag == null){
              return "<span class='tag tag-orange'>Validasi Belum Lengkap</span>";
            }
          }
        }
        return "<span class='tag tag-green'>Sudah Divalidasi</span>";
      },
      className: "text-center"
    },
    {
      render: function(data, type, row, meta){
        var buttonDetail = '<a style="width: 80px" class="btn btn-sm btn-gray" href="Cetak_pbma?id_pbma=' + row.id_dokumen + '">Lihat Detail</a> ';
        if(row.priv_level == "Asisten Bagian"){
          var buttonApproval = '<button style="width: 80px" class="btn btn-sm btn-primary" onclick = approve(' + row.id_dokumen +') >Setuju</button> ' +
          '<button style="width: 80px" type="button" class="btn btn-sm btn-red" onclick = cancel(' + row.id_dokumen + ')>Batalkan</button>';
          if(row.tgl_validasi_bagian == null){
            return buttonDetail + buttonApproval;
          } else {
            return buttonDetail;
          }
        } else {
          if(row.priv_level == "Kepala Sub Bagian"){
            var buttonApproval = '<button style="width: 80px" class="btn btn-sm btn-primary" onclick = approveAskep(' + row.id_dokumen +') >Setuju</button> ' +
            '<button style="width: 80px" type="button" class="btn btn-sm btn-red" onclick = cancel(' + row.id_dokumen + ')>Batalkan</button>';
            if(row.tgl_validasi_kasubbag == null){
              return buttonDetail + buttonApproval;
            } else {
              return buttonDetail;
            }
          }
        }
        return buttonDetail;
      },
      className: "text-left"
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "1px",
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
    $("#tahun_giling").on("change", function(){
      tahun_giling = parseInt($("#tahun_giling").val()) || 0;
      $("#tblListPbma").DataTable().ajax.url(js_base_url + "List_pbma/getAllPbma?tahun_giling=" + tahun_giling).load();
    })
  },
  footerCallback: function (row, data, start, end, display){
    var api = this.api(), data;
    var intRupiah = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    totalBiaya = api.column(3).data().reduce( function (a, b) {
        return intRupiah(a) + intRupiah(b);
    },0);
    $(api.column(3).footer()).html('<font color="white" size="3">' + 'Rp ' + totalBiaya.toLocaleString({maximumFractionDigits: 0}) + '</font>');
  },
  language: {
    "search": ""
  }
});
