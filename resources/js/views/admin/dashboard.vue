<template>
  <div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
      <!-- <a
        href="#"
        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
        ><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a
      > -->
    </div>

    <!-- Content Row -->
    <div class="row">
      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-4 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="
                    text-xs
                    font-weight-bold
                    text-primary text-uppercase
                    mb-1
                  "
                >
                  PERJALAN DINAS (BULANAN)
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  10 PERJALAN
                </div>
              </div>
              <div class="col-auto">
                <i class="fa fa-suitcase fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-4 col-md-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="
                    text-xs
                    font-weight-bold
                    text-success text-uppercase
                    mb-1
                  "
                >
                  Biaya Pengeluaran (Bulanan)
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  Rp 3.453.250
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Earnings (Monthly) Card Example -->
      <!-- <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-info text-uppercase mb-1"
                >
                  Tugas (SELESAI)
                </div>
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                      50%
                    </div>
                  </div>
                  <div class="col">
                    <div class="progress progress-sm mr-2">
                      <div
                        class="progress-bar bg-info"
                        role="progressbar"
                        style="width: 50%"
                        aria-valuenow="50"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div> -->

      <!-- Pending Requests Card Example -->
      <div class="col-xl-4 col-md-12 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="
                    text-xs
                    font-weight-bold
                    text-warning text-uppercase
                    mb-1
                  "
                >
                  Pinjam Kendaraan (BULANAN)
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-car fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-6">
        <topCustomerComponent/>
      </div>
      <!-- <div class="col-6">
        <topCostCategoryComponent/>
      </div> -->
    </div>
  </div>
</template>

<script>
import chartAreaDemo from "../../chart/demo/chart-area-demo";
import chartPieDemo from "../../chart/demo/chart-pie-demo";
import topCustomerComponent from "./others/top-customer.vue"
import topCostCategoryComponent from "./others/top-cost-category.vue"

export default {
  name: "Dashboard",
  mounted() {
    // chartAreaDemo();
    // chartPieDemo();
  },
  components:{
    topCustomerComponent,
    topCostCategoryComponent
  }
};
</script>
