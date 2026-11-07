import {createApp} from "vue";
import common, {jQuery as $, bootbox, renderVueTemplate} from "../common.js";
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
            roles, departments, statuses, locale, citiesUrl,
            toast: {
                color: 'primary',
                content: null
            },
            openContact: {
                id: null,
                name: null
            },
            activeForm: null,
            form: {}
        };
    },
    methods: {
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
        },
        emptyForm(){
            this.form = {
                roles: [],
                emails: [{address: '', default: true}],
                phones: [{number: '', default: true}],
                addresses: [],
                applicantMode: 'new',
                nationality: '',
                degree: '',
                existingDocuments: [],
                newDocuments: []
            };
        },
        addRow(type){
            let presets = {
                emails: {address: '', default: !this.form.emails.length},
                phones: {number: '', default: !this.form.phones.length},
                addresses: {city_id: null, city_name: '', line_1: '', line_2: ''},
                newDocuments: {title: ''}
            };
            this.form[type].push(presets[type]);
        },
        removeRow(type, i){
            this.form[type].splice(i, 1);
            // A collection always keeps exactly one default row (FR-002):
            // when the default row is removed, the first row takes over.
            if((type == 'emails' || type == 'phones') && this.form[type].length
                && !this.form[type].some(row => row.default))
                this.form[type][0].default = true;
        },
        markDefault(type, i){
            this.form[type].forEach((row, index) => row.default = index == i);
        },
        /**
         * (Re)initializes the roles multiple-select of a staff form modal
         * as a select2. Called after the modal's form reset, once Vue has
         * re-applied the selection from form.roles.
         */
        initRolesSelect(id){
            let element = $('#' + id);
            if(element.hasClass('select2-hidden-accessible'))
                element.select2('destroy');
            element.select2({
                theme: 'bootstrap',
                placeholder: this.locale.contacts.select_roles,
                width: '100%'
            });
        },
        error(field){
            let form = this.activeForm && this.$refs[this.activeForm];
            return form && form.validation[field] && form.validation[field][0] || null;
        },
        submit(modal){
            this.activeForm = modal + 'Form';
            this.$refs[modal + 'Form'].submit().then(response => {
                this.$refs[modal].hide();
                this.toast = {
                    color: 'success',
                    content: response.message
                };
                this.dataTable.draw();
                this.$nextTick(() => this.$refs.toast.show());
            }).catch(() => {});
        },
        addContact(){
            this.activeForm = 'createContactForm';
            this.emptyForm();
            this.$refs.createContact.show(() => {
                this.$refs.createContactForm.reset();
                this.$nextTick(() => this.initRolesSelect('contact-roles'));
            });
        },
        editContact(e){
            let view = this;
            let row = view.dataTable.row($(e.target).closest('tr')).data();
            view.openContact = row;
            $.get(`${baseUrl}/contacts/${row.id}`).then(response => {
                view.activeForm = 'updateContactForm';
                view.form = {
                    roles: response.roles_ids ?? [],
                    emails: response.emails.map(email => ({address: email.address, default: !!email.default})),
                    phones: response.phones.map(phone => ({number: phone.number, default: !!phone.default})),
                    addresses: response.addresses.map(address => ({city_id: address.city_id, city_name: address.city?.translations?.[0]?.name ?? '',
                        line_1: address.line_1, line_2: address.line_2})),
                    applicantMode: response.applicant? 'existing' : 'new',
                    nationality: response.applicant? response.applicant.country_id ?? '' : '',
                    degree: response.applicant? response.applicant.degree_id ?? '' : '',
                    existingDocuments: (response.applicant? response.applicant.documents : [])
                        .map(document => ({id: document.id, title: document.title, download_url: document.download_url, removed: false})),
                    newDocuments: []
                };
                view.$nextTick(() => view.$refs.updateContact.show(() => {
                    view.$refs.updateContactForm.reset({
                        name: response.name,
                        job: response.job,
                        username: response.username,
                        degree_date: response.applicant? String(response.applicant.degree_date ?? '') : '',
                        recruited_at: response.applicant && response.applicant.recruited_at? response.applicant.recruited_at.slice(0, 10) : '',
                        applicant_id: response.applicant? {id: response.id, text: response.name} : undefined
                    });
                    view.$nextTick(() => view.initRolesSelect('e_contact-roles'));
                }));
            });
        },
        deleteContact(e){
            let view = this;
            let row = view.dataTable.row($(e.target).closest('tr')).data();
            bootbox.confirm(view.locale.contacts.confirm_delete, result => {
                if(!result)
                    return;
                $.post(`${baseUrl}/contacts/delete`, {id: row.id}).then(response => {
                    view.toast = {color: 'success', content: response.message};
                    view.dataTable.draw();
                    view.$nextTick(() => view.$refs.toast.show());
                });
            });
        },
        reactivateContact(e){
            let view = this;
            let row = view.dataTable.row($(e.target).closest('tr')).data();
            bootbox.confirm(view.locale.contacts.confirm_reactivate, result => {
                if(!result)
                    return;
                $.post(`${baseUrl}/contacts/restore`, {id: row.id}).then(response => {
                    view.toast = {color: 'success', content: response.message};
                    view.dataTable.draw();
                    view.$nextTick(() => view.$refs.toast.show());
                });
            });
        },
        renderActions(){
            let view = this, appInstance = this.$appInstance;
            const showProfile = e => {
                view.openContact = view.dataTable.row($(e.target).closest('tr')).data();
                view.$nextTick(() => {
                    view.$refs.profileModal.show(() => {
                        $.get(`${baseUrl}/contacts/${view.openContact.id}`).then(response => {
                            view.openContact = response;
                        });
                    });
                });
            };
            $('[vue-template]').each(function(){
                let data = view.dataTable.row($(this).closest('tr')).data();
                renderVueTemplate(this, appInstance, {methods: {
                    showProfile,
                    editContact: e => view.editContact(e),
                    deleteContact: e => view.deleteContact(e),
                    reactivateContact: e => view.reactivateContact(e)
                }});
                $(this).find('.profile, .edit, .delete').toggle(!(data && data.deleted_at));
                $(this).find('.reactivate').toggle(!!(data && data.deleted_at));
            });
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
    created(){
        this.emptyForm();
    }
}), bundles = [Datatable, Form, Timeline, List, Tab], components = {Modal: 'modal', Autocomplete: 'autocomplete'};

app.config.globalProperties.$appInstance = app;
common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');
