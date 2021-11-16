<template>
    <template v-if="$parent.vertical">
        <!-- TODO Supporting vertical form mode -->
    </template>
    <template v-else>
        <div class="form-group row">
            <label :class="labelClass">
                <slot></slot>
            </label>
            <div :class="inputClass">
                <input v-if="inputType == 1" :id="id" :class="(type != 'file')? 'form-control' : ''" :name="name" :type="textType" v-model="value" :autocomplete="!autocomplete? 'off' : 'on'" :placeholder="placeholder" @change="onChange">
                <select v-else-if="inputType == 2" :id="id" class="form-control" :name="name" v-model="value" :multiple="multiple" @change="onChange">
                    <option value="">-- {{placeholder}} --</option>
                    <slot name="options"></slot>
                </select>
                <template v-else-if="inputType == 3">
                    <slot name="options"></slot>
                </template>
                <autocomplete v-else :name="name" :id="id" :url="url" :placeholder="placeholder" @change="onAutocompleteChange"></autocomplete>
            </div>
        </div>
    </template>
</template>

<script>
import Autocomplete from "../autocomplete.vue";
var $ = window.$;

export default {
    name: "VueField",
    components:{
        Autocomplete
    },
    props: {
        id: String,
        name: String,
        autocomplete: {
            type: Boolean,
            default: false
        },
        type: {
            default: 'text',
            validator: function(value){
                var supportedTypes = ['text', 'number', 'password', 'email', 'tel', 'daterange', 'file', 'select', 'select2', 'checkbox', 'radio', 'autocomplete'];
                return supportedTypes.indexOf(value) != -1;
            }
        },
        multiple: {
            type: Boolean,
            default: false
        },
        url: String,
        default: {
            type: [String, Number, Boolean],
            default: undefined
        }
    },
    data: function(){
        return {
            value: null,
            placeholder: ''
        };
    },
    computed: {
        labelClass: function(){
            return 'control-label col-12 col-sm-' + this.$parent.small + ' col-md-' + this.$parent.medium + ' col-lg-' +
                this.$parent.large;
        },
        inputClass: function(){
            return 'control-label col-12 col-sm-' + (12 - this.$parent.small) + ' col-md-' + (12 - this.$parent.medium)
                + ' col-lg-' + (12 - this.$parent.large);
        },
        inputType: function(){
            switch(this.type){
                case 'text':
                case 'number':
                case 'password':
                case 'email':
                case 'tel':
                case 'file':
                case 'daterange':
                    return 1;
                case 'select':
                case 'select2':
                    return 2;
                case 'radio':
                case 'checkbox':
                    return 3;
                default:
                    return 4;
            }
        },
        textType: function(){
            return (this.type == 'daterange')? 'text' : this.type;
        }
    },
    methods: {
        onChange: function(){
            this.$parent.setField(this.name, this.value);
        },
        onAutocompleteChange: function(name, value){
            this.value = value;
            this.$nextTick(this.onChange);
        },
        construct: function(){
            switch(this.type){
                case 'daterange': return this.initDatePicker();
                case 'select2': return this.initSelect();
            }
        },
        destroy: function(){
            var element = $('#' + this.id);
            if(this.type == 'select2' && element.hasClass('select2-hidden-accessible'))
                element.select2('destroy');
            if([3, 4].indexOf(this.inputType) != -1 && this.name !== undefined)
                this.$parent.emitter.emit('destroy', this.name);
        },
        initDatePicker: function(){

        },
        initSelect: function(){
            var field = this;
            $('#' + this.id).off().select2({
                placeholder: this.placeholder,
                width: '100%'
            }).on('change', function(){
                field.$parent.setField(field.name, $(this).val());
            });
        }
    },
    mounted: function(){
        this.placeholder = this.$slots.default()[0].el.textContent.trim();
        this.$nextTick(function(){
            var field = this;
            this.$parent.emitter.on('init', function(){
                field.destroy();
                field.construct();
                field.value = field.default;
                field.onChange();
                if(field.inputType == 3)
                    field.$parent.emitter.emit('init:sub', field.name);
            });
        });
    }
}
</script>