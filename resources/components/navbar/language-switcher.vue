<template>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
           :aria-label="label" :title="label">
            <i class="fas fa-globe"></i>
            <span class="d-none d-md-inline">{{ active }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a v-for="option in options" :key="option.code" href="#" class="dropdown-item"
               :class="{'font-weight-bolder text-primary': option.code === current}"
               @click.prevent="switchTo(option.code)">
                {{ option.name }}
                <i v-if="option.code === current" class="fas fa-check small"></i>
            </a>
        </div>
    </li>
</template>

<script>
export default {
    name: 'LanguageSwitcher',
    data: function(){
        return {
            current: (window.uiLocale && window.uiLocale.lang) || 'en',
            label: (window.uiLocale && window.uiLocale.label) || 'Language',
            switchUrl: window.uiLocale? window.uiLocale.switchUrl : undefined,
            options: [
                {code: 'en', name: 'English'},
                {code: 'ar', name: 'العربية'}
            ]
        };
    },
    computed: {
        active: function(){
            let option = this.options.find(option => option.code === this.current);

            return option? option.name : '';
        }
    },
    methods: {
        switchTo: function(code){
            if(code === this.current || this.switchUrl === undefined)
                return;
            let token = document.querySelector('meta[name="csrf-token"]');
            fetch(this.switchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token? token.getAttribute('content') : ''
                },
                body: JSON.stringify({locale: code})
            }).finally(() => window.location.reload());
        }
    }
};
</script>
