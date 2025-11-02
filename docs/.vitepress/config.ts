import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "OATERS",
  description: "A comprehensive, modular Enterprise Resource Planning system built with Laravel",
  base: "/docs/",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { 
        text: 'Modules', 
        items: [
          { text: 'Lava - Common Modules', link: '/modules/lava-common' },
          { text: 'Onyx - Manufacturing', link: '/modules/onyx' },
          { text: 'Amethyst - E-commerce', link: '/modules/amethyst' },
          { text: 'Topaz - Finance', link: '/modules/topaz' },
          { text: 'Emerald - Projects', link: '/modules/emerald' },
          { text: 'Ruby - Human Resources', link: '/modules/ruby' },
          { text: 'Sapphire - Auth & Tenancy', link: '/modules/sapphire' }
        ]
      },
      {
        text: 'Development',
        items: [
          { text: 'Getting Started', link: '/development/getting-started-frontend' },
          { text: 'Frontend Architecture', link: '/development/frontend-architecture' },
          { text: 'Component Development', link: '/development/component-development' }
        ]
      }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'About OATERS', link: '/' }
        ]
      },
      {
        text: 'Business Modules',
        collapsed: true,
        items: [
          { text: 'Lava - Common Modules', link: '/modules/lava-common' },
          { text: 'Onyx - Manufacturing (Coming Soon)', link: '/modules/onyx' },
          { text: 'Amethyst - E-commerce (Coming Soon)', link: '/modules/amethyst' },
          { text: 'Topaz - Finance (Coming Soon)', link: '/modules/topaz' },
          { text: 'Emerald - Projects (Coming Soon)', link: '/modules/emerald' },
          { text: 'Ruby - Human Resources', link: '/modules/ruby' },
          { text: 'Sapphire - Auth & Tenancy', link: '/modules/sapphire' }
        ]
      },
      {
        text: 'Development Guide',
        collapsed: true,
        items: [
          { text: 'Getting Started', link: '/development/getting-started-frontend' },
          { text: 'Frontend Architecture', link: '/development/frontend-architecture' },
          {
            text: 'Component Development',
            collapsed: true,
            items: [
              { text: 'Overview', link: '/development/component-development' },
              {
                text: 'Simple Components',
                collapsed: true,
                items: [
                  { text: 'Alert', link: '/components/alert' },
                  { text: 'Autocomplete', link: '/components/autocomplete' },
                  { text: 'Avatar', link: '/components/avatar' },
                  { text: 'Card', link: '/components/card' },
                  { text: 'Chart', link: '/components/chart' },
                  { text: 'Counter', link: '/components/counter' },
                  { text: 'Loader', link: '/components/loader' },
                  { text: 'Modal', link: '/components/modal' },
                  { text: 'OrgChart', link: '/components/org-chart' }
                ]
              },
              {
                text: 'Component Bundles',
                collapsed: true,
                items: [
                  { text: 'Navbar', link: '/components/navbar' },
                  { text: 'Breadcrumb', link: '/components/breadcrumb' },
                  { text: 'DataTable', link: '/components/datatable' },
                  { text: 'Table', link: '/components/table' },
                  { text: 'Tab', link: '/components/tab' },
                  { text: 'Timeline', link: '/components/timeline' },
                  { text: 'List', link: '/components/list' },
                  { text: 'Form', link: '/components/form' }
                ]
              }
            ]
          }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/grey-dev-0/oaters' }
    ],

    search: {
      provider: 'local'
    },

    footer: {
      message: 'Built with ❤️ using Laravel and VitePress',
      copyright: 'Version 1.0.0 (Under Development)'
    }
  }
})
