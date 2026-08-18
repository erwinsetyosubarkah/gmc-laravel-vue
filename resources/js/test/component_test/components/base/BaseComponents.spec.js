import { describe, expect, it } from 'vitest'
import { shallowMount } from '@vue/test-utils'

const moleculeComponents = import.meta.glob('../../../../components/base/molecules/*.vue', {
    eager: true,
    import: 'default'
})
const organismComponents = import.meta.glob('../../../../components/base/organisms/*.vue', {
    eager: true,
    import: 'default'
})
const templateComponents = import.meta.glob('../../../../components/base/templates/*.vue', {
    eager: true,
    import: 'default'
})

const components = {
    ...moleculeComponents,
    ...organismComponents,
    ...templateComponents
}

// Data lengkap ini membuat setiap komponen dapat dirender tanpa bergantung pada API.
const defaultProps = {
    id: 1,
    modelValue: '',
    errors: {},
    links: [],
    articles: [],
    categories: [],
    events: [],
    galleries: [],
    clients: [],
    products: [],
    users: [],
    posts: [],
    data: [],
    item: {},
    article: {
        id: 1,
        title: 'Artikel contoh',
        excerpt: 'Ringkasan artikel',
        category: { category_name: 'Berita' },
        created_at: '2024-01-01',
        post_image: 'article.jpg'
    },
    description: 'Deskripsi contoh',
    backgroundImage: '/img/banner.jpg',
    isSubmitting: false,
    page: 1,
    total: 1,
    perPage: 10,
    dataImage: [],
    listDataCard: [],
    noData: ''
}

const mountOptions = {
    props: defaultProps,
    global: {
        stubs: {
            BaseButton: {
                template: '<button type="button" @click="$emit(\'click\')"><slot /></button>'
            },
            RouterLink: { template: '<a><slot /></a>' },
            RouterView: { template: '<div><slot /></div>' }
        },
        mocks: {
            $diffForHumans: () => 'baru saja'
        }
    }
}

