<template>
  <div>
    <!-- Content Row -->
    <div class="row">
      <div class="col-12">
        <!-- Bar Chart -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Kategori Biaya</h6>
          </div>
          <div class="card-body">
            <div class="chart-bar">
              <canvas id="myBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import chartAreaDemo from "../../../chart/demo/chart-area-demo";
import chartPieDemo from "../../../chart/demo/chart-pie-demo";
import chartBarDemo from "../../../chart/demo/chart-bar-demo";

export default {
  name: "Charts",
  mounted() {
    chartAreaDemo();
    chartPieDemo();
    chartBarDemo();
  },
};
</script>