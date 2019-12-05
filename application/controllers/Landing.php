<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

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

	public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Landing.js").'");';
  }

	public function loadData(){
		echo $this->dashboard_model->loadData();
	}

	public function loadDataGudang(){
		echo $this->dashboard_model->loadDataGudang();
	}

	public function loadContent(){
		$content_header =
		'
			<div class="row row-cards">
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
									<div class="card-body p-3 text-center">
										<div class="h1 m-0" id="total_luas"></div>
										<div class="text-muted mb-4">Lahan terdaftar</div>
									</div>
								</div>
							</div>
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
									<div class="card-body p-3 text-center">
										<div class="h1 m-0" id="total_kelompok"></div>
										<div class="text-muted mb-4">Jumlah Kelompok Tani</div>
									</div>
								</div>
							</div>
							<div class="col-4 col-sm-12 col-lg-4">
								<div class="card">
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
											<strong>33%</strong>
										</div>
										<div class="float-right">
											<small class="text-muted">1,250 / 1,355 Ha</small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div class="progress-bar bg-red" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
								</td>
							</tr>
							<tr>
								<td>Ton ditebang</td>
								<td>
									<div class="clearfix">
										<div class="float-left">
											<strong>40%</strong>
										</div>
										<div class="float-right">
											<small class="text-muted">120,560 / 129,500 ton</small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div class="progress-bar bg-yellow" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
								</td>
							</tr>
							<tr>
								<td>Rendemen</td>
								<td>
									<div class="clearfix">
										<div class="float-left">
											<strong>90%</strong>
										</div>
										<div class="float-right">
											<small class="text-muted">6.30 / 7.00 %</small>
										</div>
									</div>
									<div class="progress progress-xs">
                    <div class="progress-bar bg-green" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
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
