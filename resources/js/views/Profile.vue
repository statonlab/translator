<template>
    <div>
        <div class="card border-0 shadow">
            <div class="card-header bg-white border-0 p-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <ion-icon name="contact"></ion-icon>
                            <span>Personal Information</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <ion-icon name="lock"></ion-icon>
                            <span>Password</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'Profile',

    mounted() {
      setTimeout(() => this.$root.$emit('changeTitle', 'Profile'), 100)
    }
  }
</script>

<style scoped>

</style>
