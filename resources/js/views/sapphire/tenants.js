(function(){
    var view = Vue.createApp({
        methods: {
            renderActions: function(){
                return '<small class="text-muted">to be specified</small>';
            }
        }
    });
    loadComponents(view, 'tenants');
    view.mount('#app');
})();