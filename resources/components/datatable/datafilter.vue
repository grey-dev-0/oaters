<template>
    <div class="list-group list-group-flush">
        <div :class="'list-group-item pb-0 bg-' + color">
            <div class="row">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import emitter from 'mitt';

export default {
    name: 'VueDatafilter',
    data: function(){
        return {
            filters: {},
            defaults: {}
        };
    },
    props: {
        cols: {
            type: Number,
            default: 5
        },
        datatableRef: String,
        color: {
            type: String,
            default: 'grey-10'
        }
    },
    computed: {
        count: function(){
            return Object.keys(this.filters).length;
        },
        dataTable: function(){
            return (this.datatableRef !== undefined)?
                this.$root.$refs[this.datatableRef].dataTable : this.$root.dataTable;
        }
    },
    mounted: function(){
        if(!this.$root.emitter)
            this.$root.emitter = emitter();
        this.$root.emitter.on('initialized', (e) => {
            if(e.ref == this.datatableRef)
                this.setDefaults();
        });
    },
    methods: {
        filter: function(field, value){
            this.filters[field] = value;
            this.dataTable.column(field + ':name').search(value).draw();
        },
        clear: function(){
            this.$root.emitter.emit('clear', {ref: this.datatableRef});
            if(Object.keys(this.defaults).length){
                this.dataTable.columns().search('');
                this.setDefaults();
            } else
                this.dataTable.columns().search('').draw();
        },
        setDefaults: function(){
            for(var column in this.defaults)
                this.dataTable.column(column + ':name').search(this.defaults[column]);
            this.dataTable.draw();
        }
    }
}
</script>