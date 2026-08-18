import { describe, expect, it } from 'vitest';

import { mount } from '@vue/test-utils';
import BaseArticleImage from '@/components/base/atoms/BaseArticleImage.vue';

describe('BaseArticleImage', () => {
    it('renders the fallback image when src is empty', () => {
        // Memastikan gambar default digunakan ketika artikel tidak memiliki gambar.
        const wrapper = mount(BaseArticleImage);

        expect(wrapper.find('img').attributes('src')).toBe('/storage/no-image.png');
    });

    it('renders the fallback image when src is not provided', () => {
        // Memastikan nilai default prop tetap bekerja ketika src tidak dikirimkan.
        const wrapper = mount(BaseArticleImage, {
            props: { src: '' },
        });

        expect(wrapper.find('img').attributes('src')).toBe('/storage/no-image.png');
    });

    it('prefixes a relative image path with the storage directory', () => {
        // Memastikan path file relatif diarahkan ke lokasi penyimpanan publik Laravel.
        const wrapper = mount(BaseArticleImage, {
            props: { src: 'articles/example.jpg' },
        });

        expect(wrapper.find('img').attributes('src')).toBe('/storage/articles/example.jpg');
    });

    it('removes leading slashes before prefixing a relative image path', () => {
        // Memastikan slash di awal path tidak menghasilkan URL dengan double slash.
        const wrapper = mount(BaseArticleImage, {
            props: { src: '/articles/example.jpg' },
        });

        expect(wrapper.find('img').attributes('src')).toBe('/storage/articles/example.jpg');
    });

    it('keeps an absolute image URL unchanged', () => {
        // Memastikan gambar dari layanan eksternal tidak diberi prefix storage lokal.
        const imageUrl = 'https://cdn.example.com/articles/example.jpg';
        const wrapper = mount(BaseArticleImage, {
            props: { src: imageUrl },
        });

        expect(wrapper.find('img').attributes('src')).toBe(imageUrl);
    });

    it('renders the default alt text', () => {
        // Memastikan gambar tetap memiliki teks alternatif yang informatif secara default.
        const wrapper = mount(BaseArticleImage);

        expect(wrapper.find('img').attributes('alt')).toBe('Article image');
    });

    it('renders a custom alt text and the responsive image class', () => {
        // Memastikan alt dari pemanggil diteruskan dan class responsif selalu diterapkan.
        const wrapper = mount(BaseArticleImage, {
            props: {
                alt: 'Company profile article',
            },
        });

        expect(wrapper.find('img').attributes('alt')).toBe('Company profile article');
        expect(wrapper.find('img').classes()).toContain('img-fluid');
    });
});
