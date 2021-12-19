var dialogAddPermintaanPupuk = $("#dialogAddPermintaanPupuk");
var dialogAddPerawatan = $("#dialogAddPerawatan");
var dialogAddPermintaanTma = $("#dialogAddPermintaanTma");
var dialogAddPermintaanBibit = $("#dialogAddPermintaanBibit");
var btnSimpanPermintaanPupuk = $("#btnSimpanPermintaanPupuk");
var btnSimpanPermintaanPerawatan = $("#btnSimpanPermintaanPerawatan");
var formKonfirmasi = $("#formKonfirmasi");
var formViewBeritaAcaraAff = $("#formViewBeritaAcaraAff");
var formInputIdKelompok = document.getElementById("id_kelompok");
var formInputKodeBlok = document.getElementById("kode_blok");
var formInputIdKelompokView = document.getElementById("id_kelompok_v");
var formInputKodeBlokView = document.getElementById("kode_blok_v");
var selectedKelompok;
var arrayPermintaanPupuk = [];
var arrayAktivitas = [];
var arrayPermintaanPerawatan = [];
var arrayPermintaanPerawatanMaks = [];
var arrayBahan = [];
var arrayPermintaanPupukMaks = [];
var arrayPermintaanPupukExisting = [];
var arrayStokBahan = [];
var selectedKelompok;
var objTransaksi = function(id_aktivitas, id_bahan, id_kelompok, kode_transaksi, tahun_giling, nama_bahan, kuanta, luas_aplikasi, satuan, rupiah){
  var obj = {};
  obj.id_aktivitas = id_aktivitas;
  obj.id_bahan = id_bahan;
  obj.id_kelompok = id_kelompok;
  obj.kode_transaksi = kode_transaksi;
  obj.kuanta = kuanta;
  obj.tahun_giling = tahun_giling;
  obj.nama_bahan = nama_bahan;
  obj.rupiah = Math.round(kuanta*rupiah);
  obj.luas_aplikasi = luas_aplikasi;
  obj.satuan = satuan;
  return obj;
}
var cbxJenisBahan = $("#jenis_pupuk");
var cbxNamaGudang = $("#nama_gudang");
var luasAplikasi = $("#luas_aplikasi");
var btnTambahPupuk = $("#btnTambahPupuk");
var btnTambahPerawatan = $("#btnTambahPerawatan");
var perawatan_biaya = $("#perawatan_biaya");
var perawatan_jmlBiaya = $("#perawatan_jmlBiaya");
var perawatan_luasDiminta = $("#perawatan_luasDiminta");
var perawatan_luas = $("#perawatan_luas");
var cbxAktivitas = $("#jenis_aktivitas");
var namaKelompokBibit = $("#namaKelompokBibit");
var luasBakuBibit = $("#luasBakuBibit");
var varBibit = $("#varBibit");
var asalBibit = $("#asalBibit");
var biayaBibit = $("#biayaBibit");
var luasBibitDiminta = $("#luasBibitDiminta");
var totalBiayaBibit = $("#totalBiayaBibit");
var btnSimpanPermintaanBibit = $("#btnSimpanPermintaanBibit");
var biaya_aktivitas;
var selectedRowData;


/************************** OBJECT METHODS SECTION *****************************/

$("#luas_aplikasi").bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9. ]/g,""));
});

$("#luas_aplikasi").on("blur", function(){
  if ($(this).val() != ""){
    $(this).val(parseFloat($(this).val()));
    var luas_maks = parseFloat($("#luas").val().replace(/[^0-9. ]/g,""));
    (parseFloat($(this).val()) > luas_maks) ? $(this).val(luas_maks) : "";
  }
});

$("#perawatan_luasDiminta").bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9. ]/g,""));
});
$("#perawatan_luasDiminta").on("blur", function(){
  if ($(this).val() != "" && cbxAktivitas.selectize()[0].selectize.getValue() != ""){
    $(this).val(parseFloat($(this).val()));
    var luas_maks = parseFloat($("#perawatan_luas").val().replace(/[^0-9. ]/g,""));
    (parseFloat($(this).val()) > luas_maks) ? $(this).val(luas_maks) : "";
    var luas_req = parseFloat($(this).val());
    var harga = parseFloat($("#perawatan_biaya").val().replace(/[^0-9. ]/g,""));
    $("#perawatan_jmlBiaya").val("Rp " + (luas_req*harga).toLocaleString({maximumFractionDigits: 2}));
  } else {
    $(this).val("");
  }
});

