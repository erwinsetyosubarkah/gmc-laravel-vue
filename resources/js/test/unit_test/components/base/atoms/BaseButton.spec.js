import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseButton from '@/components/base/atoms/BaseButton.vue';

describe('BaseButton', () => {
    it('renders a primary button with the default type', () => {
        // Memastikan konfigurasi default menghasilkan tombol yang dapat digunakan.
        const wrapper = mount(BaseButton);

        expect(wrapper.find('button').exists()).toBe(true);
        expect(wrapper.attributes('type')).toBe('button');
        expect(wrapper.classes()).toEqual(['btn', 'btn-primary']);
    });

    it('renders the requested button type', () => {
        // Memastikan atribut type dapat diubah untuk kebutuhan submit atau reset form.
        const wrapper = mount(BaseButton, {
            props: { type: 'submit' },
        });

        expect(wrapper.attributes('type')).toBe('submit');
    });

    it('renders a custom variant class', () => {
        // Memastikan variant biasa mendapat prefix btn- secara otomatis.
        const wrapper = mount(BaseButton, {
            props: { variant: 'secondary' },
        });

        expect(wrapper.classes()).toContain('btn-secondary');
    });

    it('keeps an existing btn- prefix in the variant', () => {
        // Memastikan class variant yang sudah lengkap tidak menjadi btn-btn-*.
        const wrapper = mount(BaseButton, {
            props: { variant: 'btn-danger' },
        });

        expect(wrapper.classes()).toContain('btn-danger');
        expect(wrapper.classes()).not.toContain('btn-btn-danger');
    });

    it('applies the disabled state', () => {
        // Memastikan tombol meneruskan status disabled ke elemen HTML-nya.
        const wrapper = mount(BaseButton, {
            props: { disabled: true },
        });

        expect(wrapper.attributes('disabled')).toBeDefined();
    });

    it('applies a custom class', () => {
        // Memastikan class tambahan dari pemanggil tetap dipertahankan.
        const wrapper = mount(BaseButton, {
            props: { customClass: 'button-wide' },
        });

        expect(wrapper.classes()).toContain('button-wide');
    });

    it('renders slot content', () => {
        // Memastikan label atau konten tombol yang diberikan pemanggil tampil.
        const wrapper = mount(BaseButton, {
            slots: { default: 'Simpan' },
        });

        expect(wrapper.text()).toBe('Simpan');
    });

    it('emits a click event when clicked', async () => {
        // Memastikan interaksi pengguna diteruskan sebagai event click.
        const wrapper = mount(BaseButton);

        await wrapper.trigger('click');

        expect(wrapper.emitted('click')).toBeTruthy();
    });
});
