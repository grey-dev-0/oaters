<template>
<th><slot></slot></th>
</template>

<script>
export default {
    name: "Column",
    props: {
        data: String,
        last: Boolean
    }
}
</script>