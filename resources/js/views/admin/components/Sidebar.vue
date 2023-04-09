<template>
  <div>
    <div
      id="scrollbar2"
      style="max-height: 100vh; overflow: auto; overflow-x: hidden"
    >
      <ul
        class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
        id="accordionSidebar"
      >
        <!-- Sidebar - Brand -->
        <router-link
          class="mb-0 pb-0 sidebar-brand d-flex justify-content-center"
          to="/"
        >
          <div class="sidebar-brand-icon">
            <img src="/images/icon.svg" width="42vw" />
          </div>
          <div class="sidebar-brand-text"><h2>SIPDK</h2></div>
        </router-link>
        <small class="text-center text-light mx-1 mt-0 pt-0 mb-2"
          ><b>Sistem Informasi Perjalan Dinas Karyawan</b></small
        >

        <!-- Divider -->
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></router-link
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider" />

        <!-- Heading -->
        <div class="sidebar-heading">Transaksi</div>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fas fa-fw fa-table"></i>
            <span>Surat Perintah Kerja</span></router-link
          >
        </li>
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
          <a
            class="nav-link collapsed"
            href="#"
            data-toggle="collapse"
            data-target="#transaction"
            aria-expanded="true"
            aria-controls="transaction"
          >
            <i class="fa fa-plane rotate-n-15"></i>
            <span>Perjalan Dinas</span>
          </a>
          <div
            id="transaction"
            class="collapse"
            aria-labelledby="headingTwo"
            data-parent="#accordionSidebar"
          >
            <div class="bg-primary bg-gradient py-2 collapse-inner rounded">
              <!-- <h6 class="collapse-header">Custom Components:</h6> -->
              <router-link
                class="bg-primary text-light collapse-item"
                to="/admin/components/buttons"
                >Perencaan</router-link
              >
              <router-link
                class="bg-primary text-light collapse-item"
                to="/admin/components/cards"
                >Realisasi</router-link
              >
              <router-link
                class="bg-primary text-light collapse-item"
                to="/admin/components/cards"
                >Evaluasi</router-link
              >
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a
            class="nav-link collapsed"
            href="#"
            data-toggle="collapse"
            data-target="#finance"
            aria-expanded="true"
            aria-controls="finance"
          >
            <i class="fa fa-credit-card"></i>
            <span>Keuangan</span>
          </a>
          <div
            id="finance"
            class="collapse"
            aria-labelledby="headingTwo"
            data-parent="#accordionSidebar"
          >
            <div class="bg-primary py-2 collapse-inner rounded">
              <!-- <h6 class="collapse-header">Custom Components:</h6> -->
              <router-link
                class="bg-primary text-light collapse-item"
                to="/admin/components/cards"
                >Penyerahan Biaya</router-link
              >
              <router-link
                class="bg-primary text-light collapse-item"
                to="/admin/components/buttons"
                >Reimburse</router-link
              >
            </div>
          </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider" />

        <!-- Heading -->
        <div class="sidebar-heading">Laporan</div>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fa fa-file"></i>
            <span>Rekap Perjalan Dinas</span></router-link
          >
        </li>
        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fa fa-file"></i>
            <span>Rekap Biaya Pengeluaran</span></router-link
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider" />

        <!-- Heading -->
        <div class="sidebar-heading">Kelola Pengguna</div>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fa fa-users"></i>
            <span>Pengguna</span></router-link
          >
        </li>
        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fa fa-info-circle"></i>
            <span>Hak Akses</span></router-link
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider" />

        <!-- Heading -->
        <div class="sidebar-heading">Data Master</div>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/projects">
            <i class="fas fa-fw fa-table"></i>
            <span>Projek</span></router-link
          >
        </li>
        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fas fa-fw fa-table"></i>
            <span>Kategori Pekerjaan</span></router-link
          >
        </li>
        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fas fa-fw fa-table"></i>
            <span>Kategori Biaya</span></router-link
          >
        </li>
        <!-- Nav Item - Tables -->
        <li class="nav-item">
          <router-link class="nav-link" to="/admin/tables">
            <i class="fas fa-fw fa-table"></i>
            <span>Kendaraan Inventaris</span></router-link
          >
        </li>
      </ul>
    </div>
    <!-- End of Sidebar -->
  </div>
</template>

<script>
export default {
  name: "Sidebar",
};
</script>
<style scoped>
::-webkit-scrollbar {
  width: 5px;
}

::-webkit-scrollbar-track {
  border-radius: 8px;
  background-color: #e7e7e7;
  border: 1px solid #cacaca;
  box-shadow: inset 0 0 6px rgba(77, 77, 77, 0.2);
}

::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 10rem;
  border: 1px solid #fff;
}
::-webkit-scrollbar-track-piece:start {
  background: transparent;
}

::-webkit-scrollbar-track-piece:end {
  background: transparent;
}
</style>
