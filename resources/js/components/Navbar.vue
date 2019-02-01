<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <form class="form-inline my-2 my-lg-0 mr-auto">
            <input class="form-control bg-light shadow-sm" type="search" placeholder="Search" aria-label="Search">
        </form>

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
                </div>
            </li>
        </ul>
    </nav>
</template>

<script>
  export default {
    name: 'Navbar',
    data() {
      return {
        user      : window.app.user,
        showSearch: false
      }
    }
  }
</script>

<style scoped>

</style>
