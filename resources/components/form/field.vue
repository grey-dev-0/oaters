<template>
    <template v-if="$parent.$parent.vertical">
        <!-- TODO Supporting vertical form mode -->
    </template>
    <template v-else>
        <div class="form-group row">
            <label :class="labelClass" ref="label">
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
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
let $;
select2();

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
            return 'control-label col-12 col-sm-' + this.$parent.$parent.small + ' col-md-' + this.$parent.$parent.medium + ' col-lg-' +
                this.$parent.$parent.large;
        },
        inputClass: function(){
            return 'control-label col-12 col-sm-' + (12 - this.$parent.$parent.small) + ' col-md-' + (12 - this.$parent.$parent.medium)
                + ' col-lg-' + (12 - this.$parent.$parent.large);
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
            this.$parent.$parent.setField(this.name, this.value);
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
                this.$parent.$parent.emitter.emit('destroy', this.name);
        },
        initDatePicker: function(){

        },
        initSelect: function(){
            var field = this;
            $('#' + this.id).off().select2({
                theme: 'bootstrap',
                placeholder: this.placeholder,
                width: '100%'
            }).on('change', function(){
                field.$parent.$parent.setField(field.name, $(this).val());
            });
        }
    },
    created(){
        $ = this.$root.jQuery();
    },
    mounted: function(){
        this.placeholder = $(this.$refs.label).children().first().text().trim();
        this.$nextTick(function(){
            var field = this;
            this.$parent.$parent.emitter.on('init', function(){
                field.destroy();
                field.construct();
                field.value = field.default;
                field.onChange();
                if(field.inputType == 3)
                    field.$parent.$parent.emitter.emit('init:sub', field.name);
            });
        });
    }
}
</script>