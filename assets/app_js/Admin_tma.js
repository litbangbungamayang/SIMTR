$.ajax({
  url: js_base_url + "Rdkk_add/getAllKabupaten",
  type: "GET",
  dataType: "json",
  success: function(response){
    $("#namaKab").selectize({
      valueField: "id_wilayah",
      labelField: "nama_wilayah",
      sortField: "nama_wilayah",
      searchField: "nama_wilayah",
      maxItems: 1,
      create: false,
      placeholder: "Pilih kabupaten",
      options: response,
      onChange: function(value){
      }
    });
  }
});
