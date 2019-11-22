<div class="header py-4">
  <div class="container">
    <div class="d-flex">
      <!-- COMPANY LOGO -->
      <a class="header-brand" href="<? echo base_url('') ?>">
        <img src="<? echo base_url('/assets/images/Logo BCN - SIMTR.png')?>" class="header-brand-img" alt="tabler logo">
      </a>
      <!-------------------->
      <div class="d-flex order-lg-2 ml-auto">
        <!-- NOTIFICATION AREA -->
        <div class="dropdown d-none d-md-flex">
          <a class="nav-link icon" data-toggle="dropdown">
            <i class="fe fe-bell"></i>
            <span class="nav-read"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
            <!-- ISI NOTIFIKASI
            <a href="#" class="dropdown-item d-flex">
              <span class="avatar mr-3 align-self-center" style="background-image: url(demo/faces/male/41.jpg)"></span>
              <div>
                <strong>Nathan</strong> pushed new commit: Fix page load performance issue.
                <div class="small text-muted">10 minutes ago</div>
              </div>
            </a>
            <a href="#" class="dropdown-item d-flex">
              <span class="avatar mr-3 align-self-center" style="background-image: url(demo/faces/female/1.jpg)"></span>
              <div>
                <strong>Alice</strong> started new task: Tabler UI design.
                <div class="small text-muted">1 hour ago</div>
              </div>
            </a>
            <a href="#" class="dropdown-item d-flex">
              <span class="avatar mr-3 align-self-center" style="background-image: url(demo/faces/female/18.jpg)"></span>
              <div>
                <strong>Rose</strong> deployed new version of NodeJS REST Api V3
                <div class="small text-muted">2 hours ago</div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item text-center">Mark all as read</a>
            -->
          </div>
        </div>
        <!---------------------->
        <!-- USER INFO -->
        <? $loggedUser = (object) $this->session->all_userdata(); ?>
        <div class="dropdown">
          <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
            <span class="avatar" style="background-image: url()"></span>
            <span class="ml-2 d-none d-lg-block">
              <span class="text-default"><? echo $loggedUser -> nama_user; ?></span>
              <small class="text-muted d-block mt-1"><? echo $loggedUser -> jabatan; ?></small>
              <small class="text-muted d-block mt-1"><? echo (($loggedUser->afd) !== NULL ? 'Afdeling '.$loggedUser -> afd : ''); ?></small>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
            <a class="dropdown-item" href="<? echo site_url('/landing/logout')?>">
              <i class="dropdown-icon fe fe-log-out"></i> Sign out
            </a>
          </div>
        </div>
        <!----------------------------------------------------------->
      </div>
      <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
        <span class="header-toggler-icon"></span>
      </a>
    </div>
  </div>
</div>
