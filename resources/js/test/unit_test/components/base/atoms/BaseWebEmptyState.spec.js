import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseWebEmptyState from '@/components/base/atoms/BaseWebEmptyState.vue';

describe('BaseWebEmptyState', () => {
    it('renders the default empty-state message', () => {
        // Memastikan pengguna mendapat pesan informatif ketika data kosong.
        const wrapper = mount(BaseWebEmptyState);

        expect(wrapper.text()).toBe('Data tidak ditemukan.');
        expect(wrapper.find('.text-center').exists()).toBe(true);
    });

    it('renders a custom message with the expected layout classes', () => {
        // Memastikan pesan khusus tetap berada di layout grid dan rata tengah.
        const wrapper = mount(BaseWebEmptyState, { props: { message: 'Belum ada artikel.' } });

        expect(wrapper.text()).toBe('Belum ada artikel.');
        expect(wrapper.find('.col-lg-12').classes()).toEqual([
            'col-lg-12',
            'col-md-12',
            'col-sm-12',
            'text-center',
            'p-3',
        ]);
    });
});
