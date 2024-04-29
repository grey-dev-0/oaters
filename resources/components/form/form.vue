<template>
    <form :id="id" :action="action" method="post" :enctype="encoding">
        <v-loader v-if="loading"></v-loader>
        <div :class="loading? 'invisible' : ''">
            <div class="row" v-if="vertical">
                <slot></slot>
            </div>
            <template v-else>
                <slot></slot>
            </template>
        </div>
    </form>
</template>

<script>
import {computed} from 'vue';
import emitter from 'mitt';
let $;

export default {
    name: "VueForm",
    props: {
        id: {
            type: String,
            required: true
        },
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
            loading: false,
            fields: {},
            validation: {},
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
            validation: computed(() => this.validation),
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
        reset: function(defaults){
            this.validation = {};
            $('#' + this.id)[0].reset();
            this.emitter.emit('init', {defaults});
            this.loading = false;
        },
        submit(){
            let form = $('#' + this.id);
            if(!this.ajax)
                form.submit();
            else{
                let request = {
                    url: this.action || window.location.href,
                    type: 'POST',
                    error: (xhr) => {
                        if(xhr.status != 422){
                            this.$root.bootbox().alert('Something went wrong!');
                            console.error(xhr.responseText);
                        } else{
                            let errors = xhr.responseJSON.errors, validation = {}, splitKey, error;
                            for(let key in errors){
                                splitKey = key.split('.');
                                error = errors[key];
                                if(splitKey.length > 1)
                                    splitKey.forEach((part, index) => {
                                        if(index == 0)
                                            key = part;
                                        else
                                            key += `[${part}]`;
                                    });
                                validation[key] = error;
                            }
                            this.validation = validation;
                        }
                    }
                };
                request = Object.assign(request, this.encoding != 'multipart/form-data'? {
                    data: form.serialize()
                } : {
                    contentType: false,
                    processData: false,
                    data: new FormData(form[0])
                });
                return $.ajax(request);
            }
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