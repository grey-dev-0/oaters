import {createApp} from "vue";
import common from "../common.js";
import Datatable from "../../components/datatable";
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';

let $ = common.jQuery;
let app = createApp({
    data: function(){
        return {
            emitter: null
        };
    },
    methods: {
        jQuery(){
            return $;
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.departmentsTable.dataTable;
        }
    }
}), bundles = [Datatable];

common.load(app);
common.loadBundles(app, bundles);
app.mount('#app');