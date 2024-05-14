import {createApp} from "vue";
import common from "../common.js";
import Datatable from "../../components/datatable";
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
import Form from "../../components/form";
select2();

let $ = common.jQuery;
let app = createApp({
    data: function(){
        return {
            roles,
            emitter: null,
            locale: window.locale,
            toast: {
                color: 'primary',
                content: null
            },
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
            this.$refs[modal + 'Form'].submit().then(response => {
                this.$refs[modal].hide();
                this.toast = {
                    color: 'success',
                    content: response.message
                };
                this.dataTable.draw();
                this.$nextTick(() => this.$refs.toast.show());
            });
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
                view.$refs.updateDepartmentForm.loading = true;
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
        }).on('click', '.contacts', function(){
            view.openDepartment.id = $(this).data('id');
            view.openDepartment.name = view.dataTable.row($(this).closest('tr')).data().name;
            view.$nextTick(() => view.$refs.contactsModal.show(() => view.$refs.contactsTable.init()));
        });
    }
}), bundles = [Datatable, Form], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');