<template>
    <nav class="main-navbar navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="container">
            <h1 class="navbar-text mb-0 font-weight-bold text-dark h5">
                {{ title }}
            </h1>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown"
                       class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false">
                        {{ user.name }} <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <router-link to="/" class="dropdown-item">
                            Profile
                        </router-link>
                        <a href="/logout" class="dropdown-item">
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
  export default {
    name : 'Navbar',

    props: {title: {type: String, required: false, default: ''}},

    data() {
      return {
        user      : window.app.user,
        showSearch: false,
      }
    }
  }
</script>

<style scoped>

</style>
