import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseSelect from '@/components/base/atoms/BaseSelect.vue';

describe('BaseSelect', () => {
    it('renders the placeholder and primitive options', () => {
        // Memastikan select menampilkan placeholder serta opsi string/angka.
        const wrapper = mount(BaseSelect, {
            props: { id: 'status', placeholder: 'Pilih status', options: ['Aktif', 'Arsip'] },
        });

        expect(wrapper.find('select').attributes('id')).toBe('status');
        expect(wrapper.find('option[disabled]').text()).toBe('Pilih status');
        expect(wrapper.findAll('option')).toHaveLength(3);
        expect(wrapper.findAll('option')[1].text()).toBe('Aktif');
    });

    it('renders object options using custom keys and errors', () => {
        // Memastikan opsi object memakai value/label key yang dikonfigurasi.
        const wrapper = mount(BaseSelect, {
            props: {
                options: [{ code: 1, name: 'Satu' }],
                valueKey: 'code',
                labelKey: 'name',
                inputClass: 'select-lg',
                error: 'Pilihan wajib diisi',
            },
        });

        expect(wrapper.findAll('option')[1].attributes('value')).toBe('1');
        expect(wrapper.findAll('option')[1].text()).toBe('Satu');
        expect(wrapper.find('select').classes()).toContain('select-lg');
        expect(wrapper.find('.invalid-feedback').text()).toBe('Pilihan wajib diisi');
    });

    it('emits the selected value', async () => {
        // Memastikan pilihan pengguna diteruskan melalui event update:modelValue.
        const wrapper = mount(BaseSelect, { props: { options: ['Aktif', 'Arsip'] } });

        await wrapper.find('select').setValue('Arsip');

        expect(wrapper.emitted('update:modelValue')).toEqual([['Arsip']]);
    });
});
