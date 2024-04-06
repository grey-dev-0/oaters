<template>
    <form :id="id" :action="action" method="post" :enctype="encoding">
        <div class="row" v-if="vertical">
            <slot></slot>
        </div>
        <template v-else>
            <slot></slot>
        </template>
    </form>
</template>

<script>
import emitter from 'mitt';
var $ = window.$;

export default {
    name: "VueForm",
    props: {
        id: String,
        action: String,
        vertical: {
            type: Boolean,
            default: false
        },
        ajax: {
            type: Boolean,
            default: true
        },
        small: {
            type: Number,
            default: 6
        },
        medium: {
            type: Number,
            default: 3
        },
        large: {
            type: Number,
            default: 2
        }
    },
    data: function(){
        return {
            fields: {},
            hasFiles: false,
            emitter: null
        };
    },
    computed: {
        encoding: function(){
            return this.hasFiles? 'multipart/form-data' : 'application/x-www-form-urlencoded'
        }
    },
    methods: {
        setField: function(field, value){
            this.fields[field] = value;
        },
        reset: function(){
            $('#' + this.id)[0].reset();
            this.emitter.emit('init');
        }
    },
    mounted: function(){
        this.emitter = emitter();
        if(this.$parent.$options.name != 'Modal')
            this.emitter.emit('init');
    }
}
</script>