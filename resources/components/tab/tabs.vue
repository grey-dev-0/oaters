<template>
    <div>
        <ul class="nav nav-tabs mb-3" role="tablist">
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
    provide(){
        return {
            activeTab: computed(() => this.activeTab)
        };
    }
};
</script>