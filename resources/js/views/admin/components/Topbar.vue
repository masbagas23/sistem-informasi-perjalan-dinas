<template>
<div>
  <div
    class="
      navbar navbar-expand navbar-light
      bg-white
      topbar
      mb-4
      shadow
    "
  >
    <!-- Sidebar Toggle (Topbar) -->
    <button
      id="sidebarToggleTop"
      class="btn btn-link rounded-circle mr-3"
    >
      <i class="fa fa-bars"></i>
    </button>
    <!-- Topbar Search -->
    <!-- <form
      class="
        d-none d-sm-inline-block
        form-inline
        mr-auto
        ml-md-3
        my-2 my-md-0
        mw-100
        navbar-search
      "
    >
      <div class="input-group">
        <input
          type="text"
          class="form-control bg-light border-0 small"
          placeholder="Search for..."
          aria-label="Search"
          aria-describedby="basic-addon2"
        />
        <div class="input-group-append">
          <button class="btn btn-primary" type="button">
            <i class="fas fa-search fa-sm"></i>
          </button>
        </div>
      </div>
    </form> -->

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
      <!-- Nav Item - Search Dropdown (Visible Only XS) -->
      <!-- <li class="nav-item dropdown no-arrow d-sm-none">
        <a
          class="nav-link dropdown-toggle"
          href="#"
          id="searchDropdown"
          role="button"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <i class="fas fa-search fa-fw"></i>
        </a> -->
        <!-- Dropdown - Messages -->
        <!-- <div
          class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
          aria-labelledby="searchDropdown"
        > -->

          <!-- <form class="form-inline mr-auto w-100 navbar-search">
            <div class="input-group">
              <input
                type="text"
                class="form-control bg-light border-0 small"
                placeholder="Search for..."
                aria-label="Search"
                aria-describedby="basic-addon2"
              />
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form> -->
        <!-- </div>
      </li> -->

      <!-- <li class="nav-item align-self-center">
        <div id="myNotifications"></div>
      </li> -->

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow">
        <div id="myNotifications"></div>
        <a
          class="nav-link dropdown-toggle"
          href="#"
          id="userDropdown"
          role="button"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <span class="mr-2 d-none d-lg-inline text-gray-600 small"
            >{{ user.first_name }} {{ user.last_name }}</span
          >
          <img
            class="img-profile rounded-circle"
            :src="user.avatar_url"
          />
        </a>
        <!-- Dropdown - User Information -->
        <div
          class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
          aria-labelledby="userDropdown"
        >
          <a class="dropdown-item" href="#">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
          </a>
          <div class="dropdown-divider"></div>
          <a
            class="dropdown-item"
            href="javascript:void(0)"
            @click="logout"
            data-toggle="modal"
            data-target="#logoutModal"
          >
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
            Logout
          </a>
        </div>
      </li>
    </ul>
  </div>
  <nav v-if="$route.meta.disableBreadcrumb === undefined" aria-label="breadcrumb">
    <div class="row px-4 d-flex align-items-center">
      <div class="col-6">
        <h3>{{$route.meta.title}}</h3>
      </div>
      <div class="col-6 text-right">
        <ol class="bg-light breadcrumb d-flex justify-content-end">
          <li v-if="$route.meta.module !== undefined" class="breadcrumb-item" :class="{'text-primary': $route.meta.title !== undefined}">{{$route.meta.module}}</li>
          <li v-if="$route.meta.title !== undefined" class="breadcrumb-item">{{$route.meta.title}}</li>
        </ol>
      </div>
    </div>
  </nav>
</div>
</template>

<script>
import { mapGetters } from "vuex";
import NotificationAPI from 'notificationapi-js-client-sdk';

export default {
  name: "Topbar",
  computed: {
    ...mapGetters(["user"]),
  },
  mounted(){
    const notificationapi = new NotificationAPI({
      clientId: '585rb2fl4je58k1ut46oviauc8',
      userId: this.user.email
    });

    notificationapi.showInApp({
      root: 'myNotifications',
      popupPosition: 'bottomLeft',
    });
  },
  methods: {
    logout() {
      localStorage.removeItem("token");
      this.$store.dispatch("user", null);
      this.$router.push("/login");
    },
  },
};
</script>
