import {createApp} from "vue";
import common, {jQuery as $, renderVueTemplate} from "../common.js";
import Datatable from "../../components/datatable";
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';

let app = createApp({
    data: function(){
        return {
            emitter: null,
            openUser: {
                id: 0,
                name: null
            }
        };
    },
    methods: {
        renderAmount: function(amount){
            return '$' + amount;
        },
        renderExecuted: function(executed){
            return (executed == 1)? locale.common.yes : locale.common.no;
        },
        renderPaidAt: function(row){
            return (row['executed'] == 1)? row['updated_at'] : locale.common.unpaid;
        },
        renderActions(){
            let view = this, appInstance = this.$appInstance;
            const openPayments = e => {
                view.paymentsModal.show(function(){
                    let tenant = view.dataTable.row($(e.target).closest('tr')).data();
                    view.openUser = { id: tenant['id'], name: tenant['name'] };
                    view.$nextTick(function(){ view.paymentsTable.init(); });
                });
            };
            const toggleModules = e => {
                let button = $(e.currentTarget);
                if(button.hasClass('disabled')) return;
                let row = button.closest('tr');
                let dtRow = view.dataTable.row(row);
                if(dtRow.child.isShown()) dtRow.child.hide();
                else{
                    let subscriptionId = button.attr('data-id');
                    let child = $('<tr/>').append('<td class="bg-sky-10"><strong>'+locale.common.modules
                        +'</strong></td><td class="bg-sky-10" colspan="' + (row.find('td').length - 1) + '" data-subscription-id="'
                        +subscriptionId+'"><i class="fas fa-spin fa-spinner"></i></td>');
                    dtRow.child(child).show();

                    $.ajax({
                        url: window.baseUrl + '/subscriptions/' + subscriptionId + '/modules',
                        type: 'GET',
                        success: function(response){
                            let cell = $('[data-subscription-id="'+subscriptionId+'"]'), color;
                            cell.empty();
                            response.modules.forEach(function(module){
                                color = view.moduleColor(module);
                                cell.append('<div class="badge badge-'+color+'">'+module+'</div> ');
                            });
                        }
                    });
                }
            };
            const extendTenant = e => { /* implement if needed */ };
            const revokeTenant = e => { /* implement if needed */ };
            $('[vue-template]').each(function(){
                renderVueTemplate(this, appInstance, {methods: {openPayments, toggleModules, extendTenant, revokeTenant}});
            });
        },
        moduleColor: function(module){
            switch(module){
                case 'amethyst': return 'info';
                case 'topaz': return 'warning';
                case 'emerald': return 'success';
                case 'ruby': return 'danger';
                case 'sapphire': return 'primary';
                default: return 'secondary';
            }
        }
    },
    computed: {
        dataTable: function(){
            return this.$refs.tenantsTable.dataTable;
        },
        paymentsModal: function(){
            return this.$refs.paymentsModal;
        },
        paymentsTable: function(){
            return this.$refs.paymentsTable;
        }
    }
}), bundles = [Datatable], components = {Modal: 'modal'};

app.config.globalProperties.$appInstance = app;
common.load(app);
common.loadBundles(app, bundles);
common.loadComponents(app, components);
app.mount('#app');
