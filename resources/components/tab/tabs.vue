<template>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <slot name="navigation"></slot>
        </ul>
        <div class="tab-content">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import {computed} from 'vue';

export default {
    name: "Tabs",
    props: {
        activeTab: String
    },
    provides: {
        activeTab: computed(() => this.activeTab)
    }
};
</script>