asalBibit.on("change", function(){
  if ($(this).val() != ""){
    id_aktivitas_selected = asalBibit.selectize()[0].selectize.getValue();
    biaya_aktivitas = parseInt(asalBibit.selectize()[0].selectize.options[id_aktivitas_selected].biaya);
    biayaBibit.val("Rp " + biaya_aktivitas.toLocaleString({maximumFractionDigits: 2}));
    luasBibitDiminta.val("");
    totalBiayaBibit.val("");
  }
});

luasBibitDiminta.bind("keyup blur", function(){
  $(this).val($(this).val().replace(/[^0-9. ]/g,""));
});

luasBibitDiminta.on("blur", function(){
  if ($(this).val() != "" && asalBibit.selectize()[0].selectize.getValue != ""){
    $(this).val(parseFloat($(this).val()));
    var luas_maks = parseFloat(luasBakuBibit.val().replace(/[^0-9. ]/g,""));
    (parseFloat($(this).val()) > luas_maks) ? $(this).val(luas_maks) : "";
    totalBiayaBibit.val("Rp " + parseInt(biaya_aktivitas*$(this).val()).toLocaleString({maximumFractionDigits: 0}));
  } else {
    $(this).val("");
  }
});


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
          $("#tblPupuk").DataTable().ajax.url(js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok=" + selectedKelompok.id_kelompok).load();
          arrayPermintaanPupuk = [];
          arrayPermintaanPupukMaks = [];
          console.log("Limit After insert :");
          console.log(arrayPermintaanPupukMaks);
          refreshTablePermintaan();
          resetFeedbackAddPupuk();
          dialogAddPermintaanPupuk.modal("toggle");
        }
      }
    });
  }
});

btnSimpanPermintaanPerawatan.on("click", function(){
  if (arrayPermintaanPerawatan.length > 0){
    $.ajax({
      url: js_base_url + "Rdkk_list/getArrayPermintaanPerawatan",
      dataType: "json",
      type: "POST",
      data: "perawatan=" + JSON.stringify(arrayPermintaanPerawatan),
      success: function(response){
        if(response.length > 0){
          for(i = 0; i < response.length; i++){
            alert(response[i]);
          }
        }
        arrayPermintaanPerawatanMaks = [];
        arrayPermintaanPerawatan = [];
        $("#tblPerawatan").DataTable().ajax.url(js_base_url + "Transaksi_aktivitas/getTransaksiAktivitasByIdKelompok?id_kelompok=" + selectedKelompok.id_kelompok + "&jenis_aktivitas=" + "PERAWATAN").load();
        refreshTablePermintaanPerawatan();
      }
    });
  }
})

btnSimpanPermintaanBibit.on("click", function(){
  var permintaanBaru = objTransaksi(
    id_aktivitas_selected,
    0,
    selectedRowData.id_kelompok,
    2,
    selectedRowData.tahun_giling,
    asalBibit.selectize()[0].selectize.options[id_aktivitas_selected].nama_aktivitas,
    parseFloat(luasBibitDiminta.val()),
    parseFloat(luasBibitDiminta.val()),
    asalBibit.selectize()[0].selectize.options[id_aktivitas_selected].biaya,
    asalBibit.selectize()[0].selectize.options[id_aktivitas_selected].biaya
  );
  var arrayPost = [];
  arrayPost.push(permintaanBaru);
  console.log(arrayPost);
  $.ajax({
    url: js_base_url + "Rdkk_list/getArrayPermintaanPerawatan",
    dataType: "json",
    type: "POST",
    data: "perawatan=" + JSON.stringify(arrayPost),
    success: function(response){
      if(response.length > 0){
        for(i = 0; i < response.length; i++){
          console.log(response.length);
          alert(response[i]);
        }
      }
      arrayPost = [];
      resetFeedbackAddBibit();
    }
  });
});

dialogAddPermintaanPupuk.on("hide.bs.modal", function(){
  arrayPermintaanPupuk = [];
  resetFeedbackAddPupuk();
});

dialogAddPerawatan.on("hide.bs.modal", function(){
  arrayPermintaanPerawatan = [];
  resetFeedbackAddPerawatan();
});

