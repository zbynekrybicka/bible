<template>
    <div v-for="vers of verse" class="card mb-3">
        <div class="card-header">
            <b contenteditable="true" @blur="putVerse($event, vers.id)">{{ vypisVers(vers) }}</b>

            <a href="#" @click.prevent="deleteVers(vers)" class="font-weight-bold float-right text-dark">X</a>
        </div>
        <div class="card-body">
            <div class="form-group">
                <template v-for="(obsah, cislo) of vers.obsah"><sup>{{ cislo }}</sup>{{ obsah }}&nbsp;</template>
                <a href="#" @click.prevent="zobrazitNovyVyraz(vers.id)" v-if="!novyVyrazZobrazit[vers.id]">(+)</a>
            </div>
            <div class="form-group" v-if="novyVyrazZobrazit[vers.id]">
                <input type="text" ref="novyVyraz" list="novyVyraz" autocomplete="off" class="form-control" placeholder="Nový výraz..." @change="novyVyraz($event, vers)" @blur="novyVyrazZobrazit[vers.id] = false"/>
                <datalist id="novyVyraz">
                    <option v-for="(vyraz, index) of slovnikAvailable(vers.id)" :value="vyraz" :key="index"/>
                </datalist>
            </div>
            <div class="form-group" v-for="(vyraz, index) of slovnik(vers.id)" :key="index">
                <div class="row">
                    <div class="col-sm-3 font-weight-bold">{{ vyraz.vyraz }}</div>
                    <div class="col-sm-9">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" :id="'vyznam' + index" v-model="vyraz.vyznam" placeholder="Význam..." @change="putVyznam($event, vyraz)" />
                            <div class="input-group-append">
                                <span class="input-group-text bg-danger text-light font-weight-bold" @click="deleteVyraz(vers, vyraz)">X</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        </div>

        <div class="card-footer font-weight-bold" contenteditable @blur="putVysvetleni($event, vers)" @keyup.enter="putVysvetleni($event, vers); $event.target.blur()">{{ vers.vysvetleni }}</div>
    </div>
</template>
<script>
export default {
    name: "LekceVerse",
    props: {
        lekce: {
            type: Object,
            required: true
        }
    }, 
    data() {
        return {
            novyVyrazZobrazit: {}
        }
    },
    computed: {
        verse() {
            return this.$store.getters.verseByLekceId(this.lekce.id)
        }
    },
    methods: {
        slovnikAvailable(id) {
            return this.$store.getters.slovnikAvailableByVersId(id)
        },
        zobrazitNovyVyraz(id) {
            this.novyVyrazZobrazit[id] = true; 
            this.$nextTick(() => {
                this.$refs.novyVyraz[0].focus()
            })
        },
        slovnik(id) {
            return this.$store.getters.slovnikByVersId(id)
        },
        putVysvetleni(e, vers) {
            this.$store.dispatch('putLekceVerse', { id: vers.id, vysvetleni: e.target.innerText })
        },
        putVyznam(e, vyraz) {
            this.$store.dispatch('putSlovnik', vyraz)
        },
        novyVyraz(e, vers) {
            this.$store.dispatch('postSlovnik', { lekce_vers_id: vers.id, vyraz: e.target.value })
            e.target.value = ""
            this.$refs.novyVyraz[0].blur()
        },
        deleteVers(vers) {
            if (confirm('Opravdu chcete smazat celý tento verš včetně připojených výrazů?')) {
                this.$store.dispatch('deleteVers', vers.id)
            }
        },
        deleteVyraz(vers, vyraz) {
            if (confirm('Opravdu chcete odpojit tento výraz?')) {
                this.$store.dispatch('deleteLekceVerseSlovnik', { slovnik_id: vyraz.id, lekce_vers_id: vers.id })
            }
        },
        putVerse(e, id) {
            const value = e.target.innerText
            if (value.match(/\s+[0-9]+(:[0-9]+(-[0-9]+)?)?$/)) {
                const [kniha, verse] = value.split(/\s+/)
                const [kapitola, vers] = verse.split(":")
                let from = 0, to = 1000
                if (vers) {
                    [from, to] = vers.split("-")
                    if (!to) {
                        to = from
                    }
                }
                this.$store.dispatch("putLekceNovyVers", { id, kniha, kapitola, from, to })
                e.target.value = ""
            }
        },
        vypisVers(vers) {
            return vers.nazev + " " + vers.kapitola + (vers.from > 0 ? ":" + vers.from + (vers.to !== vers.from ? '-' + vers.to : '') : '')
        }

    }
}
</script>