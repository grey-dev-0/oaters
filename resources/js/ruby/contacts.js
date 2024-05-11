import {createApp} from "vue";
import common from "../common.js";
import Datatable from "../../components/datatable/index.js";
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
select2();

let $ = common.jQuery, app = createApp({
    data(){
        return {
            roles, departments,
            toast: {
                color: 'primary',
                content: null
            }
        };
    },
    methods: {
        jQuery(){
            return $;
        },
        bootbox(){
            return common.bootbox;
        },
        renderDepartments(row){
            let departments = [], i;
            for(i in row.departments)
                departments.push(`<p class="m-0 text-primary">${row.departments[i].name}</p>`);
            for(i in row.managed_departments)
                departments.push(`<p class="m-0 text-info">${row.managed_departments[i].name}</p>`);
            return departments.length? departments.join('') : `<small class="text-muted">${window.locale.common.unassigned}</small>`;
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.contactsTable.dataTable;
        }
    }
}), bundles = [Datatable];

common.load(app);
common.loadBundles(app, bundles);
app.mount('#app');