
loadDashboardData();

function loadDashboardData(){
  $.ajax({
    url: js_base_url + "Landing/loadData",
    dataType:"json",
    type: "GET",
    success: function(response){
      //document.getElementById("total_luas").innerHTML = response[0].luas;
      console.log(response);
      $("#total_luas").html(parseFloat(response.total_luas).toLocaleString() + " ha");
      $("#total_kelompok").html(response.total_kelompok);
      $("#total_petani").html(response.total_petani);
    }
  });
  $.ajax({
    url: js_base_url + "Landing/loadDataGudang",
    dataType: "json",
    type: "GET",
    success: function(response){
      console.log(response);
      $.each(response, function(i, item){
        var $tr = $("<tr>").append(
          $("<td>").text(item.jenis_bahan + " " + item.nama_bahan),
          $("<td class=text-right>").text(parseInt(item.total_kuanta).toLocaleString() + " " + item.satuan)
        );
        $("#tblStok").append($tr);
      });
    }
  });
}
