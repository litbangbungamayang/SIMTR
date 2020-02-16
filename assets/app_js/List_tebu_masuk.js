$("#tblTebuMasukSkrg").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: 'tp',
  ajax: {
    //url: "http://simpgbuma.ptpn7.com/index.php/dashboardtimbangan/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    //url: "http://localhost/index.php/api_buma/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    url: "http://localhost/simpg/index.php/api_buma/getDataTimbangPeriodeGroup?tgl_timbang_awal=2010-01-01&tgl_timbang_akhir=2030-01-01",
    //url: "",
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "kode_blok"},
    {
      data: "deskripsi_blok"
    },
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
    }
  ]
});
