<template>
    <div class="container" v-if="isLoggedIn">
        <div class="form-group">
            <input type="text" v-model="lekce.nazev" class="form-control" @change="putLekce" />
        </div>
        <div class="form-group">
            <textarea type="text" v-model="lekce.popis" class="form-control" placeholder="Zadejte popis..." rows="30" @change="putLekce" />
        </div>
        <h2>Verše</h2>
        <LekceVerse :lekce="lekce" />
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Nový verš..." @change="novyVers" list="knihy"/>
            <datalist id="knihy">
                <option :value="kniha" v-for="(kniha, index) of knihy" :key="index" />
            </datalist>
            <input type="text" class="form-control" placeholder="Poznámka..." @change="vlozitPoznamku" />
        </div>
    </div>
</template>
<script>
import LekceVerse from '@/components/LekceVerse.vue'

export default {
    name: 'LekceDetailView',
    components: { LekceVerse },
    computed: {
        isLoggedIn() {
            return this.$store.getters.isLoggedIn
        },
        knihy() {
            return this.$store.getters.knihy
        },
        lekce() {
            return this.$store.getters.lekceById(this.$route.params.id)
        }
    },
    methods: {
        vlozitPoznamku(e) {
            this.lekce.popis += "\n- " + e.target.value
            e.target.value = ""
            this.putLekce()
        },
        putLekce() {
            this.$store.dispatch('putLekce', this.lekce)
        },
        novyVers(e) {
            const value = e.target.value
            const match = value.match(/^(.+)\s+([0-9]+)(:([0-9]+)(-([0-9]+))?)?$/)
            if (match) {
                let [, kniha, kapitola,, from,, to] = match
                if (!from) {
                    from = 0
                    to = 1000
                }
                if (!to) {
                    to = from
                }
                this.$store.dispatch("postLekceVerse", { lekce_id: parseInt(this.$route.params.id), kniha, kapitola, from, to })
                e.target.value = ""
            }
        }
    }
}
</script>