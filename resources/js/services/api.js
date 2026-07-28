import axios from 'axios'

const apiClient = axios.create({
  // Menggunakan domain aktif browser atau via file .env jika ada
  baseURL: import.meta.env.VITE_API_BASE_URL || window.location.origin,
  timeout: 10000, // Maksimal waktu tunggu respons (10 detik)
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

// (Opsional) Interceptor untuk menyisipkan Token JWT / CSRF Laravel otomatis
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (csrfToken && !config.headers['X-CSRF-TOKEN']) {
    config.headers['X-CSRF-TOKEN'] = csrfToken
  }

  return config
}, (error) => {
  return Promise.reject(error)
})

export default apiClient
