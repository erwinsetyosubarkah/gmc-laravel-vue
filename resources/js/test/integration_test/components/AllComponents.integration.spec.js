import { beforeEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { createStore } from 'vuex'
import { createRouter, createMemoryHistory } from 'vue-router'

vi.mock('@/services/api', () => ({
    default: {
        get: vi.fn(async (url) => ({ data: fakeApiResponse(url) })),
        post: vi.fn(async () => ({ data: { status: 'success' } })),
        delete: vi.fn(async () => ({ data: { status: 'success' } })),
    },
}))

vi.mock('sweetalert2/dist/sweetalert2', () => ({
    default: { fire: vi.fn(async () => ({ isConfirmed: true })) },
    swal: { fire: vi.fn(async () => ({ isConfirmed: true })) },
}))

const components = import.meta.glob('../../../components/**/*.vue', {
    eager: true,
    import: 'default',
})

const profile = {
    id: 1,
    club_name: 'GMC Community',
    club_logo: 'logo.png',
    email: 'info@example.test',
    phone: '08123456789',
    address: 'Bandung',
    description: 'Profil komunitas',
    short_description: 'Komunitas berbagi',
    leader_name: 'Ketua GMC',
    leader_email: 'leader@example.test',
}

function fakeApiResponse(url) {
    if (url.includes('gethome')) {
        return { profile, galeries: [], articles: [] }
    }

    if (url.includes('getvisidanmisi')) {
        return { page_title: 'Visi dan Misi', visidanmisi: { title: 'Visi', content: 'Misi' } }
    }

    if (url.includes('getartikel')) {
        return { page_title: 'Artikel', artikels: { data: [], links: [] } }
    }

    if (url.includes('getevent/')) {
        return {
            page_title: 'Event',
            event: {
                event_image: 'event.jpg',
                event_title: 'Event contoh',
                event_date: '2026-01-01',
                created_at: '2026-01-01',
                event_description: 'Deskripsi event',
            },
        }
    }

    if (url.includes('getevent')) return { page_title: 'Event', events: { data: [], links: [] } }
    if (url.includes('getgalery')) return { page_title: 'Galeri', galeries: { data: [], links: [] } }
    if (url.includes('getprodukkami')) return { page_title: 'Produk', produkkami: { data: [], links: [] } }
    if (url.includes('getklienkami')) return { page_title: 'Klien', klienkami: { data: [], links: [] } }

    return { status: 'success', data: [] }
}

const store = createStore({
    state: () => ({
        profile,
        categories: [],
        newevents: [],
        auth: { authenticated: false, user: null },
    }),
    actions: { updateAuth: vi.fn() },
})

const router = createRouter({
    history: createMemoryHistory(),
    routes: [{ path: '/:pathMatch(.*)*', component: { template: '<div />' } }],
})

const mountOptions = {
    global: {
        plugins: [store, router],
        mocks: { $diffForHumans: () => 'baru saja' },
        stubs: {
            RouterLink: { template: '<a><slot /></a>' },
            RouterView: { template: '<div><slot /></div>' },
            ContentLoader: { template: '<div data-test="content-loader" />' },
        },
    },
}

const defaultProps = {
    modelValue: { name: '', username: '', email: '', password: '', password2: '' },
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
    rows: [],
    columns: [{ key: 'name', label: 'Nama' }],
    listDataCard: [],
    dataImage: [],
    item: {},
    article: {},
    initialValues: {},
    description: '',
    backgroundImage: '/img/banner.jpg',
    pageTitle: 'Halaman',
    page_title: 'Halaman',
    noData: 'Tidak ada data',
}

function propsFor(component, path) {
    const props = { ...defaultProps }
    const declaredProps = component?.props || {}

    Object.keys(declaredProps).forEach((name) => {
        if (name === 'modelValue' && /LoginForm|RegisterForm/.test(path)) {
            props[name] = defaultProps.modelValue
        } else if (!(name in props)) {
            props[name] = Array.isArray(declaredProps[name]?.default) ? [] : undefined
        }
    })

    if (path.includes('CustListCard')) props.noData = 'Tidak ada data'
    return props
}

describe('integrasi seluruh komponen Vue', () => {
    beforeEach(() => {
        document.body.innerHTML = ''
        window.ClassicEditor = undefined
    })

    it.each(Object.entries(components))('dapat di-mount: %s', async (path, component) => {
        // Smoke integration ini memastikan setiap komponen dapat bergabung dengan dependency global aplikasi.
        const wrapper = mount(component, {
            ...mountOptions,
            props: propsFor(component, path),
        })

        await flushPromises()

        expect(wrapper.exists()).toBe(true)
        wrapper.unmount()
    })

    it('menghubungkan pencarian, model input, dan pagination ke parent', async () => {
        // Test ini memverifikasi aliran event dari atom ke molecule lalu ke halaman pemanggil.
        const ArticleSearchForm = components['../../../components/base/molecules/ArticleSearchForm.vue']
        const ArticlePagination = components['../../../components/base/organisms/ArticlePagination.vue']
        const search = mount(ArticleSearchForm, mountOptions)
        const pagination = mount(ArticlePagination, {
            ...mountOptions,
            props: { links: [{ label: '2', url: '/article?page=2', active: false }] },
        })

        await search.find('input').setValue('komunitas')
        await search.find('form').trigger('submit')
        await pagination.find('a').trigger('click')

        expect(search.emitted('update:modelValue')).toEqual([['komunitas']])
        expect(search.emitted('search')).toHaveLength(1)
        expect(pagination.emitted('change-page')).toEqual([['/article?page=2']])
    })

    it('menghubungkan form kategori dengan slug otomatis dan submit valid', async () => {
        // Test ini memastikan input kategori memakai atom bersama, membuat slug, dan hanya submit saat valid.
        const CategoryForm = components['../../../components/base/molecules/CategoryForm.vue']
        const wrapper = mount(CategoryForm, mountOptions)

        await wrapper.find('#category_name').setValue('Komunitas GMC')
        expect(wrapper.find('#category_slug').element.value).toBe('komunitas-gmc')

        await wrapper.find('form').trigger('submit')
        expect(wrapper.emitted('submit')).toEqual([[
            { category_name: 'Komunitas GMC', category_slug: 'komunitas-gmc' },
        ]])
    })

    it('menghubungkan tabel data dengan action edit dan delete', async () => {
        // Test ini memastikan data melewati BaseTable dan aksi baris diteruskan sampai parent tabel.
        const UserTable = components['../../../components/base/organisms/UserTable.vue']
        const wrapper = mount(UserTable, {
            ...mountOptions,
            props: { rows: [{ id: 7, name: 'Ayu', username: 'ayu', email: 'ayu@example.test', level: 'author' }] },
        })

        const buttons = wrapper.findAll('button')
        await buttons[0].trigger('click')
        await buttons[1].trigger('click')

        expect(wrapper.text()).toContain('Ayu')
        expect(wrapper.emitted('edit')).toEqual([[7], [7]])
        expect(wrapper.emitted('delete')).toEqual([[7], [7]])
    })
})
