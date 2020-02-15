$("#tblTebuMasukSkrg").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: 'tp',
  ajax: {
    //url: "http://simpgbuma.ptpn7.com/index.php/dashboardtimbangan/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    url: "http://localhost/index.php/api_buma/getDataTimbang?kode_blok=1230940&tgl_timbang=2019-06-24",
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "no_spat"},
    {data: "tgl_timbang"},
    {data: "no_angkutan"},
    {
      data: "bruto",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " KG";
      },
      className: "text-right"
    },
    {
      data: "tara",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " KG";
      },
      className: "text-right"
    },
    {
      data: "netto",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " KG";
      },
      className: "text-right"
    },
    {
      data: "RAFAKSI",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " % (" + Math.round((row.RAFAKSI/100)*row.netto).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " KG)";
      },
      className: "text-right"
    },
    {
      data: "RAFAKSI",
      render: function(data, type, row, meta){
        return parseInt(Math.round(((100-row.RAFAKSI)/100)*row.netto)).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " KG";
      },
      className: "text-right"
    }
  ],
  footerCallback: function (row, data, start, end, display){
    var api = this.api(), data;
    var intRupiah = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    totalRupiah = api.column(5).data().reduce( function (a, b) {
        return intRupiah(a) + intRupiah(b);
    },0);
    $(api.column(5).footer()).html('<font size="3" color="white">' + "Rp " + totalRupiah.toLocaleString({maximumFractionDigits: 0}) + ' </font>');
  }
});
