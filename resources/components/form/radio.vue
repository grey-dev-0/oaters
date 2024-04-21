<template>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" :id="id" :name="name" :value="value" @change="onChange" v-model="checked" :key="id">
        <label :for="id" class="form-check-label"><slot></slot></label>
    </div>
</template>

<script>
export default {
    name: "Radio",
    inject: ['setValue', 'setField', 'emitter'],
    props: {
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        value: String
    },
    data: function(){
        return {
            checked: undefined
        };
    },
    methods: {
        onChange: function(){
            if(this.$parent.$parent.$options.name != 'VueField')
                return;
            this.setValue(this.value);
        }
    },
    mounted: function(){
        if(this.$parent.$parent.$options.name != 'VueField')
            return;
        this.$nextTick(function(){
            this.emitter.on('init:sub', fieldName => {
                this.$nextTick(function(){
                    if(this.name == fieldName)
                        this.checked = this.$parent.$parent.value;
                });
            });

            this.emitter.on('destroy', fieldName => {
                if(this.name == fieldName)
                    this.checked = undefined;
            });
        });
    }
}
</script>