btnTambahPerawatan.on("click", function(){
  if(validasiFormPerawatan()){
    var perawatan_luasDimintaValue = parseFloat(perawatan_luasDiminta.val());
    var id_aktivitas_selected = cbxAktivitas.selectize()[0].selectize.getValue();
    var aktivitas_selected = cbxAktivitas.selectize()[0].selectize.options[id_aktivitas_selected];
    var kuanta = parseFloat(perawatan_luasDiminta.val());
    /*
    function(id_aktivitas, id_bahan, id_kelompok, kode_transaksi, tahun_giling, nama_bahan, kuanta, luas_aplikasi, satuan, rupiah)
    obj.id_aktivitas = id_aktivitas;
    obj.id_bahan = id_bahan;
    obj.id_kelompok = id_kelompok;
    obj.kode_transaksi = kode_transaksi;
    obj.kuanta = kuanta;
    obj.tahun_giling = tahun_giling;
    obj.nama_bahan = nama_bahan;
    obj.rupiah = Math.round(kuanta*rupiah);
    obj.luas_aplikasi = luas_aplikasi;
    obj.satuan = satuan;
    */
    var permintaanBaru = objTransaksi(
      id_aktivitas_selected,
      0,
      selectedKelompok.id_kelompok,
      2,
      selectedKelompok.tahun_giling,
      aktivitas_selected.nama_aktivitas,
      kuanta,
      kuanta,
      aktivitas_selected.biaya,
      aktivitas_selected.biaya);
    var index_aktivitas = arrayPermintaanPerawatanMaks.findIndex(x => x.id_aktivitas === permintaanBaru.id_aktivitas);
    if (arrayPermintaanPerawatanMaks[index_aktivitas].req_maks >= permintaanBaru.kuanta){
      arrayPermintaanPerawatanMaks[index_aktivitas].req_maks = arrayPermintaanPerawatanMaks[index_aktivitas].req_maks - permintaanBaru.kuanta;
      arrayPermintaanPerawatan.push(permintaanBaru);
      cbxAktivitas.selectize()[0].selectize.setValue("");
      perawatan_luasDiminta.val("");
      perawatan_jmlBiaya.val("");
      perawatan_biaya.val("");
      resetFeedbackAddPerawatan();
    } else {
      alert("Permintaan perawatan untuk aktivitas " + permintaanBaru.nama_bahan + " sudah melebihi batas luasan!");
    }
  }
})

