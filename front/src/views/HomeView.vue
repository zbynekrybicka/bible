<template>
  <div class="container">
    <LoginView v-if="!isLoggedIn" />
    <LekceView v-if="isLoggedIn" />
  </div>
</template>

<script>
// @ is an alias to /src
import LekceView from './LekceView'
import LoginView from './LoginView'

export default {
  name: 'HomeView',
  components: { LekceView, LoginView },
  computed: {
    isLoggedIn() {
      return this.$store.getters.isLoggedIn
    }
  }

}
</script>
