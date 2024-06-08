import {createApp} from "vue";
import common, {jQuery as $} from "../common.js";
import Datatable from "../../components/datatable";
import Form from "../../components/form";
import List from "../../components/list";
import Tab from '../../components/tab';
import Timeline from '../../components/timeline';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
select2();

let app = createApp({
    data(){
        return {
            roles, departments, locale,
            toast: {
                color: 'primary',
                content: null
            },
            openContact: {
                id: null,
                name: null
            }
        };
    },
    methods: {
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
        },
        closeContact(){
            this.openContact.addresses = undefined;
        }
    },
    computed: {
        dataTable(){
            return this.$refs.contactsTable.dataTable;
        },
        tenureYears(){
            return this.openContact.applicant && this.openContact.applicant.tenure
                && parseInt(this.openContact.applicant.tenure / 12.0) || 0;
        },
        tenureMonths(){
            return this.openContact.applicant && this.openContact.applicant.tenure
                && (this.openContact.applicant.tenure % 12) || 0;
        }
    },
    mounted(){
        let view = this;
        $('body').on('click', '.profile', function(){
            view.openContact = view.dataTable.row($(this).closest('tr')).data();
            view.$nextTick(() => {
                view.$refs.profileModal.show(() => {
                    $.get(`${baseUrl}/contacts/${view.openContact.id}`).then(response => {
                        view.openContact = response;
                    });
                });
            });
        });
    }
}), bundles = [Datatable, Form, Timeline, List, Tab], components = {Modal: 'modal'};

common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');