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
                $.get(window.baseUrl + '/departments/' + view.openDepartment.id).then(response => {
                    let text;
                    if(response.head[0]){
                        text = response.head[0].name + ' - ';
                        if(response.head[0].emails[0])
                            text += response.head[0].emails[0].address;
                    }
                    view.$refs.updateDepartmentForm.reset({
                        'en[name]': response.name,
                        'ar[name]': response.name_ar,
                        manager_id: {
                            id: response.head[0] && response.head[0].id,
                            text
                        },
                        contact_id: (() => {
                            let employees = {};
                            response.employees.forEach(employee => {
                                employees[employee.id] = employee.name;
                            });
                            return employees;
                        })()
                    });
                });
            }));
        });
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');