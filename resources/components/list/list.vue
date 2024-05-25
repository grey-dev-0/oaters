<template>
    <ul :id="id" :class="style">
        <slot></slot>
    </ul>
</template>

<script>
export default {
    name: 'List',
    props: {
        id: String,
        collapse: Boolean
    },
    computed: {
        style(){
            let theClass ='list-group';
            if(this.collapse)
                theClass += ' collapse list-group-flush';
            return theClass;
        }
    }
};
</script>