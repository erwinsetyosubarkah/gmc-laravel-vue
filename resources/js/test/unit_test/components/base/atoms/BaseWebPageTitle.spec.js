import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseWebPageTitle from '@/components/base/atoms/BaseWebPageTitle.vue';

describe('BaseWebPageTitle', () => {
    it('renders the title and divider', () => {
        // Memastikan judul halaman dan pemisah visual selalu dirender.
        const wrapper = mount(BaseWebPageTitle, { props: { title: 'Tentang Kami' } });

        expect(wrapper.find('h2').text()).toBe('Tentang Kami');
        expect(wrapper.find('h2').classes()).toContain('text-md');
        expect(wrapper.find('.divider').exists()).toBe(true);
    });

    it('renders an empty title by default', () => {
        // Memastikan komponen aman digunakan ketika judul belum tersedia.
        const wrapper = mount(BaseWebPageTitle);

        expect(wrapper.find('h2').text()).toBe('');
    });
});
