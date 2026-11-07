import {createApp} from "vue";
import common, {jQuery as $, renderVueTemplate} from "../common.js";
import Datatable from "../../components/datatable";
import List from "../../components/list/index.js";
import Timeline from "../../components/timeline/index.js";
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
        renderDepartments(row){
            let departments = [], i;
            for(i in row.contact.departments)
                departments.push(`<p class="m-0 text-primary">${row.contact.departments[i].name}</p>`);
            for(i in row.contact.managed_departments)
                departments.push(`<p class="m-0 text-info">${row.contact.managed_departments[i].name}</p>`);
            return departments.length? departments.join('') : `<small class="text-muted">${window.locale.common.unassigned}</small>`;
        },
        closeContact(){
            this.openContact.addresses = undefined;
        },
        openSettings(){
            this.$refs.attendanceSettings.show(() => {
                $.get(`${baseUrl}/attendance/settings`).then(response => {
                    this.$refs.attendanceSettingsForm.reset({threshold: response.threshold});
                });
            });
        },
        submitSettings(){
            this.$refs.attendanceSettingsForm.submit().then(response => {
                this.$refs.attendanceSettings.hide();
                this.toast = {
                    color: 'success',
                    content: response.message
                };
                this.dataTable.draw();
                this.$nextTick(() => this.$refs.toast.show());
            });
        },
        exportTable(format){
            $.ajax({
                url: `${baseUrl}/attendance/export`,
                method: 'POST',
                data: {...this.dataTable.ajax.params(), format},
                xhrFields: {responseType: 'blob'}
            }).then(blob => {
                let url = URL.createObjectURL(blob), link = document.createElement('a');
                link.href = url;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(url);
            });
        },
        renderActions(){
            let view = this, appInstance = this.$appInstance;
            const showProfile = e => {
                view.openContact = view.dataTable.row($(e.target).closest('tr')).data().contact;
                view.$nextTick(() => {
                    view.$refs.profileModal.show(() => {
                        $.get(`${baseUrl}/contacts/${view.openContact.id}`).then(response => {
                            view.openContact = response;
                        });
                    });
                });
            };
            $('[vue-template]').each(function(){
                renderVueTemplate(this, appInstance, {methods: {showProfile}});
            });
        }
    },
    computed: {
        dataTable(){
            return this.$refs.attendanceTable.dataTable;
        },
        tenureYears(){
            return this.openContact.applicant && this.openContact.applicant.tenure
                && parseInt(this.openContact.applicant.tenure / 12.0) || 0;
        },
        tenureMonths(){
            return this.openContact.applicant && this.openContact.applicant.tenure
                && (this.openContact.applicant.tenure % 12) || 0;
        }
    }
}), bundles = [Datatable, Timeline, List], components = {Modal: 'modal'};

app.config.globalProperties.$appInstance = app;
common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');