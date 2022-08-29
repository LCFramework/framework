import { defineConfig } from 'vitepress';
import { version } from '../../composer.json';

const nav = [
    {
        text: 'Documentation',
        link: '/docs/introduction/what-is-lcframework',
        activeMatch: '/docs/'
    },
    {
        text: `Version: ${ version }`,
        items: [
            {
                text: 'Changelog',
                link: 'https://github.com/lcframework/framework'
            },
            {
                text: 'Contributing',
                link: 'https://github.com/lcframework/framework'
            }
        ]
    }
];

const sidebarGuide = [
    {
        text: 'Introduction',
        collapsible: true,
        items: [
            { text: 'What is LCFramework?', link: '/docs/introduction/what-is-lcframework' },
            { text: 'Getting Started', link: '/docs/introduction/getting-started' },
            { text: 'Configuration', link: '/docs/introduction/configuration' }
        ]
    },
    {
        text: 'Modules',
        collapsible: true,
        items: [
            { text: 'Getting Started', link: '/docs/modules/getting-started' },
            { text: 'Building Modules', link: '/docs/modules/building-modules' },
            { text: 'Transforming Data', link: '/docs/modules/transforming-data' },
            { text: 'Persistent Data', link: '/docs/modules/persistent-data' }
        ]
    },
    {
        text: 'Themes',
        collapsible: true,
        items: [
            { text: 'Getting Started', link: '#' },
            { text: 'Building Themes', link: '' }
        ]
    },
    {
        text: 'Authentication',
        collapsible: true,
        items: [
            { text: 'Getting Started', link: '#' },
            { text: 'Login', link: '#' },
            { text: 'Registration', link: '#' }
        ]
    },
    {
        text: 'Administration',
        collapsible: true,
        items: [
            { text: 'Getting Started', link: '#' },
            { text: 'Roles & Permissions', link: '' },
            { text: 'Building Forms', link: '' }
        ]
    }
];

export default defineConfig({
    base: '/framework/',

    lang: 'en-US',
    title: 'LCFramework',
    description: 'A framework for LastChaos private servers.',

    lastUpdated: true,
    cleanUrls: 'without-subfolders',

    themeConfig: {
        nav,

        sidebar: sidebarGuide,

        footer: {
            message: 'Released under the MIT license.'
        },

        editLink: {
            pattern: 'https://github.com/lcframework/framework/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        },

        socialLinks: [
            { icon: 'github', link: 'https://github.com/lcframework/' }
        ]
    }
});
