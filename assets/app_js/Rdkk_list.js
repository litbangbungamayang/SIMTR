var dialogAddPermintaanPupuk = $("#dialogAddPermintaanPupuk");
var btnSimpanPermintaanPupuk  =$("#btnSimpanPermintaanPupuk");
var arrayPermintaanPupuk = [];
var arrayBahan = [];
var arrayPermintaanPupukMaks = [];
var arrayPermintaanPupukExisting = [];
var arrayStokBahan = [];
var selectedKelompok;
var objTransaksi = function(id_bahan, id_kelompok, kode_transaksi, tahun_giling, nama_bahan, kuanta_req, luas_aplikasi, satuan, rupiah){
  var obj = {};
  obj.id_bahan = id_bahan;
  obj.id_kelompok = id_kelompok;
  obj.kode_transaksi = kode_transaksi;
  obj.kuanta = Math.round(kuanta_req/50)*50;
  obj.tahun_giling = tahun_giling;
  obj.nama_bahan = nama_bahan;
  obj.kuanta_req = kuanta_req;
  obj.rupiah = Math.round(Math.round(kuanta_req/50)*50*rupiah);
  obj.luas_aplikasi = luas_aplikasi;
  obj.satuan = satuan;
  return obj;
}
var cbxJenisBahan = $("#jenis_pupuk");
var luasAplikasi = $("#luas_aplikasi");
var btnTambahPupuk = $("#btnTambahPupuk");

$("#luas_aplikasi").bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9. ]/g,""));
});
$("#luas_aplikasi").on("blur", function(){
  if ($(this).val() != ""){
    $(this).val(parseFloat($(this).val()));
    var luas_maks = parseFloat($("#luas").val().replace(/[^0-9. ]/g,""));
    (parseFloat($(this).val()) > luas_maks) ? $(this).val(luas_maks) : "";
  }
})

btnSimpanPermintaanPupuk.on("click", function(){
  if (arrayPermintaanPupuk.length > 0){
    $.ajax({
      url: js_base_url + "Rdkk_list/getArrayPermintaanPupuk",
      dataType: "text",
      type: "POST",
      data: "pupuk=" + JSON.stringify(arrayPermintaanPupuk),
      success: function(response){
        alert(response);
        if (response = "Data pengajuan telah tersimpan!"){
          $("#tblPupuk").DataTable().ajax.url(js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok=" + selectedKelompok.id_kelompok).load(function(callback){
            arrayPermintaanPupukExisting = callback;
          });
          arrayPermintaanPupuk = [];
          refreshTablePermintaan();
        }
      }
    });
  }
})

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
    {data: "button",
      render: function(data, type, row, meta){
        return actionButtonView(row.id_kelompok);
      }
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
      $("#tblList").DataTable().ajax.url(js_base_url + "Rdkk_list/getKelompokByTahun?tahun_giling=" + tahun_giling).load();
    });
  },
  language: {
    "search": ""
  }
});

dialogAddPermintaanPupuk.on("hide.bs.modal", function(){
  resetFeedbackAddPupuk();
})

function addPupuk(id_kelompok){
  dialogAddPermintaanPupuk.modal("toggle");
  $.ajax({
    url: js_base_url + "Landing/loadDataGudang",
    dataType: "json",
    type: "GET",
    success: function(response){
      arrayStokBahan = response;
      console.log(arrayStokBahan);
    }
  });
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    type: "GET",
    dataType: "json",
    data: {
      id_kelompok: id_kelompok
    },
    success: function(response){
      selectedKelompok = response;
      $("#namaKelompok").val(response.nama_kelompok);
      $("#luas").val(parseFloat(response.luas).toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha");
      $("#tblPupuk").DataTable().ajax.url(js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok=" + response.id_kelompok).load(function(callback){
        arrayPermintaanPupukExisting = callback;
      });
      $.ajax({
        url: js_base_url + "Admin_bahan/getBahanByJenisTahunGiling",
        type: "GET",
        dataType: "json",
        data: {
          jenis_bahan: "PUPUK",
          tahun_giling: response.tahun_giling
        },
        success: function(response){
          $("#jenis_pupuk").selectize();
          $("#jenis_pupuk").selectize()[0].selectize.clear();
          $("#jenis_pupuk").selectize()[0].selectize.clearOptions();
          $("#jenis_pupuk").selectize()[0].selectize.load(function(callback){
            callback(response);
            arrayBahan = response;
            //perhitungan permintaan pupuk maksimal
            arrayPermintaanPupukMaks = [];
            for (i = 0; i < arrayBahan.length; i++){
              arrayPermintaanPupukMaks.push({
                id_bahan: arrayBahan[i].id_bahan,
                maks: Math.round((arrayBahan[i].dosis_per_ha*selectedKelompok.luas)/50)*50
              });
            }
            console.log("Limit :");
            console.log(arrayPermintaanPupukMaks);
            for (i = 0; i < arrayPermintaanPupukExisting.length; i++){
              var jmlEksisting = arrayPermintaanPupukExisting[i].kuanta;
              var indexBahan = arrayPermintaanPupukMaks.findIndex(x => x.id_bahan === arrayPermintaanPupukExisting[i].id_bahan);
              arrayPermintaanPupukMaks[indexBahan].maks = parseInt(arrayPermintaanPupukMaks[indexBahan].maks) - parseInt(jmlEksisting);
            }
          });
        }
      });
    },
    error: function(textStatus){
      console.log(textStatus);
    }
  })
}

