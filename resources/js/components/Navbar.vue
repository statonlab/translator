<template>
    <nav class="main-navbar navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="container">
            <h1 class="navbar-text mb-0 font-weight-bold text-dark h5">
                {{ title }}
            </h1>
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item">
                    <router-link
                            to="/profile"
                            class="nav-link"
                            href="#"
                            role="button">
                        <letter-icon :value="user.name" class="d-inline-flex mr-1" :small="true"/>
                        {{ user.name }}
                    </router-link>
                </li>
                <li class="nav-item">
                    <a href="/logout" class="nav-link">
                        <ion-icon name="log-out"></ion-icon>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
  import LetterIcon from './LetterIcon'

  export default {
    name      : 'Navbar',
    components: {LetterIcon},
    props     : {title: {type: String, required: false, default: ''}},

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
