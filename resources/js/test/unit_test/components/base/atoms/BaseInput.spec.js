import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseInput from '@/components/base/atoms/BaseInput.vue';

describe('BaseInput', () => {
    it('renders the default input configuration', () => {
        // Memastikan input memakai tipe text dan class Bootstrap secara default.
        const wrapper = mount(BaseInput);

        expect(wrapper.find('input').attributes()).toMatchObject({
            type: 'text',
        });
        expect(wrapper.find('input').classes()).toContain('form-control');
    });

    it('renders configured attributes and validation error', () => {
        // Memastikan atribut input, class tambahan, dan pesan error diteruskan.
        const wrapper = mount(BaseInput, {
            props: {
                id: 'email',
                type: 'email',
                placeholder: 'Email',
                inputClass: 'input-lg',
                error: 'Email wajib diisi',
            },
        });

        expect(wrapper.find('input').attributes()).toMatchObject({
            id: 'email',
            type: 'email',
            placeholder: 'Email',
        });
        expect(wrapper.find('input').classes()).toContain('input-lg');
        expect(wrapper.find('.invalid-feedback').text()).toBe('Email wajib diisi');
    });

    it('emits model and input events with the entered value', async () => {
        // Memastikan perubahan nilai mendukung v-model dan meneruskan event input.
        const wrapper = mount(BaseInput, {
            props: { modelValue: 'lama' },
        });
        const input = wrapper.find('input');

        await input.setValue('baru');

        expect(wrapper.emitted('update:modelValue')).toEqual([['baru']]);
        expect(wrapper.emitted('input')).toHaveLength(1);
        expect(wrapper.emitted('input')[0][0]).toBeInstanceOf(Event);
    });
});