function resetFeedbackAddPupuk(){
  luasAplikasi.removeClass("is-invalid");
  cbxJenisBahan.removeClass("is-invalid");
  $("#card_tblTransaksi").addClass("card-collapsed");
  luasAplikasi.val("");
  cbxJenisBahan.val("");
  arrayPermintaanPupuk = [];
  refreshTablePermintaan();
}

function validasiForm(){
  var idBahan = cbxJenisBahan.selectize()[0].selectize.getValue();
  if (idBahan != "" && luasAplikasi.val() != ""){
    resetFeedbackAddPupuk;
    return true;
  } else {
    (idBahan == "") ? cbxJenisBahan.addClass("is-invalid") : "";
    (luasAplikasi.val() == "") ? luasAplikasi.addClass("is-invalid") : "";
  }
  return false;
}

btnTambahPupuk.on("click", function(){
  if (validasiForm()){
    var hargaSatuan;
    var luasValue = parseFloat(luasAplikasi.val());
    var bahanSelected = cbxJenisBahan.selectize()[0].selectize.options[cbxJenisBahan.selectize()[0].selectize.getValue()];
    var dosisBahan = bahanSelected.dosis_per_ha;
    var kuanta_req = dosisBahan * luasValue;
    // OBJECT : function(id_bahan, id_kelompok, kode_transaksi, tahun_giling, nama_bahan, kuanta_req, luas_aplikasi, satuan, rupiah)
    $.ajax({
      url: js_base_url + "Rdkk_list/getHargaSatuan",
      data: {id_bahan: bahanSelected.id_bahan},
      dataType: "json",
      async: false,
      type: "GET",
      success: function(response){
        hargaSatuan = response[0].harga_unit;
      }
    });
    var permintaanBaru = objTransaksi(bahanSelected.id_bahan, selectedKelompok.id_kelompok, 2,
      selectedKelompok.tahun_giling, bahanSelected.nama_bahan, kuanta_req, luasValue, bahanSelected.satuan, hargaSatuan);
    var indexBahan = arrayPermintaanPupukMaks.findIndex(x => x.id_bahan === permintaanBaru.id_bahan);
    var indexStokBahan = arrayStokBahan.findIndex(x => x.id_bahan === permintaanBaru.id_bahan);
    console.log("ID Permintaan Baru = " + permintaanBaru.id_bahan);
    console.log("Array Stok Bahan = ");
    console.log(arrayStokBahan);
    if (indexStokBahan > -1){
      if (permintaanBaru.kuanta <= arrayStokBahan[indexStokBahan].total_kuanta){
        if (permintaanBaru.kuanta <= arrayPermintaanPupukMaks[indexBahan].maks){
          arrayPermintaanPupukMaks[indexBahan].maks = arrayPermintaanPupukMaks[indexBahan].maks - permintaanBaru.kuanta;
          arrayPermintaanPupuk.push(permintaanBaru);
          cbxJenisBahan.selectize()[0].selectize.setValue("");
          luasAplikasi.val("");
          refreshTablePermintaan();
          console.log(permintaanBaru);
        } else {
          alert("Permintaan bahan " + permintaanBaru.nama_bahan + " tidak bisa melebihi " +
            parseInt(arrayPermintaanPupukMaks[indexBahan].maks).toLocaleString({maximumFractionDigits:2}) + " kg atau " +
            parseFloat(arrayPermintaanPupukMaks[indexBahan].maks/bahanSelected.dosis_per_ha).toLocaleString({maximumFractionDigits:2}) + " Ha!");
        }
      } else {
        alert("Stok bahan " + permintaanBaru.nama_bahan + " tidak mencukupi! Diminta = " + parseInt(permintaanBaru.kuanta).toLocaleString({maximumFractionDigits: 2, minimumFractionDigits: 2}) + " kg," +
          " Bahan tersedia = " + parseInt(arrayStokBahan[indexStokBahan].total_kuanta).toLocaleString({maximumFractionDigits:2}) + " kg");
      }
    } else {
      alert("Stok bahan " + permintaanBaru.nama_bahan + " belum diinput!");
    }
  }
})

