<template>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" :id="id" :name="name" :value="value" @change="onChange">
        <label :for="id" class="form-check-label"><slot></slot></label>
    </div>
</template>

<script>
export default {
    name: "Checkbox",
    inject: ['setValue', 'setField'],
    props: {
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        value: [String, Boolean]
    },
    methods: {
        onChange: function(){
            this.setValue(this.value);
            this.setField(this.name, this.value);
        }
    }
}
</script>