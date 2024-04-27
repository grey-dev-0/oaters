<template>
    <div v-if="shown" :id="id" :class="'alert alert-'+color+' mb-2 fade'" role="alert">
        <div v-if="!$slots.default" v-html="content"></div>
        <slot v-else></slot>
        <span role="button" class="close" @click="hide">&times;</span>
    </div>
</template>

<script>
let $;

export default {
    name: "Alert",
    props: {
        id: {
            type: String,
            required: true
        },
        color: {
            type: String,
            required: true
        },
        content: String
    },
    data(){
        return {
            shown: false
        };
    },
    methods: {
        show(){
            this.shown = true;
            setTimeout(() => {
                $('#' + this.id).addClass('show');
            }, 25);
        },
        hide(){
            $('#' + this.id).removeClass('show');
            setTimeout(() => {
                this.shown = false;
            }, 250);
        }
    },
    created(){
        $ = this.$root.jQuery();
    }
};
</script>

<style lang="scss">
.alert{
    .close{
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        padding: .75rem 1.25rem;
        color: inherit;
    }
}
</style>