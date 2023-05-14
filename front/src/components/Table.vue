<template>
    <div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th v-for="(value, key) in headers" :key="key" @click="toggleSort(key)" :class="key === 'id' ? 'col-1' : ''">{{ value }}<span v-if="sortAttribute === key">{{ sortDirection === "asc" ? "^" : "v" }}</span></th>
          </tr>
          <tr>
            <th v-for="(value, key) in headers" :key="key">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search"
                    v-model="searchText[key]"
                >    
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in filteredData" :key="index" class="table-row">
            <td v-for="(header, key, index) in headers" :key="index" @click="$emit('row-click', row)" :class="style(row)" v-html="row[key]" />
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      rowStyle: {
        type: Function,
        required: false,
      },
      data: {
        type: Array,
        required: true,
      },
      headers: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        sortAttribute: null,
        sortDirection: 'asc',
        searchText: Object.keys(this.headers).reduce((acc, curr) => {
            acc[curr] = "";
            return acc;
        }, {}),
      };
    },
    computed: {
      style() {
        return this.rowStyle ? this.rowStyle : () => false
      },

      filteredData() {
        let data = this.data
        for (const [key, value] of Object.entries(this.searchText)) {
          const filter = value.toLowerCase();
          data = data.filter(row => {
            if (key in row) {
                const strval = "" + row[key]
                return strval.toLowerCase().includes(filter)
            } else {
                return true
            }            
          });
        }
        
  
        if (this.sortAttribute) {
          data.sort((a, b) => {
            let comparison = 0;
            if (a[this.sortAttribute] > b[this.sortAttribute]) {
              comparison = 1;
            } else if (a[this.sortAttribute] < b[this.sortAttribute]) {
              comparison = -1;
            }
            return this.sortDirection === 'desc' ? comparison * -1 : comparison;
          });
        }
  
        return data;
      },
    },
    methods: {
      toggleSort(attribute) {
        if (this.sortAttribute === attribute) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortAttribute = attribute;
          this.sortDirection = 'asc';
        }
      },
    },
  };
  </script>
  

<style>
table.table.table-bordered tr.table-row:hover {
    background-color: #DFD;
    cursor: pointer;
}
</style>