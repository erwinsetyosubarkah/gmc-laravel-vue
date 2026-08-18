import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseWebSearchInput from '@/components/base/atoms/BaseWebSearchInput.vue';

describe('BaseWebSearchInput', () => {
    it('renders default search controls', () => {
        // Memastikan form pencarian memiliki placeholder, name, dan label default.
        const wrapper = mount(BaseWebSearchInput);
        const input = wrapper.find('input');
        const button = wrapper.find('button');

        expect(input.attributes()).toMatchObject({
            type: 'text',
            placeholder: 'Masukan kata kunci...',
            name: 'search',
        });
        expect(button.text()).toBe('Cari');
        expect(button.attributes('type')).toBe('button');
    });

    it('renders custom search text and value', () => {
        // Memastikan konfigurasi pemanggil diteruskan ke input dan tombol pencarian.
        const wrapper = mount(BaseWebSearchInput, {
            props: {
                modelValue: 'produk',
                placeholder: 'Cari produk',
                name: 'query',
                buttonText: 'Temukan',
            },
        });

        expect(wrapper.find('input').element.value).toBe('produk');
        expect(wrapper.find('input').attributes('placeholder')).toBe('Cari produk');
        expect(wrapper.find('input').attributes('name')).toBe('query');
        expect(wrapper.find('button').text()).toBe('Temukan');
    });

    it('emits model updates and search actions', async () => {
        // Memastikan perubahan kata kunci dan klik tombol diteruskan ke parent.
        const wrapper = mount(BaseWebSearchInput);

        await wrapper.find('input').setValue('klien');
        await wrapper.find('button').trigger('click');

        expect(wrapper.emitted('update:modelValue')).toEqual([['klien']]);
        expect(wrapper.emitted('search')).toHaveLength(1);
    });
});
