export const limitText = (text, limit = 100) => {
  if (!text) return '';

  // 1. Proses STRIP_TAGS: Menghapus semua tag HTML menggunakan Regex
  const cleanText = text.replace(/<\/?[^>]+(>|$)/g, "");

  // 2. Jika teks lebih pendek dari limit, langsung kembalikan teks bersih
  if (cleanText.length <= limit) return cleanText;

  // 3. Proses SUBSTR: Potong teks sesuai limit dan tambahkan titik-titik (...)
  return cleanText.substring(0, limit) + '...';
};
