var txtRafaksiLain = $("#ton_rafaksiLain");
var chk_NamaKelompok = $("#nama_kelompok");
var chk_NoKontrak = $("#no_kontrak");
var chk_Kategori = $("#kategori");
var chk_Luas = $("#luas");
var chk_LuasTebang = $("#luas_tebang");
var chk_Varietas = $("#varietas");
var chk_MasaTanam = $("#masa_tanam");
var chk_TonTakmar = $("#ton_takmar");
var chk_TonTimbang = $("#ton_timbang");
var chk_TonRafaksiBakar = $("#ton_rafaksi_bakar");
var chk_TonTrash = $("#ton_trash");
var chk_TonRafaksiCs = $("#ton_rafaksiCs");
var chk_TonRafaksiLain = $("#cek_rafaksiLain");
var chk_TonHitung = $("#ton_hitung");
var chk_TonTotalRafaksi = $("#ton_totalRafaksi");
var chk_PersenRafaksiTakmar = $("#persen_rafaksi_takmar");
var chk_TonTebuBakar = $("#ton_bakar");
var chk_TglAwalTimbang = $("#awal_tebang");
var chk_TglAkhirTimbang = $("#akhir_tebang");
var frm_selesaiTebang = $("#frmSelesaiTebang");

function koreksiDesimal(textBox){
    var val_textbox = textBox.val();
    val_textbox = parseFloat(val_textbox.replace(/[^0-9.]/,""));
    (isNaN(val_textbox) ? val_textbox = 0 : val_textbox = val_textbox);
    textBox.val(val_textbox);
}

txtRafaksiLain.bind("blur", function(){
  koreksiDesimal(txtRafaksiLain);
});

function cekValidasiField(){
  if(
    chk_NamaKelompok.is(":checked") && chk_NoKontrak.is(":checked") && chk_Kategori.is(":checked") && chk_Luas.is(":checked") &&
    chk_LuasTebang.is(":checked") && chk_Varietas.is(":checked") && chk_MasaTanam.is(":checked") && chk_TonTakmar.is(":checked") &&
    chk_TonTimbang.is(":checked") && chk_TonRafaksiBakar.is(":checked") && chk_TonTrash.is(":checked") && chk_TonRafaksiCs.is(":checked") &&
    chk_TonRafaksiLain.is(":checked") && chk_TonHitung.is(":checked") && chk_TonTotalRafaksi.is(":checked") && chk_PersenRafaksiTakmar.is(":checked") &&
    chk_TonTebuBakar.is(":checked") && chk_TglAwalTimbang.is(":checked") && chk_TglAkhirTimbang.is(":checked")
  ){
    $.ajax({
      url: js_base_url + "Aff_kebun/simpanAffKebun",
      type: "POST",
      dataType: "json",
      data: frm_selesaiTebang.serialize(),
      success: function(response){
        if(response == 1){
          alert("Data berhasil disimpan!");
          window.location.replace(js_base_url + "rdkk_all");
        }
      },
      error: function(xhr, status, error){
        console.log(xhr.status + ":" + xhr.statusText);
      }
    })
  } else {
    alert("Periksa kembali, validasi belum lengkap!");
  }
}
