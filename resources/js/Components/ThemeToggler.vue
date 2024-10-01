<template>
    <div class="dropdown nav-link">
        <span role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fs-5">
                <i class="fas fa-adjust"></i>
            </i>
            <span class="fs-5 fw-lighter d-none d-md-inline ms-md-2 mt-1 mt-md-0">Modo</span>
        </span>
        <ul class="dropdown-menu my-2">
            <li v-for="theme in themes" :key="theme">
                <a class="dropdown-item" :data-bs-theme-value="theme" @click="changeTheme(theme)" v-html="translateTheme(theme)"></a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            themes: ['light', 'dark', 'auto'],
            currentTheme: '',
            activeIconHref: ''
        }
    },
    mounted() {
        this.currentTheme = this.getPreferredTheme();
        this.setTheme(this.currentTheme);
        this.showActiveTheme(this.currentTheme);
        
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', this.updateThemeOnPrefChange);
    },
    beforeDestroy() {
        window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', this.updateThemeOnPrefChange);
    },
    methods: {
        getStoredTheme() {
            return localStorage.getItem('theme');
        },
        setStoredTheme(theme) {
            localStorage.setItem('theme', theme);
        },
        getPreferredTheme() {
            const storedTheme = this.getStoredTheme();
            if (storedTheme) {
                return storedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },
        setTheme(theme) {
            if (theme === 'auto') {
                document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
        },
        showActiveTheme(theme, focus = false) {
            const themeSwitcher = document.querySelector('#bd-theme');
            if (!themeSwitcher) return;
            
            const themeSwitcherText = document.querySelector('#bd-theme-text');
            const activeThemeIcon = document.querySelector('.theme-icon-active use');
            const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
            const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href');
            
            document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                element.classList.remove('active');
                element.setAttribute('aria-pressed', 'false');
            });
            
            btnToActive.classList.add('active');
            btnToActive.setAttribute('aria-pressed', 'true');
            activeThemeIcon.setAttribute('href', svgOfActiveBtn);
            const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`;
            themeSwitcher.setAttribute('aria-label', themeSwitcherLabel);
            
            if (focus) {
                themeSwitcher.focus();
            }
        },
        changeTheme(theme) {
            this.setStoredTheme(theme);
            this.setTheme(theme);
            this.showActiveTheme(theme, true);
        },
        updateThemeOnPrefChange() {
            const storedTheme = this.getStoredTheme();
            if (storedTheme !== 'light' && storedTheme !== 'dark') {
                const preferredTheme = this.getPreferredTheme();
                this.setTheme(preferredTheme);
                this.showActiveTheme(preferredTheme);
            }
        },
        translateTheme(theme) {
            const translations = {
                light: '<i class="fas fa-sun"></i>&nbsp;Claro',
                dark: '<i class="fas fa-moon"></i>&nbsp;Oscuro',
                auto: '<i class="fas fa-magic"></i>&nbsp;Automático'
            };
            return translations[theme] || theme;
        }
    }
}
</script>

<style>
/* Agrega estilos aquí si es necesario */
</style>
