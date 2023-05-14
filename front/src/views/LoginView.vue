<template>
    <div class="container">
        <h1 class="text-center">Přihlášení</h1>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Zadejte kód..." @change="authenticate" />
        </div>
    </div>
</template>
<script>
export default {
    name: "LoginView",
    methods: {
        authenticate(e) {
            this.$store.dispatch("authenticate", e.target.value)
        }
    }
}
</script>