import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseModal from '@/components/base/atoms/BaseModal.vue';
import BaseButton from '@/components/base/atoms/BaseButton.vue';

describe('BaseModal', () => {
    it('does not render when show is false', () => {
        // Memastikan modal tersembunyi secara default agar tidak mengganggu halaman.
        const wrapper = mount(BaseModal);

        expect(wrapper.find('[role="dialog"]').exists()).toBe(false);
    });

    it('renders title and slot content when shown', () => {
        // Memastikan modal menampilkan judul serta isi yang diberikan pemanggil.
        const wrapper = mount(BaseModal, {
            props: { show: true, title: 'Tambah data' },
            slots: { default: '<p>Form data</p>' },
        });

        expect(wrapper.find('[role="dialog"]').exists()).toBe(true);
        expect(wrapper.find('.modal-title').text()).toBe('Tambah data');
        expect(wrapper.find('.modal-body').text()).toBe('Form data');
    });

    it('emits close when the close button is clicked', async () => {
        // Memastikan tombol close meneruskan permintaan penutupan kepada parent.
        const wrapper = mount(BaseModal, { props: { show: true } });

        await wrapper.findComponent(BaseButton).vm.$emit('click');

        expect(wrapper.emitted('close')).toHaveLength(1);
    });
});
