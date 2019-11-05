<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="en" dir="ltr">
  <head>
    <? $this->load->view("_partials/head.php")?>
  </head>
  <body class="">
    <div class="page">
      <div class="flex-fill">
        <? $this->load->view("_partials/upper_banner.php") ?>
        <? $this->load->view("_partials/navbar.php") ?>
        <div class="my-3 my-md-5">
          <div class="container">
            <? $this->load->view("_partials/page_title.php") ?>
            <?
              if (isset($content)){
                echo $content;
              }
            ?>
          </div>
          <script>
            function rdkkNextStep(){
              $('#grNamaDesa').hide();
              $('#grNamaKelompok').hide();
              $('#grMasaTanam').hide();
              $('#btnNext').hide();
              $('#grUploadKtp').hide();
              $('#grUploadKk').hide();
              $('#grUploadPernyataan').hide();
              $('#grVarietas').hide();
              $('#grNamaPetani').show();
            }

            function rdkkLoad(){
              $('#grNamaPetani').hide();
            }
            require(['jquery'], function () {

            	$(document).ready(function () {

                //rdkkLoad();

            		function setCookie(name,value,days) {
            			var expires = "";
            			if (days) {
            				var date = new Date();
            				date.setTime(date.getTime() + (days*24*60*60*1000));
            				expires = "; expires=" + date.toUTCString();
            			}
            			document.cookie = name + "=" + (value || "")  + expires + "; path=/";
            		}

            		function getCookie(name) {
            			var nameEQ = name + "=";
            			var ca = document.cookie.split(';');
            			for(var i=0;i < ca.length;i++) {
            				var c = ca[i];
            				while (c.charAt(0)==' ') c = c.substring(1,c.length);
            				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            			}
            			return null;
            		}

            		if (!getCookie('bottombar-hidden')) {
            			$('.js-bottombar').show();
            		}

            		$('.js-bottombar-close').on('click', function (e) {
            			$('.js-bottombar').hide();
            			setCookie('bottombar-hidden', 1, 7);
            			e.preventDefault();
            			return false;
            		});

            	});
            });
            require(['jquery', 'selectize'], function ($, selectize) {
              $('#namaDesa').selectize({create: false, sortField: 'text'});
              $('#masaTanam').selectize({create: false, sortField: 'text'});
              $('#varietas').selectize({create: false, sortField: 'text'});
            });
          </script>
        </div>
      </div>
      <!-- <? $this->load->view("_partials/lower_banner.php") ?> -->
      <footer class="footer">
        <? $this->load->view("_partials/footer.php") ?>
      </footer>
    </div>
  </body>
</html>