function refreshTablePermintaan(){
  tblPermintaanPupuk = $("#tblPermintaanPupuk").DataTable();
  tblPermintaanPupuk.clear();
  tblPermintaanPupuk.rows.add(arrayPermintaanPupuk);
  tblPermintaanPupuk.draw();
}

$("#jenis_pupuk").selectize({
  valueField: "id_bahan",
  labelField: "nama_bahan",
  sortField: "nama_bahan",
  searchField: "nama_bahan",
  maxItems: 1,
  create: false,
  placeholder: "Pilih jenis pupuk",
  options: []
});

function actionButtonView(id_kelompok){
  return  '<div class="btn-group"><button style="width: 80px" type="button" class="btn btn-secondary btn-sm btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
          '<i class="fe fe-settings mr-2"></i> Opsi' +
          '</button>' +
          '<div class="dropdown-menu dropdown-menu-right">' +
            '<a class="dropdown-item" href="Rdkk_view?id_kelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat Data Kelompok</a>' +
            '<div class="dropdown-divider"></div>' +
            '<a class="dropdown-item" href="#" onclick="addPupuk(' + id_kelompok + ')"><i class="fe fe-sunset"></i> Buat Permintaan Pupuk</a>' +
            '<a class="dropdown-item" href="#"><i class="fe fe-feather"></i> Buat Permintaan Perawatan</a>' +
          '</div></div>';
}

$("#tblPupuk").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: 'tp',
  ajax: {
    url: js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok="+0,
    dataSrc: ""
  },
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "no_transaksi"},
    {data: "tgl_transaksi"},
    {data: "nama_bahan"},
    {
      data: "kuanta",
      render: function(data, type, row, meta){
        return parseInt(data).toLocaleString(undefined, {maximumFractionDigits:2}) + " " +row.satuan;
      },
      className: "text-right"
    },
    {
      data: "button",
      render: function(data, type, row, meta){
        return '<div class="btn btn-outline-primary btn-sm" name="hapus" id="cetakAu58" onclick=""><i class="fe fe-printer"></i></div>'
      },
      className: "text-center"
    }
  ],
  initComplete: function(){

  }
});

$("#tblPermintaanPupuk").DataTable({
  bFilter: false,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  data: arrayPermintaanPupuk,
  columns : [
    {
      data: "no",
      render: function(data, type, row, meta){
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    },
    {data: "nama_bahan"},
    {
      data: "luas_aplikasi",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits:2, maximumFractionDigits:2}) + " Ha";
      },
      className: "text-right"
    },
    {
      data: "kuanta_req",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits:2, maximumFractionDigits:2}) + " " +row.satuan;
      },
      className: "text-right"
    },
    {
      data: "kuanta",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits:2, maximumFractionDigits:2}) + " " +row.satuan;
      },
      className: "text-right"
    },
    {
      data: "rupiah",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString({maximumFractionDigits:2});
      },
      className: "text-right"
    },
    {
      data: "button",
      render: function(data, type, row, meta){
        return '<div class="btn btn-danger btn-sm" name="hapus" id="hapusPermintaan" onclick="hapusPermintaan('+meta.row+')"><i class="fe fe-trash-2"></i></div>'
      }
    }
  ]
});

function hapusPermintaan(index){
  var indexBahan = arrayPermintaanPupukMaks.findIndex(x => x.id_bahan === arrayPermintaanPupuk[index].id_bahan);
  arrayPermintaanPupukMaks[indexBahan].maks = arrayPermintaanPupukMaks[indexBahan].maks + arrayPermintaanPupuk[index].kuanta;
  arrayPermintaanPupuk.splice(index,1);
  refreshTablePermintaan();
}
