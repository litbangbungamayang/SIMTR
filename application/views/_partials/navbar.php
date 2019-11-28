<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg order-lg-first">
        <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
          <li class="nav-item">
            <a href="<? echo base_url('') ?>" class="nav-link <? echo $this->uri->segment(1) == '' ? 'active' : '' ?>"><i class="fe fe-home"></i> Home</a>
          </li>
          <li class="nav-item dropdown" style="display: ">
            <a href="javascript:void(0)" class="nav-link <? echo ($this->uri->segment(1) == 'dokumen')||($this->uri->segment(1) == 'dokumen_add') ? 'active' : '' ?>" data-toggle="dropdown"><i class="fe fe-book-open"></i> RDKK</a>
            <div class="dropdown-menu dropdown-menu-arrow">
              <a href="<? echo site_url('/rdkk_add')?>" class="dropdown-item "><i class="fe fe-search"></i> Pendaftaran RDKK</a>
              <a href="<? echo site_url('/rdkk_all')?>" class="dropdown-item "><i class="fe fe-edit"></i> Penelusuran RDKK</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-sunset"></i> Permintaan Pupuk</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-feather"></i> Perawatan Tanaman</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-zap"></i> Tebang-Muat-Angkut</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-layers"></i> Perhitungan Bagi Hasil</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <? $loggedUser = (object) $this->session->all_userdata();?>
          <li class="nav-item dropdown" style="display:<? echo ($loggedUser->jabatan == 'Superadmin' || $loggedUser->jabatan == 'Admin')? '' : 'none'; ?>">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-settings"></i> Administrasi Sistem </a>
            <div class="dropdown-menu dropdown-menu-arrow">
              <a href="<? echo site_url('/admin_bahan')?>" class="dropdown-item "><i class="fe fe-shopping-bag"></i> Administrasi Bahan</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
