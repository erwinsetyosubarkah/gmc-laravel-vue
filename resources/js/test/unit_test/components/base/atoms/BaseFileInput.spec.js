import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseFileInput from '@/components/base/atoms/BaseFileInput.vue';

describe('BaseFileInput', () => {
    it('renders configured file input attributes and help text', () => {
        // Memastikan input file memiliki id, filter file, dan petunjuk yang sesuai.
        const wrapper = mount(BaseFileInput, {
            props: { id: 'photo', accept: 'image/*', helpText: 'Maksimal 2 MB' },
        });

        expect(wrapper.find('input').attributes()).toMatchObject({
            id: 'photo',
            type: 'file',
            accept: 'image/*',
        });
        expect(wrapper.find('.form-text').text()).toBe('Maksimal 2 MB');
    });

    it('emits the selected file and null when no file is selected', async () => {
        // Memastikan perubahan file mengirim file pertama atau null secara konsisten.
        const wrapper = mount(BaseFileInput);
        const file = new File(['isi'], 'photo.png', { type: 'image/png' });
        const input = wrapper.find('input');

        Object.defineProperty(input.element, 'files', { value: [file], configurable: true });
        await input.trigger('change');
        expect(wrapper.emitted('change')[0]).toEqual([file]);

        Object.defineProperty(input.element, 'files', { value: [], configurable: true });
        await input.trigger('change');
        expect(wrapper.emitted('change')[1]).toEqual([null]);
    });
});
