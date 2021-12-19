
loadDashboardData();

function loadDashboardData(){
  var total_kelompok;
  var total_petani;
  var total_luas;
  var rkap_luas;
  var rkap_ton;
  var rkap_rend;
  var real_luas;
  var real_ton;
  var real_rend;

  $.ajax({
    url: js_base_url + "Landing/loadData",
    dataType:"json",
    type: "GET",
    success: function(response){
      total_luas = parseFloat(response.total_luas * 1).toFixed(2);
      total_kelompok = response.total_kelompok;
      total_petani = response.total_petani;
      rkap_luas = parseFloat(response.luas_tr * 1).toFixed(2);
      rkap_ton = parseFloat(response.tebu_tr * 1).toFixed(2);
      rkap_rend = parseFloat(response.rend_tr * 1).toFixed(2);
      $.ajax({
        url: js_base_url + "Landing/loadDataGudang",
        dataType: "json",
        type: "GET",
        success: function(response){
          $.each(response, function(i, item){
            var $tr = $("<tr>").append(
              $("<td>").text(item.jenis_bahan + " " + item.nama_bahan),
              $("<td class=text-right>").text(parseInt(item.total_kuanta).toLocaleString() + " " + item.satuan)
            );
            $("#tblStok").append($tr);
          });
        }
      });
      $.ajax({
        url: js_base_url + "Landing/getDataDashboard",
        dataType: "json",
        type: "GET",
        success: function(response){
          function formatAngka (number){
            return parseFloat(number).toLocaleString(undefined,{minimumFractionDigits: 2});
          }
          response = response[0];
          real_luas = parseFloat(response.ha_giling_tr_sd * 1).toFixed(2);
          real_ton = parseFloat(response.ton_giling_tr_sd * 1).toFixed(2);
          real_rend = parseFloat(response.rend_tr_sd * 1).toFixed(2);
          $("#total_luas").html(total_luas + " ha");
          $("#luasan").html(formatAngka(real_luas) + " / " + formatAngka(rkap_luas) + " ha");
          $("#persen_luas").html(parseFloat(real_luas/rkap_luas*100).toFixed(0) + "%");
          $("#progress_luas").css("width", (real_luas/rkap_luas*100) + "%");
          $("#progress_luas").css("background-color", "hsl(" + ((real_luas/rkap_luas*100) + ",50%,50%)"));

          $("#tebu_ditebang").html(formatAngka(real_ton) + " / " + formatAngka(rkap_ton) + " ton");
          $("#persen_tebang").html(parseFloat(real_ton/rkap_ton*100).toFixed(0) + "%");
          $("#progress_tebang").css("width", (real_ton/rkap_ton*100) + "%");
          $("#progress_tebang").css("background-color", "hsl(" + ((real_ton/rkap_ton*100) + ",50%,50%)"));

          $("#rendemen").html(formatAngka(real_rend) + " / " + formatAngka(rkap_rend) + " %");
          $("#persen_rend").html(parseFloat(real_rend/rkap_rend*100).toFixed(0) + "%");
          $("#progress_rend").css("width", (real_rend/rkap_rend*100) + "%");
          $("#progress_rend").css("background-color", "hsl(" + ((real_rend/rkap_rend*100) + ",50%,50%)"));

          $("#total_kelompok").html(total_kelompok);
          $("#total_petani").html(total_petani);

        }
      });

    }
  });


}
