<template>
    <ul class="timeline m-0">
        <slot></slot>
    </ul>
</template>

<script>
export default {
    name: 'Timeline'
};
</script>

<style lang="scss">
ul.timeline{
    list-style-type: none;
    position: relative;

    &:before{
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    & > li:first-child{
        margin-top: 0;
    }

    & > li{
        margin: 20px 0;
        padding-left: 20px;
    }

    & > li:last-child{
        margin-bottom: 0;
    }

    & > li:before{
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
    }
}
</style>