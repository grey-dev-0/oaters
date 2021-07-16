(function(){
    var app = Vue.createApp({
        data: function(){
            return {
                emitter: null,
                inspectedUser: null
            };
        },
        methods: {
            renderActions: function(){
                return '<small class="text-muted">to be specified</small>';
            }
        },
        computed: {
            dataTable: function(){
                return this.$refs.tenantsTable.dataTable;
            },
            paymentsModal: function(){
                return this.$refs.paymentsModal;
            }
        }
    });
    loadComponents(app, 'tenants');
    var view = app.mount('#app');

    $('body').on('click', '#tenants-table .payments', function (){
        view.inspectedUser = view.dataTable.row($(this).closest('tr')).data()['name'];
        view.paymentsModal.show();
    });
})();