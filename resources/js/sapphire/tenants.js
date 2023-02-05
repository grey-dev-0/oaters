import {createApp} from "vue";
import Modal from "../../components/modal.vue";
import Datatable from "../../components/datatable";
let libraries = Object.assign({}, Datatable, {Modal});

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
        onDatatableDraw: function(){
            $('#tenants-table').find('.modules').each(function(){
                if($(this).attr('data-id') == '')
                    $(this).addClass('disabled');
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
});

import(
    /* webpackPreload: true */
    /* webpackChunkName: "js/common" */
    '../../components/common'
    ).then((common) => {
    common.load(app);
    for(let component in libraries)
        app.component(component, libraries[component]);
    app.mount('#app');
});

$('body').on('click', '#tenants-table .payments', function (){
    let button = $(this);
    view.paymentsModal.show(function(){
        let tenant = view.dataTable.row(button.closest('tr')).data();
        view.openUser = {
            id: tenant['id'],
            name: tenant['name']
        };
        view.$nextTick(function(){
            view.paymentsTable.init();
        });
    });
}).on('click', '#tenants-table .modules', function(){
    if($(this).hasClass('disabled'))
        return;
    let row = $(this).closest('tr');
    let dtRow = view.dataTable.row(row);
    if(dtRow.child.isShown())
        dtRow.child.hide();
    else{
        let subscriptionId = $(this).attr('data-id');
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
});