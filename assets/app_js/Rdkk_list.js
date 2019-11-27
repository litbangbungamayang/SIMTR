$("#tblList").DataTable({
  bFilter: true,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  ajax: {
    url: js_base_url + "Rdkk_list/getAllKelompok",
    dataSrc: ""
  },
  columns : [
    {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
    {data: "nama_kelompok"},
    {data: "no_kontrak"},
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
        return '<form action="Rdkk_view" method="get"><button type="submit" class="btn btn-info btn-sm" name="idKelompok" value="'+row.id_kelompok+'" >Lihat Data</button></form>'
      }
    }
  ],
  initComplete: function(){
    $(".dataTables_filter input[type=\"search\"]").css({
      "width": "200px",
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
});
