<template>
  <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="/"> Sloppy Seconds </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle Navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav me-auto"></ul>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ms-auto">
          <Button @click="toggleDarkMode"><i class="pi pi-sun"></i></Button>
          <!-- Authentication Links -->
          <template v-if="$page.props.auth.user">
            <li class="nav-item dropdown">
              <a
                id="navbarDropdown"
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                v-pre
              >
                {{ $page.props.auth.user.name }}
              </a>

              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a
                  class="dropdown-item"
                  href="{{ route('logout') }}"
                  onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                >
                  Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
              </div>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <login></login>
            </li>

            <li class="nav-item">
              <register></register>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
  <slot />
</template>

<script>
import { Link } from "@inertiajs/vue3";
import Button from "primevue/button";
import Menubar from "primevue/menubar";
import Login from "../components/Login.vue";
import Register from "../components/Register.vue";

export default {
  components: {
    Button,
    Menubar,
    Link,
    Login,
    Register,
  },
  methods: {
    toggleDarkMode() {
      document.documentElement.classList.toggle("dark-mode");
    },
  },
};
</script>
