<template>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" :id="id" :name="name" :value="value" @change="onChange" v-model="checked" :key="id">
        <label :for="id" class="form-check-label"><slot></slot></label>
    </div>
</template>

<script>
export default {
    name: "Radio",
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
            if(this.$parent.$options.name != 'VueField')
                return;
            this.$parent.value = this.value;
            this.$parent.$parent.setField(this.name, this.value);
        }
    },
    mounted: function(){
        if(this.$parent.$options.name != 'VueField')
            return;
        var radio = this;
        this.$nextTick(function(){
            this.$parent.$parent.emitter.on('init:sub', function(fieldName){
                radio.$nextTick(function(){
                    if(radio.name == fieldName)
                        radio.checked = radio.$parent.value;
                });
            });

            this.$parent.$parent.emitter.on('destroy', function(fieldName){
                if(radio.name == fieldName)
                    radio.checked = undefined;
            });
        });
    }
}
</script>