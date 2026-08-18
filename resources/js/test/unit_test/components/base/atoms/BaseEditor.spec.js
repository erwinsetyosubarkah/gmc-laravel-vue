import { describe, expect, it, vi } from 'vitest';

import { flushPromises, mount } from '@vue/test-utils';
import BaseEditor from '@/components/base/atoms/BaseEditor.vue';

describe('BaseEditor', () => {
    it('renders its textarea, label, and validation error', () => {
        // Memastikan struktur editor tetap informatif sebelum library editor dimuat.
        const wrapper = mount(BaseEditor, {
            props: {
                id: 'content',
                label: 'Konten',
                placeholder: 'Tulis konten',
                error: 'Konten wajib diisi',
            },
        });

        expect(wrapper.find('label').attributes('for')).toBe('content');
        expect(wrapper.find('label').text()).toBe('Konten');
        expect(wrapper.find('textarea').attributes('placeholder')).toBe('Tulis konten');
        expect(wrapper.find('.invalid-feedback').text()).toBe('Konten wajib diisi');
    });

    it('initializes and destroys ClassicEditor while syncing changes', async () => {
        // Memastikan integrasi editor eksternal membuat, mengubah, dan menghancurkan instance.
        const originalEditor = window.ClassicEditor;
        const changeHandlers = [];
        const editor = {
            getData: vi.fn(() => ''),
            setData: vi.fn(),
            destroy: vi.fn().mockResolvedValue(undefined),
            model: { document: { on: vi.fn((event, handler) => changeHandlers.push(handler)) } },
        };
        window.ClassicEditor = { create: vi.fn().mockResolvedValue(editor) };
        const wrapper = mount(BaseEditor, { props: { modelValue: '<p>Awal</p>' } });

        await flushPromises();
        expect(window.ClassicEditor.create).toHaveBeenCalledWith(wrapper.find('textarea').element);
        expect(editor.setData).toHaveBeenCalledWith('<p>Awal</p>');

        editor.getData.mockReturnValue('<p>Baru</p>');
        changeHandlers[0]();
        expect(wrapper.emitted('update:modelValue')).toEqual([['<p>Baru</p>']]);

        wrapper.unmount();
        await flushPromises();
        expect(editor.destroy).toHaveBeenCalled();
        window.ClassicEditor = originalEditor;
    });
});
