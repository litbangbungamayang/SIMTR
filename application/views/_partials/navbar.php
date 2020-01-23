<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="header d-lg-flex p-0">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-9 order-lg-first">
        <ul class="nav nav-tabs border-0 flex-lg-row">
          <li class="nav-item" style="background: teal; color: white;">SIMTR</li>
          <li class="nav-item">
            <a href="<? echo base_url('') ?>" class="nav-link <? echo $this->uri->segment(1) == '' ? 'active' : '' ?>"><i class="fe fe-home"></i> Home</a>
          </li>
          <li class="nav-item dropdown" style="display: ">
            <a href="javascript:void(0)" class="nav-link <? echo ($this->uri->segment(1) == 'rdkk_add')||($this->uri->segment(1) == 'rdkk_all') ? 'active' : '' ?>" data-toggle="dropdown"><i class="fe fe-book-open"></i> Data Kelompok Tani</a>
            <div class="dropdown-menu dropdown-menu-arrow">
              <a href="<? echo site_url('/rdkk_add')?>" class="dropdown-item " style="<? echo ($this->session->userdata('afd') == '') ? 'display:none' : ''; ?>"><i class="fe fe-search"></i> Pendaftaran RDKK</a>
              <a href="<? echo site_url('/rdkk_all')?>" class="dropdown-item "><i class="fe fe-edit"></i> Penelusuran Data</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-sunset"></i> Pupuk</a>
            <div class="dropdown-menu dropdown-menu-arrow">
              <a href="<? echo site_url('/biaya_muat_angkut_pupuk')?>" class="dropdown-item "><i class="fe fe-sunset"></i> Pengajuan Biaya Muat & Angkut Pupuk</a>
              <a href="<? echo site_url('/list_pbma')?>" class="dropdown-item "><i class="fe fe-sunset"></i> Penelusuran BMA-P</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-feather"></i> Perawatan</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-zap"></i> TMA</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-layers"></i> Bagi Hasil</a>
            <div class="dropdown-menu dropdown-menu-arrow">
            </div>
          </li>
          <? $loggedUser = (object) $this->session->all_userdata();?>
          <li class="nav-item dropdown" style="display:<? echo ($loggedUser->jabatan == 'Superadmin' || $loggedUser->jabatan == 'Admin')? '' : 'none'; ?>">
            <a href="javascript:void(0)" class="nav-link <? echo ($this->uri->segment(1) == 'admin_bahan')||($this->uri->segment(1) == 'admin_vendor')||($this->uri->segment(1) == 'transaksi_bahan') ? 'active' : '' ?>" data-toggle="dropdown"><i class="fe fe-settings"></i> Administrasi Sistem </a>
            <div class="dropdown-menu dropdown-menu-arrow">
              <a href="<? echo site_url('/admin_bahan')?>" class="dropdown-item "><i class="fe fe-shopping-bag"></i> Master Bahan</a>
              <a href="<? echo site_url('/admin_vendor')?>" class="dropdown-item "><i class="fe fe-users"></i> Master Vendor</a>
              <a href="<? echo site_url('/admin_aktivitas')?>" class="dropdown-item "><i class="fe fe-activity"></i> Master Aktivitas</a>
              <a href="<? echo site_url('/transaksi_bahan')?>" class="dropdown-item "><i class="fe fe-tag"></i> Transaksi Bahan</a>
            </div>
          </li>
        </ul>
      </div>
      <div class="col-3 text-right ">
        <? $loggedUser = (object) $this->session->all_userdata(); ?>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link pr-0" data-toggle="dropdown"><i class="fe fe-user mr-2"></i> Akun</a>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
            <div class="dropdown-header text-default"><? echo $loggedUser -> nama_user; ?></div>
            <div class="dropdown-header text-muted" style="margin-top: -10px;"><? echo $loggedUser -> jabatan; ?></div>
            <div class="dropdown-header text-muted" style="margin-top: -10px;"><? echo (($loggedUser->afd) !== NULL ? ' Afdeling '.$loggedUser -> afd : ''); ?></div>
            <a class="dropdown-item" href="<? echo site_url('/landing/logout')?>">
              <i class="dropdown-icon fe fe-log-out"></i> Log out
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
