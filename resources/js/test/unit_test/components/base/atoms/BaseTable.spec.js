import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import { h } from 'vue';
import BaseTable from '@/components/base/atoms/BaseTable.vue';

describe('BaseTable', () => {
    const columns = [
        { key: 'name', label: 'Nama', className: 'name-column' },
        { key: 'status', label: 'Status' },
    ];

    it('renders column headers and their classes', () => {
        // Memastikan setiap definisi kolom menjadi header tabel yang benar.
        const wrapper = mount(BaseTable, { props: { columns } });

        expect(wrapper.findAll('th').map((header) => header.text())).toEqual(['Nama', 'Status']);
        expect(wrapper.find('th').classes()).toContain('name-column');
    });

    it('renders the empty state with the column span', () => {
        // Memastikan tabel kosong memberi pesan dan merentangkan seluruh kolom.
        const wrapper = mount(BaseTable, { props: { columns, rows: [] } });

        expect(wrapper.find('tbody td').text()).toBe('tidak ada data ditemukan di tabel');
        expect(wrapper.find('tbody td').attributes('colspan')).toBe('2');
    });

    it('renders the rows slot and exposes rows to the slot', () => {
        // Memastikan data tersedia bagi slot rows untuk dirender oleh pemanggil.
        const rows = [{ name: 'GMC', status: 'Aktif' }];
        const wrapper = mount(BaseTable, {
            props: { columns, rows },
            slots: {
                rows: ({ rows: slotRows }) => h('tr', [
                    h('td', slotRows[0].name),
                    h('td', slotRows[0].status),
                ]),
            },
        });

        expect(wrapper.find('tbody').text()).toContain('GMC');
        expect(wrapper.find('tbody').text()).toContain('Aktif');
        expect(wrapper.find('tbody td').text()).not.toContain('tidak ada data');
    });
});
