import common from "../common.js";
import {createApp} from "vue";
import Datatable from "../../components/datatable";
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';

let app = createApp({
    data: function(){
        return {
            locale: window.locale
        }
    },
    methods: {
        jQuery(){
            return common.jQuery;
        },
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
}), bundles = [Datatable];

common.load(app);
common.loadBundles(app, bundles);
app.mount('#app');