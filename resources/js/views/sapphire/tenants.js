(function(){
    var app = Vue.createApp({
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
    loadComponents(app, 'tenants');
    var view = app.mount('#app');

    $('body').on('click', '#tenants-table .payments', function (){
        var button = $(this);
        view.paymentsModal.show(function(){
            var tenant = view.dataTable.row(button.closest('tr')).data();
            view.openUser = {
                id: tenant['id'],
                name: tenant['name']
            };
            view.$nextTick(function(){
                view.paymentsTable.init();
            });
        });
    });
})();