<template>
    <div>
        <Table :headers="{id:'ID', nazev: 'Název lekce' }" :data="lekce" @row-click="gotoLekce" />
        <button class="btn btn-primary" @click="postLekce">Nová lekce</button>
    </div>
</template>

<script>
import Table from '@/components/Table.vue'

export default {
    name: "LekceView",
    components: { Table },
    computed: {
        lekce() {
            return this.$store.getters.lekce
        }
    },
    methods: {
        gotoLekce(lekce) {
            this.$router.push('/lekce/' + lekce.id)
        },
        postLekce() {
            this.$store.dispatch('postLekce').then(() => {
                this.gotoLekce(this.lekce[this.lekce.length - 1])
            })
        }
    }
}
</script>