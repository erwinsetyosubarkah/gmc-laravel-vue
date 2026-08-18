import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseAuthLogo from '@/components/base/atoms/BaseAuthLogo.vue';

describe('BaseAuthLogo', () => {
    it('renders an empty source by default', () => {
        // Memastikan logo tidak menunjuk ke file tertentu ketika src belum diberikan.
        const wrapper = mount(BaseAuthLogo);

        expect(wrapper.find('img').attributes('src')).toBe('');
    });

    it('resolves local and absolute logo sources', () => {
        // Memastikan path lokal memakai storage, sedangkan URL eksternal tetap utuh.
        const localWrapper = mount(BaseAuthLogo, { props: { src: '/brand/logo.png' } });
        const externalWrapper = mount(BaseAuthLogo, {
            props: { src: 'https://cdn.example.com/logo.png' },
        });

        expect(localWrapper.find('img').attributes('src')).toBe('/storage/brand/logo.png');
        expect(externalWrapper.find('img').attributes('src')).toBe('https://cdn.example.com/logo.png');
    });

    it('renders the expected logo attributes', () => {
        // Memastikan logo memiliki class dan ukuran yang dibutuhkan layout autentikasi.
        const wrapper = mount(BaseAuthLogo);
        const image = wrapper.find('img');

        expect(image.classes()).toEqual([
            'brand-image',
            'img-circle',
            'elevation-3',
            'd-block',
            'mb-2',
        ]);
        expect(image.attributes()).toMatchObject({ height: '80', width: '80', alt: '' });
    });
});
