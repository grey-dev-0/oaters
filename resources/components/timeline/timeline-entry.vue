<template>
    <li><slot></slot></li>
</template>

<script>
export default {
    name: 'TimelineEntry',
    props: {
        color: {
            type: String,
            default: 'primary'
        }
    },
    computed: {
        colorVariable(){
            return `var(--${this.color})`;
        }
    }
};
</script>

<style>
ul.timeline > li:before{
    border-color: v-bind(colorVariable);
}
</style>