describe('base molecules, organisms, and templates', () => {
    it.each(Object.entries(components))('renders %s without runtime errors', (path, component) => {
        // Smoke test ini memastikan setiap base component bisa dikompilasi dan dirender mandiri.
        const props = {
            ...defaultProps,
            modelValue: /LoginForm|RegisterForm/.test(path)
                ? { name: '', username: '', email: '', password: '', password2: '' }
                : defaultProps.modelValue,
            noData: path.includes('CustListCard') ? '' : defaultProps.noData
        }
        const wrapper = shallowMount(component, { ...mountOptions, props })

        expect(wrapper.exists()).toBe(true)
    })

    it('emits search and model updates from ArticleSearchForm', async () => {
        // Memastikan molecule pencarian menghubungkan v-model dan submit ke parent.
        const component = components['../../../../components/base/molecules/ArticleSearchForm.vue']
        const wrapper = shallowMount(component, mountOptions)

        await wrapper.find('input').setValue('layanan')
        await wrapper.find('form').trigger('submit')

        expect(wrapper.emitted('update:modelValue')).toEqual([['layanan']])
        expect(wrapper.emitted('search')).toHaveLength(1)
    })

    it.each([
        ['EventActionButtons', '../../../../components/base/molecules/EventActionButtons.vue'],
        ['GaleryActionButtons', '../../../../components/base/molecules/GaleryActionButtons.vue'],
        ['MyclientActionButtons', '../../../../components/base/molecules/MyclientActionButtons.vue'],
        ['MyproductActionButtons', '../../../../components/base/molecules/MyproductActionButtons.vue'],
        ['PostActionButtons', '../../../../components/base/molecules/PostActionButtons.vue'],
        ['UserActionButtons', '../../../../components/base/molecules/UserActionButtons.vue']
    ])('forwards edit and delete events from %s', async (name, path) => {
        // Memastikan action molecule meneruskan id item pada aksi edit dan hapus.
        const wrapper = shallowMount(components[path], mountOptions)
        const buttons = wrapper.findAll('button')

        await buttons[0].trigger('click')
        await buttons[1].trigger('click')

        expect(wrapper.emitted('edit')).toContainEqual([1])
        expect(wrapper.emitted('delete')).toContainEqual([1])
    })

    it('emits the selected URL from ArticlePagination', async () => {
        // Memastikan pagination mengirim URL halaman yang dipilih dan menandai status link.
        const component = components['../../../../components/base/organisms/ArticlePagination.vue']
        const wrapper = shallowMount(component, {
            ...mountOptions,
            props: { links: [{ label: '2', url: '/articles?page=2', active: false }] }
        })

        await wrapper.find('a').trigger('click')

        expect(wrapper.find('.page-item').classes()).not.toContain('active')
        expect(wrapper.emitted('change-page')).toEqual([['/articles?page=2']])
    })

    it('emits create from the page shell template', async () => {
        // Memastikan template shell menyediakan aksi create untuk halaman administrasi.
        const component = components['../../../../components/base/organisms/CategoryPageShell.vue']
        const wrapper = shallowMount(component, mountOptions)

        await wrapper.find('button').trigger('click')

        expect(wrapper.emitted('create')).toBeTruthy()
    })

    describe('pengujian rendering data dari props', () => {
        it('renders banner description from the backgroundImage and description props', () => {
            // Memastikan nilai props tampil pada teks dan style template banner.
            const component = components['../../../../components/base/templates/CustBanner.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: { backgroundImage: '/img/hero.jpg', description: 'Layanan komunitas' }
            })

            expect(wrapper.find('p').text()).toBe('Layanan komunitas')
            expect(wrapper.find('section').attributes('style')).toContain('/img/hero.jpg')
        })

        it('renders each card item received through listDataCard', () => {
            // Memastikan data list diterjemahkan menjadi kartu beserta judul dan kategori.
            const component = components['../../../../components/base/templates/CustListCard.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: {
                    listDataCard: [
                        {
                            title: 'Program pertama',
                            category_name: 'Kegiatan',
                            action_url: '/program/1',
                            img_url: '/img/program.jpg',
                            excerpt: 'Ringkasan program',
                            created_at_humans: 'hari ini'
                        }
                    ],
                    noData: 'Tidak ada data'
                }
            })

            expect(wrapper.find('h4').text()).toContain('Program pertama')
            expect(wrapper.text()).toContain('Kegiatan')
            expect(wrapper.find('img').attributes('src')).toBe('/img/program.jpg')
        })

        it('renders the page title received by ArticlePageShell', () => {
            // Memastikan judul halaman berasal dari props dan tampil pada heading shell.
            const component = components['../../../../components/base/organisms/ArticlePageShell.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: { pageTitle: 'Daftar Artikel' }
            })

            expect(wrapper.find('h2').text()).toBe('Daftar Artikel')
        })
    })

    describe('pengujian conditional rendering', () => {
        it('shows cards and hides the empty state when listDataCard has data', () => {
            // Memastikan cabang v-if menampilkan daftar ketika data tersedia.
            const component = components['../../../../components/base/templates/CustListCard.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: {
                    listDataCard: [{ title: 'Kartu tersedia', action_url: '/' }],
                    noData: 'Tidak ada data'
                }
            })

            expect(wrapper.find('.service-block').exists()).toBe(true)
            expect(wrapper.text()).not.toContain('Tidak ada data')
        })

        it('shows the empty state and hides cards when listDataCard is empty', () => {
            // Memastikan cabang v-else menampilkan pesan kosong tanpa kartu.
            const component = components['../../../../components/base/templates/CustListCard.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: { listDataCard: [], noData: 'Tidak ada data' }
            })

            expect(wrapper.find('.service-block').exists()).toBe(false)
            expect(wrapper.text()).toContain('Tidak ada data')
        })
    })

    describe('pengujian slot organisms dan templates', () => {
        it('renders named slots in WebHomePageShell', () => {
            // Memastikan template homepage menempatkan setiap konten pada slot bernama yang tepat.
            const component = components['../../../../components/base/organisms/WebHomePageShell.vue']
            const wrapper = shallowMount(component, {
                slots: {
                    banner: '<div data-test="banner">Banner</div>',
                    carousel: '<div data-test="carousel">Carousel</div>',
                    'article-list': '<div data-test="article-list">Artikel</div>'
                }
            })

            expect(wrapper.find('[data-test="banner"]').text()).toBe('Banner')
            expect(wrapper.find('[data-test="carousel"]').text()).toBe('Carousel')
            expect(wrapper.find('[data-test="article-list"]').text()).toBe('Artikel')
        })

        it('renders named and default slots in ArticlePageShell', () => {
            // Memastikan shell artikel mendukung slot pencarian dan konten utama secara terpisah.
            const component = components['../../../../components/base/organisms/ArticlePageShell.vue']
            const wrapper = shallowMount(component, {
                ...mountOptions,
                props: { pageTitle: 'Artikel' },
                slots: {
                    search: '<input data-test="search" />',
                    default: '<div data-test="content">Konten artikel</div>'
                }
            })

            expect(wrapper.find('[data-test="search"]').exists()).toBe(true)
            expect(wrapper.find('[data-test="content"]').text()).toBe('Konten artikel')
        })

        it('renders all named slots in WebContactPageShell', () => {
            // Memastikan template kontak tidak kehilangan salah satu bagian konten bernama.
            const component = components['../../../../components/base/organisms/WebContactPageShell.vue']
            const wrapper = shallowMount(component, {
                slots: {
                    'page-title': '<h1 data-test="title">Kontak</h1>',
                    'contact-cards': '<div data-test="cards">Kartu kontak</div>',
                    'contact-callout': '<div data-test="callout">Hubungi kami</div>'
                }
            })

            expect(wrapper.find('[data-test="title"]').exists()).toBe(true)
            expect(wrapper.find('[data-test="cards"]').exists()).toBe(true)
            expect(wrapper.find('[data-test="callout"]').exists()).toBe(true)
        })
    })
})
