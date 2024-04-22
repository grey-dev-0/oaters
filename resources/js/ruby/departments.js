import {createApp} from "vue";
import common from "../common.js";
import Datatable from "../../components/datatable";
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
import Form from "../../components/form";

let $ = common.jQuery;
let app = createApp({
    data: function(){
        return {
            emitter: null,
            locale: window.locale,
            openDepartment: {
                id: 0,
                name: null
            }
        };
    },
    methods: {
        jQuery(){
            return $;
        },
        addDepartment: function(){
            this.$refs.createDepartment.show(() => {
                this.$refs.createDepartmentForm.reset();
            });
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.departmentsTable.dataTable;
        }
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');