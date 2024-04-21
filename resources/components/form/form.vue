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
import {computed} from 'vue';
import emitter from 'mitt';
let $;

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
    provide: function(){
        return {
            labelSize: computed(() => ({
                small: this.small,
                medium: this.medium,
                large: this.large
            })),
            formOrientation: computed(() => ({
                vertical: this.vertical
            })),
            emitter: computed(() => this.emitter),
            setField: this.setField
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
    created(){
        $ = this.$root.jQuery();
    },
    mounted(){
        this.emitter = emitter();
        if(this.$parent.$parent.$options.name != 'Modal')
            this.emitter.emit('init');
    }
}
</script>