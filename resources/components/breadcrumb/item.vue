<template>
    <li :class="'breadcrumb-item' + (active? ' active' : '')">
        <a v-if="url !== undefined" :href="url"><slot></slot></a>
        <template v-else><slot></slot></template>
    </li>
</template>

<script>
export default {
    name: "BcItem",
    props: {
        url: String,
        active: {
            type: Boolean,
            default: false
        }
    }
}
</script>