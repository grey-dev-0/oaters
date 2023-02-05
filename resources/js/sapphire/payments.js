import {createApp} from "vue";
import Datatable from "../../components/datatable";
let libraries = Object.assign({}, Datatable);

let app = createApp({
    data: function(){
        return {
            locale: window.locale
        }
    },
    methods: {
        renderAmount: function(amount){
            return '$' + amount;
        },
        renderExecuted: function(executed){
            return (executed == 1)? locale.common.yes : locale.common.no;
        },
        renderPaidAt: function(row){
            return (row['executed'] == 1)? row['updated_at'] : locale.common.unpaid;
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.paymentsTable.dataTable;
        }
    }
});

import(
    /* webpackPreload: true */
    /* webpackChunkName: "js/common" */
    '../../components/common'
    ).then((common) => {
    common.load(app);
    for(let component in libraries)
        app.component(component, libraries[component]);
    app.mount('#app');
});