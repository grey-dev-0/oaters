<template>
    <nav :class="'navbar navbar-expand-md navbar-'+scheme+' bg-'+bgColor">
        <a class="navbar-brand" :href="home">{{brand}}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsible-navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsible-navbar">
            <ul class="navbar-nav">
                <slot></slot>
            </ul>
            <ul class="navbar-nav ml-auto">
                <slot name="right"></slot>
            </ul>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar',
    props: {
        home: String,
        brand: String,
        scheme: {
            type: String,
            default: 'light'
        },
        bgColor: {
            type: String,
            default: 'light'
        }
    }
};
</script>