btnTambahPupuk.on("click", function(){
  if (validasiForm()){
    var hargaSatuan;
    var luasValue = parseFloat(luasAplikasi.val());
    var bahanSelected = cbxJenisBahan.selectize()[0].selectize.options[cbxJenisBahan.selectize()[0].selectize.getValue()];
    var kemasan = bahanSelected.kemasan;
    var dosisBahan = bahanSelected.dosis_per_ha;
    var kuanta_req = dosisBahan * luasValue;
    var kuanta = Math.round(kuanta_req/kemasan)*kemasan;
    var id_gudang = cbxNamaGudang.selectize()[0].selectize.getValue();
    var id_bahan = cbxJenisBahan.selectize()[0].selectize.getValue();
    //ARRAY STOK BAHAN DISINI
    $.ajax({
      url: js_base_url + "Admin_bahan/getStokGudangByIdGudang",
      dataType: "json",
      data: {id_gudang: id_gudang},
      type: "GET",
      success: function(response){
        arrayStokBahan = response;
        console.log("Stok tersedia di gudang :");
        console.log(arrayStokBahan);

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
        var objTransaksiPupuk =
        function(id_aktivitas, id_bahan, id_kelompok, kode_transaksi, tahun_giling, nama_bahan, kuanta, luas_aplikasi, satuan, rupiah){
          var obj = {}
          obj.id_aktivitas = id_aktivitas;
          obj.id_bahan = id_bahan;
          obj.id_kelompok = id_kelompok;
          obj.kode_transaksi = kode_transaksi;
          obj.kuanta = kuanta;
          obj.tahun_giling = tahun_giling;
          obj.nama_bahan = nama_bahan;
          obj.rupiah = Math.round(kuanta*rupiah);
          obj.luas_aplikasi = luas_aplikasi;
          obj.satuan = satuan;
          obj.id_gudang = id_gudang;
          return obj;
        }
        var permintaanBaru = objTransaksiPupuk(0,bahanSelected.id_bahan, selectedKelompok.id_kelompok, 2,
          selectedKelompok.tahun_giling, bahanSelected.nama_bahan, kuanta, luasValue, bahanSelected.satuan, hargaSatuan, id_gudang);
        var indexBahan = arrayPermintaanPupukMaks.findIndex(x => x.id_bahan === permintaanBaru.id_bahan);
        var indexStokBahan = arrayStokBahan.findIndex(x => x.id_bahan === permintaanBaru.id_bahan);
        if (indexStokBahan != -1){
          if (permintaanBaru.kuanta <= arrayStokBahan[indexStokBahan].total_kuanta){
            if (permintaanBaru.kuanta <= arrayPermintaanPupukMaks[indexBahan].maks){
              arrayPermintaanPupukMaks[indexBahan].maks = arrayPermintaanPupukMaks[indexBahan].maks - permintaanBaru.kuanta;
              /*
              let arrayRequest = {
                "objTransaksi": permintaanBaru,
                "id_gudang": id_gudang
              }
              */
              arrayPermintaanPupuk.push(permintaanBaru);
              cbxJenisBahan.selectize()[0].selectize.setValue("");
              cbxNamaGudang.selectize()[0].selectize.setValue("");
              luasAplikasi.val("");
              refreshTablePermintaan();
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
    });
  }
})

$("#jenis_aktivitas").on("change", function(){
  if ($(this).val() != ""){
    cbxAktivitas = $("#jenis_aktivitas");
    id_aktivitas_selected = cbxAktivitas.selectize()[0].selectize.getValue();
    biaya_aktivitas = parseInt(cbxAktivitas.selectize()[0].selectize.options[id_aktivitas_selected].biaya);
    $("#perawatan_biaya").val("Rp " + biaya_aktivitas.toLocaleString({maximumFractionDigits: 2}));
  }
  //console.log(cbxAktivitas.selectize()[0].selectize.options[id_aktivitas_selected]);
})

/********************** GENERAL FUNCTIONS SECTION *****************************/


function hapusPermintaan(index){
  var indexBahan = arrayPermintaanPupukMaks.findIndex(x => x.id_bahan === arrayPermintaanPupuk[index].id_bahan);
  arrayPermintaanPupukMaks[indexBahan].maks = arrayPermintaanPupukMaks[indexBahan].maks + arrayPermintaanPupuk[index].kuanta;
  arrayPermintaanPupuk.splice(index,1);
  refreshTablePermintaan();
}

function hapusPermintaanPerawatan(index){
  var indexBahan = arrayPermintaanPerawatanMaks.findIndex(x => x.id_aktivitas === arrayPermintaanPerawatan[index].id_aktivitas);
  arrayPermintaanPerawatanMaks[indexBahan].req_maks = arrayPermintaanPerawatanMaks[indexBahan].req_maks + arrayPermintaanPerawatan[index].kuanta;
  arrayPermintaanPerawatan.splice(index,1);
  refreshTablePermintaanPerawatan();
}

function addPerawatan(id_kelompok){
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    type: "GET",
    dataType: "json",
    data: {
      id_kelompok: id_kelompok
    },
    success: function(response){
      var tahun_giling = response.tahun_giling;
      selectedKelompok = response;
      $("#perawatan_namaKelompok").val(response.nama_kelompok);
      $("#perawatan_luas").val(parseFloat(response.luas).toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha");
      $("#tblPerawatan").DataTable().ajax.url(js_base_url + "Transaksi_aktivitas/getTransaksiAktivitasByIdKelompok?id_kelompok=" + response.id_kelompok + "&jenis_aktivitas=" + "PERAWATAN").load();
      $.ajax({
        url: js_base_url + "Admin_aktivitas/getAktivitasByTahunGiling",
        type: "GET",
        dataType: "json",
        data: {
          tahun_giling: tahun_giling,
          kategori: response.kategori
        },
        success: function(response){
          if(response.length > 0){
            $("#jenis_aktivitas").selectize();
            $("#jenis_aktivitas").selectize()[0].selectize.clear();
            $("#jenis_aktivitas").selectize()[0].selectize.clearOptions();
            $("#jenis_aktivitas").selectize()[0].selectize.load(function(callback){
              callback(response);
            });
            arrayAktivitas = response;
            arrayPermintaanPerawatanMaks = [];
            var luas_maks = parseFloat($("#perawatan_luas").val().replace(/[^0-9. ]/g,""));
            for (i = 0; i < arrayAktivitas.length; i ++){
              arrayPermintaanPerawatanMaks.push({id_aktivitas: arrayAktivitas[i].id_aktivitas, req_maks: luas_maks});
            }
            dialogAddPerawatan.modal("toggle");
          } else {
            alert("Master data aktivitas perawatan belum ada untuk tahun giling " + tahun_giling + " !\nSilahkan hubungi Admin.");
          }
        }
      });
    }
  });
}

function addBibit(id_kelompok, kategori){
  dialogAddPermintaanBibit.on("hide.bs.modal", function(){
    resetFeedbackAddBibit();
  });
  var tblListData = $("#tblList").DataTable().rows().data();
  var tblListArr = $.map(tblListData, function(value, index){
    return [value];
  })
  selectedRowData = tblListArr.find(x => x.id_kelompok == id_kelompok);
  var arrayAsalBibit;

  namaKelompokBibit.val(selectedRowData.nama_kelompok);
  luasBakuBibit.val(selectedRowData.luas + " Ha");
  varBibit.val(selectedRowData.nama_varietas);
  function validasiFormBibit(){
    if (asalBibit.val() != "" && luasBibitDiminta.val() != ""){
      luasBibitDiminta.removeClass("is-invalid");
      asalBibit.removeClass("is-invalid");
      return true;
    } else {
      (asalBibit.val() == "") ? asalBibit.addClass("is-invalid") : asalBibit.removeClass("is-invalid");
      (luasBibitDiminta.val() == "") ? luasBibitDiminta.addClass("is-invalid") : luasBibitDiminta.removeClass("is-invalid");
    }
    return false;
  }
  $.ajax({
    url: js_base_url + "Rdkk_list/getAktivitasBibit?tahun_giling="+selectedRowData.tahun_giling,
    type: "GET",
    dataType: "json",
    success: function(response){
      if(response.length > 0){
        dialogAddPermintaanBibit.modal("toggle");
        asalBibit.selectize();
        asalBibit.selectize()[0].selectize.clear();
        asalBibit.selectize()[0].selectize.clearOptions();
        asalBibit.selectize()[0].selectize.load(function(callback){
          callback(response);
        });
        arrayAsalBibit = response;
      } else {
        alert("Master data aktivitas permintaan bibit belum ada untuk tahun giling " + tahun_giling + " !\nSilahkan hubungi Admin.");
      }
    }
  });
}

function addPupuk(id_kelompok){
  $.ajax({
    url: js_base_url + "Landing/loadDataGudang",
    dataType: "json",
    type: "GET",
    success: function(response){
      arrayStokBahan = response;
      console.log("Stok tersedia:");
      console.log(arrayStokBahan);
    }
  });

  //CEK DULU TAHUN GILING BERAPA, KEMUDIAN TENTUKAN STOK BAHAN PADA TAHUN GILING YBS
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    type: "GET",
    dataType: "json",
    data: {
      id_kelompok: id_kelompok
    },
    success: function(response){
      selectedKelompok = response;
      var tahun_giling_pupuk = selectedKelompok.tahun_giling;
      $("#namaKelompok").val(response.nama_kelompok);
      $("#luas").val(parseFloat(response.luas).toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha");
      $("#tblPupuk").DataTable().ajax.url(js_base_url + "Transaksi_bahan/getTransaksiKeluarByIdKelompok?id_kelompok=" +
        response.id_kelompok).load(function(callback){
        arrayPermintaanPupukExisting = callback;
      });
      $.ajax({
        url: js_base_url + "Admin_bahan/getBahanByJenisTahunGiling",
        //url: js_base_url + "Admin_bahan/getBahanByJenis",
        type: "GET",
        dataType: "json",
        data: {
          jenis_bahan: "PUPUK",
          tahun_giling: response.tahun_giling
        },
        success: function(response){
          if(response.length > 0){
            dialogAddPermintaanPupuk.modal("toggle");
            $("#jenis_pupuk").selectize();
            $("#jenis_pupuk").selectize()[0].selectize.clear();
            $("#jenis_pupuk").selectize()[0].selectize.clearOptions();
            $("#jenis_pupuk").selectize()[0].selectize.load(function(callback){
              callback(response);
              arrayBahan = response;
              console.log("Array bahan : ");
              console.log(arrayBahan);
              //perhitungan permintaan pupuk maksimal
              arrayPermintaanPupukMaks = [];
              for (i = 0; i < arrayBahan.length; i++){
                let satuan_kemasan = arrayBahan[i].kemasan;
                arrayPermintaanPupukMaks.push({
                  id_bahan: arrayBahan[i].id_bahan,
                  maks: Math.round((arrayBahan[i].dosis_per_ha*selectedKelompok.luas)/satuan_kemasan)*satuan_kemasan
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
          } else {
            alert("Master data pupuk belum ada untuk tahun giling " + selectedKelompok.tahun_giling + " !\n Silahkan hubungi Admin.");
          }
        }
      });
    },
    error: function(textStatus){
      console.log(textStatus);
    }
  })
}

function addTma(id_kelompok){
  dialogAddPermintaanTma.modal("toggle");
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    data: {id_kelompok: id_kelompok},
    dataType: "json",
    type: "GET",
    success: function(response){
      var id_wilayah = response.id_wilayah;
      $.ajax({
        url: js_base_url + "Admin_tma/getBiayaTmaByIdWilayah",
        data: {id_wilayah: id_wilayah},
        dataType: "json",
        type: "GET",
        success: function(response){

        }
      })
    }
  })
}


/* FOR TESTING PURPOSES ONLY
formKonfirmasi.on("submit", function(event){
  event.preventDefault();
  console.log( $( this ).serialize() );
})
*/

function cekAffKebun(id_kelompok,jenis){
  var kode_blok = null;
  var kode_blok_simtr = null;
  var data_kelompok = null;
  var data_penjualan = null;
  $.ajax({
    url: js_base_url + "Rdkk_list/getKelompokById",
    data: {id_kelompok: id_kelompok},
    dataType: "json",
    type: "GET",
    success: function(response){
      kode_blok = (response.kode_blok).substr(2,7); //kode blok versi SIMPG!!!
      kode_blok_simtr = response.kode_blok;
      dataKelompok = response;
      $.ajax({
        url: js_base_url + "Biaya_tma/cekBeritaAcaraTebang",
        data: {kode_blok: kode_blok_simtr},
        dataType: "json",
        type: "GET",
        success: function(response){
          if(jenis == "aff"){
            if(response == 0){
              $.ajax({
                url: js_base_url + "Biaya_tma/cekAffKebun",
                data: {kode_blok: kode_blok},
                dataType: "json",
                type: "GET",
                success: function(response){
                  var status_aff = response[0].aff_tebang;
                  if(status_aff == 0){
                    alert("Kelompok " + dataKelompok.nama_kelompok + " belum AFF di SIMPG! Harap melakukan validasi luasan dan \"SET AFF\" di SIMPG");
                  } else {
                    $.ajax({
                      url: js_base_url + "Biaya_tma/cekDataCs",
                      data: {kode_blok: kode_blok},
                      dataType: "json",
                      type: "GET",
                      success: function(response){
                        if(response.length == 0){
                          $.ajax({
                            url: js_base_url + "Admin_potongan/getPotonganByTahunGiling",
                            data: {tahun_giling: dataKelompok.tahun_giling},
                            dataType: "json",
                            type: "get",
                            success: function(resp){
                              formInputIdKelompok.value = id_kelompok;
                              formInputKodeBlok.value = kode_blok;
                              formKonfirmasi.submit();
                            }
                          })
                        } else {
                          alert("Data hablur Kelompok " + dataKelompok.nama_kelompok + " BELUM LENGKAP. Silahkan hubungi Admin Sistem dan Bagian QA.")
                        }
                      }
                    })
                  }
                }
              })
            } else {
              alert("Berita Acara Selesai Tebang untuk kelompok " + dataKelompok.nama_kelompok + " ini sudah ada!");
              formInputKodeBlokView.value = kode_blok_simtr;
              formInputIdKelompokView.value = id_kelompok;
              formViewBeritaAcaraAff.submit();
            }
          } else if(jenis == "nota"){
            if(response == 1){
              //cek penjualan gula
              //table penjualan_gula => gula_ptr adalah seluruh gula milik petani
              $.ajax({
                url: js_base_url + "Rdkk_add/addNotaBunga",
                data: {
                  id_kelompok: dataKelompok.id_kelompok,
                  kode_blok: kode_blok_simtr
                },
                dataType: "json",
                type: "POST",
                success: function(data){
                  if(data.status == 1){

                  } else {
                    alert(data.message);
                  }
                }
              })
            } else {
              alert("Berita Acara AFF Tebang belum ada!");
            }
          }
        }
      })
    }
  })
}

function resetFeedbackAddPupuk(){
  luasAplikasi.removeClass("is-invalid");
  cbxJenisBahan.removeClass("is-invalid");
  $("#card_tblTransaksi").addClass("card-collapsed");
  luasAplikasi.val("");
  cbxJenisBahan.val("");
  refreshTablePermintaan();
}

function resetFeedbackAddBibit(){
  asalBibit.val("");
  biayaBibit.val("");
  luasBibitDiminta.val("");
  totalBiayaBibit.val("");
  luasBibitDiminta.removeClass("is-invalid");
  asalBibit.removeClass("is-invalid");
};

function resetFeedbackAddPerawatan(){
  perawatan_luasDiminta.removeClass("is-invalid");
  cbxAktivitas.removeClass("is-invalid");
  $("#card_tblPerawatan").addClass("card-collapsed");
  perawatan_luasDiminta.val("");
  cbxAktivitas.val("");
  perawatan_biaya.val("");
  perawatan_jmlBiaya.val("");
  refreshTablePermintaanPerawatan();
}

function validasiForm(){
  var idBahan = cbxJenisBahan.selectize()[0].selectize.getValue();
  if (idBahan != "" && luasAplikasi.val() != ""){
    return true;
  } else {
    (idBahan == "") ? cbxJenisBahan.addClass("is-invalid") : "";
    (luasAplikasi.val() == "") ? luasAplikasi.addClass("is-invalid") : "";
  }
  return false;
}

function validasiFormPerawatan(){
  var id_aktivitas = cbxAktivitas.selectize()[0].selectize.getValue();
  if (id_aktivitas != "" && perawatan_luasDiminta.val() != ""){
    return true;
  } else {
    (id_aktivitas == "") ? cbxAktivitas.addClass("is-invalid") : "";
    (perawatan_luasDiminta.val() == "") ? perawatan_luasDiminta.addClass("is-invalid") : "";
  }
  return false;
}

function refreshTablePermintaan(){
  tblPermintaanPupuk = $("#tblPermintaanPupuk").DataTable();
  tblPermintaanPupuk.clear();
  tblPermintaanPupuk.rows.add(arrayPermintaanPupuk);
  tblPermintaanPupuk.draw();
}

function refreshTablePermintaanPerawatan(){
  tblPermintaanPerawatan = $("#tblPermintaanPerawatan").DataTable();
  tblPermintaanPerawatan.clear();
  tblPermintaanPerawatan.rows.add(arrayPermintaanPerawatan);
  tblPermintaanPerawatan.draw();
}

function actionButtonView(id_kelompok, kategori, priv_level){
  var menu_bibit = "";
  if(priv_level == "Asisten Bagian" || priv_level == "Administrasi Sub Bagian"){
    if(kategori == 1){
      menu_bibit = '<a class="dropdown-item" href="#" onclick="addBibit(' + id_kelompok + ')"><i class="fe fe-loader"></i> Buat Permintaan Bibit</a>'
    }
    return  '<div class="btn-group"><button style="width: 80px" type="button" class="btn btn-secondary btn-sm btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="menu" aria-expanded="false">' +
            '<i class="fe fe-settings mr-2"></i> Opsi' +
            '</button>' +
            '<div class="dropdown-menu">' +
              '<a class="dropdown-item" href="Resume_skk/viewBa?id_kelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat SKK</a>' +
              '<a class="dropdown-item" href="Rdkk_view?id_kelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat Data Kelompok</a>' +
              '<a class="dropdown-item" href="Transaksi_list?id_kelompok='+id_kelompok+'"><i class="fe fe-file-text"></i> Lihat Semua Transaksi</a>' +
              '<a class="dropdown-item" href="Bagi_hasil?id_kelompok='+id_kelompok+'"><i class="fe fe-file-text"></i> Lihat Data Rendemen</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a class="dropdown-item" href="#" onclick="addPupuk(' + id_kelompok + ')"><i class="fe fe-sunset"></i> Buat Permintaan Pupuk</a>' +
              '<a class="dropdown-item" href="#" onclick="addPerawatan(' + id_kelompok + ')"><i class="fe fe-feather"></i> Buat Permintaan Perawatan</a>' +
              menu_bibit +
              '<a class="dropdown-item" href="#" onclick="cekAffKebun(' + id_kelompok + ', ' + '\'aff\'' + ')"><i class="fe fe-book-open"></i> Buat Berita Acara Selesai Tebang</a>' +
              '<a class="dropdown-item" href="#" onclick="cekAffKebun(' + id_kelompok + ', ' + '\'nota\'' + ')"><i class="fe fe-book-open"></i> Buat Nota Bunga</a>' +
              '<a class="dropdown-item" href="" onclick="" style="display:none"><i class="fe fe-zap"></i> Buat Permintaan Biaya TMA</a>' +
            '</div></div>';
  } else {
    return  '<div class="btn-group"><button style="width: 80px" type="button" class="btn btn-secondary btn-sm btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="menu" aria-expanded="false">' +
            '<i class="fe fe-settings mr-2"></i> Opsi' +
            '</button>' +
            '<div class="dropdown-menu">' +
              '<a class="dropdown-item" href="Resume_skk/viewBa?id_kelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat SKK</a>' +
              '<a class="dropdown-item" href="Rdkk_view?id_kelompok=' + id_kelompok + '"><i class="fe fe-file-text"></i> Lihat Data Kelompok</a>' +
              '<a class="dropdown-item" href="Transaksi_list?id_kelompok='+id_kelompok+'"><i class="fe fe-file-text"></i> Lihat Semua Transaksi</a>' +
            '</div></div>';
  }
}

/********************* COMBO BOX SECTIONS *************************************/

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

$("#jenis_aktivitas").selectize({
  valueField: "id_aktivitas",
  labelField: "nama_aktivitas",
  sortField: "nama_aktivitas",
  searchField: "nama_aktivitas",
  maxItems: 1,
  create: false,
  placeholder: "Pilih jenis aktivitas",
  options: []
});

$("#asalBibit").selectize({
  valueField: "id_aktivitas",
  labelField: "nama_aktivitas",
  sortField: "nama_aktivitas",
  searchField: "nama_aktivitas",
  maxItems: 1,
  create: false,
  placeholder: "Pilih asal bibit",
  options: []
});

$.ajax({
  url: js_base_url + "Admin_gudang/getAllGudangAktif",
  type: "GET",
  dataType: "json",
  success: function(response){
    $("#nama_gudang").selectize({
      valueField: "id_gudang",
      labelField: "nama_gudang",
      sortField: "nama_gudang",
      searchField: "nama_gudang",
      maxItems: 1,
      create: false,
      placeholder: "Pilih gudang",
      options: response,
      onChange: function(value){

      }
    });
  }
})

/************************ DATATABLE SECTION ***********************************/

$("#tblList").DataTable({
  bFilter: true,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: '<"row"<"labelTahunGiling"><"cbxTahunGiling">f>t<"spacer">pl',
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
      data: "kategori",
      render: function(data, type, row, meta){
        var katg = "";
        switch(data){
          case "1":
            katg = "PC";
            break;
          case "2":
            katg = "RT 1";
            break;
          case "3":
            katg = "RT 2";
            break;
          case "4":
            katg = "RT 3";
            break;
        }
        return katg;
      },
      className: "text-center"
    },
    {
      data: "tahun_giling",
      render: function(data, type, row, meta){
        var tahun_tanam = parseInt(data) - 1;
        var str_tahungiling = data.toString().substr(2,2);
        var str_tahuntanam = tahun_tanam.toString().substr(2,2);
        return str_tahuntanam + "/" + str_tahungiling;
      },
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
        return actionButtonView(row.id_kelompok, row.kategori, row.priv_level);
        //return row.kategori;
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
      var tahun_tanam = parseInt(currYear + i) - 1;
      var str_tahungiling = (currYear + i).toString().substr(2,2);
      var str_tahuntanam = tahun_tanam.toString().substr(2,2);
      optionTahun += '<option value="' + parseInt(currYear + i) + '">' + 'KTG ' + str_tahuntanam + '/' + str_tahungiling + '</option>';
    }
    $("div.spacer").html('<div class="row" style="height:120px"></div>')
    $("div.cbxTahunGiling").html('<select style="width: 150px;" name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">' + optionTahun + '</select>');
    $("div.labelTahunGiling").html('<label class="form-label" style="margin: 0px 10px 0px 0px;"></label>');
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
      data: "luas_aplikasi",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString(undefined, {maximumFractionDigits:2}) + " HA";
      },
      className: "text-right"
    },
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
        return '<a class="btn btn-outline-primary btn-sm" name="hapus" id="cetakAu58" href="Transaksi_AU58?no_transaksi='+row.no_transaksi+'&id_kelompok='+row.id_kelompoktani+'"><i class="fe fe-printer"></i></a>'
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

$("#tblPermintaanPerawatan").DataTable({
  bFilter: false,
  bPaginate: false,
  bSort: false,
  bInfo: false,
  data: arrayPermintaanPerawatan,
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
      data: "satuan",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString({maximumFractionDigits:2});
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
        return '<div class="btn btn-danger btn-sm" name="hapus" id="hapusPermintaanPerawatan" onclick="hapusPermintaanPerawatan('+meta.row+')"><i class="fe fe-trash-2"></i></div>'
      }
    }
  ],
  "footerCallback": function (row, data, start, end, display){
    var api = this.api(), data;
    var intVal = function ( i ) {
      return typeof i === 'string' ? i.replace(/[\Rp,]/g, '')*1 : typeof i === 'number' ? i : 0;
    };
    total = api.column(4).data().reduce( function (a, b) {
        return intVal(a) + intVal(b);
    },0);
    $(api.column(4).footer()).html('<font size="3" color="white">' + 'Rp '+ total.toLocaleString({maximumFractionDigits: 2}) + '</font>');
  }
});

$("#tblPerawatan").DataTable({
  bFilter: false,
  bPaginate: true,
  bSort: false,
  bInfo: false,
  dom: 'tp',
  ajax: {
    url: js_base_url + "Transaksi_aktivitas/getTransaksiAktivitasByIdKelompok?id_kelompok="+0+"&jenis_aktivitas=" + "PERAWATAN",
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
    {data: "nama_aktivitas"},
    {
      data: "kuanta",
      render: function(data, type, row, meta){
        return parseFloat(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2}) + " Ha";
      },
      className: "text-right"
    },
    {
      data: "rupiah",
      render: function(data, type, row, meta){
        return "Rp " + parseInt(data).toLocaleString({minimumFractionDigits: 2, maximumFractionDigits:2});
      },
      className: "text-right"
    },
    {
      data: "button",
      render: function(data, type, row, meta){
        return '<a class="btn btn-outline-primary btn-sm" name="hapus" id="" href="Transaksi_aktivitas?no_transaksi='+row.no_transaksi+'&id_kelompok='+row.id_kelompoktani+'"><i class="fe fe-printer"></i></a>'
      },
      className: "text-center"
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
  },
  initComplete: function(){

  }
});
