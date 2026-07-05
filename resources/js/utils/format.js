export const formatRupiah = (value) => {
  if (value === null || value === undefined) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}

// Jika hanya butuh format angka ribuan tanpa teks "Rp"
export const numberFormat = (value) => {
  if (value === null || value === undefined) return '0'
  return new Intl.NumberFormat('id-ID').format(value)
}
