<template>
  <nav v-if="isLoggedIn">
    <router-link to="/">Lekce</router-link> |
    <a href="#" @click.prevent="logout">Odhlásit se</a>
  </nav>
  <router-view/>
</template>
<script>
export default {
  name: "App",
  computed: {
    isLoggedIn() {
      return this.$store.getters.isLoggedIn
    },
  },
  methods: {
    logout() {
      this.$store.commit("logout")
    }
  },
  created() {
    this.$store.dispatch("loadAuthToken")
  }
}
</script>
<style>
nav {
  text-align: center;
  padding: 15px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}
</style>
