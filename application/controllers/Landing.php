<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

	private $buma_env = "http://simpgbuma.ptpn7.com/index.php/api_bcn/";
  private $cima_env = "http://simpgcima.ptpn7.com/index.php/api_bcn/";
  private $lokal = "http://localhost/simpgbuma/api_bcn/";

	public function __construct(){
			parent::__construct();
			$this->load->model("dashboard_model");
			$this->load->helper(array('url', 'html'));
			$this->load->library('session');
	}

	public function index()
	{
		if ($this->session->userdata('id_user') == false){
			redirect('login');
		} else {
			$data['content'] = $this->loadContent();
			$data['script'] = $this->loadScript();
			$this->load->view('main_view', $data);
		}
	}

	public function getServer($pg){
    $server_pg = null;
    switch($pg){
      case "buma":
        $server_pg = $this->buma_env;
        break;
      case "cima":
        $server_pg = $this->cima_env;
        break;
      case "lokal":
        $server_pg = $this->lokal;
        break;
    }
    return $server_pg;
  }

	function getCurl($request){
    $db_server = $request["db_server"];
    $url = str_replace(" ", "", $request["url"]);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $db_server.$url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $response; // output as json encoded
  }

	public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Landing.js").'");';
  }

	public function loadData(){
		echo $this->dashboard_model->loadData();
	}

	public function loadDataGudang(){
		echo $this->dashboard_model->loadDataGudang();
	}

	public function getDataDashboard(){
		$request = array("db_server"=>$this->getServer("buma"), "url"=>"getDataLastLhp");
    echo ($this->getCurl($request));
	}

	public function loadContent(){
		$content_header =
		'
			<div class="row row-cards">
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
									<div class="card-status bg-red"></div>
									<div class="card-body p-3 text-center">
										<div class="h1 m-0" id="total_luas"></div>
										<div class="text-muted mb-4">Lahan terdaftar</div>
									</div>
								</div>
							</div>
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
									<div class="card-status bg-red"></div>
									<div class="card-body p-3 text-center">
										<div class="h1 m-0" id="total_kelompok"></div>
										<div class="text-muted mb-4">Jumlah Kelompok Tani</div>
									</div>
								</div>
							</div>
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
									<div class="card-status bg-red"></div>
									<div class="card-body p-3 text-center">
										<div class="h1 m-0" id="total_petani"></div>
										<div class="text-muted mb-4">Jumlah Petani</div>
									</div>
								</div>
							</div>
			</div>
			<div class="row row-cards">
				<div class="col-12 col-sm-6 col-lg-4">
					<div class="card">
						<div class="card-status bg-blue"></div>
						<div class="card-header">
							<div class="card-title">Stok Barang Gudang (TR)</div>
						</div>
						<table class="table card-table">
						<tbody id="tblStok">
						</tbody>
						</table>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-lg-4">
					<div class="card">
						<div class="card-status bg-blue"></div>
						<div class="card-header">
							<div class="card-title">Kemajuan Tebang</div>
						</div>
						<table class="table card-table">
						<tbody id="tblTebang">
							<tr>
								<td>Luas ditebang</td>
								<td>
									<div class="clearfix">
										<div class="float-left">
											<strong id="persen_luas"></strong>
										</div>
										<div class="float-right">
											<small class="text-muted" id="luasan"></small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div id="progress_luas" class="progress-bar " role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
								</td>
							</tr>
							<tr>
								<td>Ton ditebang</td>
								<td>
									<div class="clearfix">
										<div class="float-left">
											<strong id="persen_tebang"></strong>
										</div>
										<div class="float-right">
											<small class="text-muted" id="tebu_ditebang"></small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div id="progress_tebang" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
								</td>
							</tr>
							<tr>
								<td>Rendemen</td>
								<td>
									<div class="clearfix">
										<div class="float-left">
											<strong id="persen_rend"></strong>
										</div>
										<div class="float-right">
											<small class="text-muted" id="rendemen"></small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div id="progress_rend" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
				</div>
			</div>
		';
		return $content_header;
	}

	function logout(){
		$data = array('login'=>'','uname'=>'','uid'=>'');
		$this->session->unset_userdata($data);
		$this->session->sess_destroy();
		redirect('/');
	}

}
