import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "OATERS",
  description: "A comprehensive, modular Enterprise Resource Planning system built with Laravel",
  base: "/oaters/",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { 
        text: 'Modules', 
        items: [
          { text: 'Sapphire - Auth & Tenancy', link: '/modules/sapphire' },
          { text: 'Ruby - Human Resources', link: '/modules/ruby' },
          { text: 'Onyx - Manufacturing', link: '/modules/onyx' },
          { text: 'Amethyst - E-commerce', link: '/modules/amethyst' },
          { text: 'Topaz - Finance', link: '/modules/topaz' },
          { text: 'Emerald - Projects', link: '/modules/emerald' }
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
        text: 'Core Modules',
        collapsed: false,
        items: [
          { text: 'Sapphire - Auth & Tenancy', link: '/modules/sapphire' },
          { text: 'Ruby - Human Resources', link: '/modules/ruby' }
        ]
      },
      {
        text: 'Business Modules',
        collapsed: true,
        items: [
          { text: 'Onyx - Manufacturing (Coming Soon)', link: '/modules/onyx' },
          { text: 'Amethyst - E-commerce (Coming Soon)', link: '/modules/amethyst' },
          { text: 'Topaz - Finance (Coming Soon)', link: '/modules/topaz' },
          { text: 'Emerald - Projects (Coming Soon)', link: '/modules/emerald' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/yourusername/oaters' }
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
