<template>
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column" style="max-height: 100vh; overflow: auto; overflow-x: hidden">
      <!-- Main Content -->
      <div id="content">
        <!-- Start of Topbar -->
        <Topbar />
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <router-view></router-view>
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>&copy; {{thisYear}} Bagasraga. Hak cipta dilindungi Undang-undang.</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->

  <!-- Logout Modal-->
</template>

<script>
import sbadmin2 from "@app/utils/sb-admin-2.js";
import axios from "axios";
import moment from "moment";
import { mapGetters } from "vuex";
import Topbar from "../components/Topbar";
import Sidebar from "../components/Sidebar";

export default {
  name: "AdminLayout",
  components: {
    Topbar,
    Sidebar
  },
  data(){
    return{
      thisYear: moment().format("Y"),
    }
  },
  mounted() {
    sbadmin2.init();
  },
  computed: {
    ...mapGetters(["user"]),
  },
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