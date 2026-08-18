import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseTextarea from '@/components/base/atoms/BaseTextarea.vue';

describe('BaseTextarea', () => {
    it('renders the default textarea configuration', () => {
        // Memastikan textarea memakai class form dan jumlah baris default.
        const wrapper = mount(BaseTextarea);

        expect(wrapper.find('textarea').classes()).toContain('form-control');
        expect(wrapper.find('textarea').attributes('rows')).toBe('3');
    });

    it('renders configured attributes and validation error', () => {
        // Memastikan konfigurasi tampilan dan batas karakter diterapkan.
        const wrapper = mount(BaseTextarea, {
            props: {
                id: 'description',
                rows: 5,
                maxlength: 100,
                placeholder: 'Deskripsi',
                textareaClass: 'textarea-lg',
                error: 'Deskripsi wajib diisi',
            },
        });

        expect(wrapper.find('textarea').attributes()).toMatchObject({
            id: 'description',
            rows: '5',
            maxlength: '100',
            placeholder: 'Deskripsi',
        });
        expect(wrapper.find('textarea').classes()).toContain('textarea-lg');
        expect(wrapper.find('.invalid-feedback').text()).toBe('Deskripsi wajib diisi');
    });

    it('emits the entered value and exposes the textarea ref', async () => {
        // Memastikan v-model menerima nilai baru dan ref internal tersedia.
        const wrapper = mount(BaseTextarea);

        await wrapper.find('textarea').setValue('Isi baru');

        expect(wrapper.emitted('update:modelValue')).toEqual([['Isi baru']]);
        expect(wrapper.vm.textareaRef).toBe(wrapper.find('textarea').element);
    });
});
