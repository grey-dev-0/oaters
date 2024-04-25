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
        bootbox(){
            return common.bootbox;
        },
        addDepartment: function(){
            this.$refs.createDepartment.show(() => {
                this.$refs.createDepartmentForm.reset();
            });
        },
        submit(modal){
            this.$refs[modal + 'Form'].submit().then(() => this.$refs[modal].hide());
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.departmentsTable.dataTable;
        }
    },
    mounted(){
        let view = this;
        $('body').on('click', '.edit', function(){
            view.openDepartment.id = $(this).data('id');
            view.openDepartment.name = view.dataTable.row($(this).closest('tr')).data().name;
            view.$nextTick(() => view.$refs.updateDepartment.show(() => {
                view.$refs.updateDepartmentForm.reset({
                    'en[name]': view.openDepartment.name,
                    contact_id: {
                        1: 'Test A',
                        2: 'Test B'
                    }
                });
            }));
        });
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');