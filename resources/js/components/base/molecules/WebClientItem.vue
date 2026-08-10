<template>
  <div class="testimonial-block style-2 gray-bg shadow">
    <i class="icofont-quote-right"></i>
    <div class="testimonial-thumb">
      <img
        :src="imageUrl"
        :alt="name"
        class="img-fluid"
        id="prevImg"
        style="cursor: zoom-in;"
        @click="openPreview"
      >
    </div>
    <div class="client-info">
      <h4>{{ name }}</h4>
      <span v-html="company"></span>
      <p v-html="address"></p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  imageUrl: {
    type: String,
    default: ''
  },
  name: {
    type: String,
    default: ''
  },
  company: {
    type: String,
    default: ''
  },
  address: {
    type: String,
    default: ''
  }
})

const openPreview = () => {
  if (!document || !window || !document.body || !document.createElement) return

  const modal = document.createElement('div')
  modal.className = 'web-image-zoom-modal'
  modal.style.position = 'fixed'
  modal.style.top = '0'
  modal.style.left = '0'
  modal.style.width = '100%'
  modal.style.height = '100%'
  modal.style.zIndex = '99999'
  modal.style.backgroundColor = 'rgba(0,0,0,.8)'
  modal.style.backgroundImage = `url(${props.imageUrl || ''})`
  modal.style.backgroundRepeat = 'no-repeat'
  modal.style.backgroundPosition = 'center'
  modal.style.backgroundSize = 'contain'
  modal.style.cursor = 'zoom-out'
  modal.setAttribute('role', 'dialog')

  const close = () => {
    modal.remove()
    window.removeEventListener('keydown', onKeydown)
  }

  const onKeydown = (e) => {
    if (e.key === 'Escape') {
      close()
    }
  }

  modal.addEventListener('click', close)
  window.addEventListener('keydown', onKeydown)

  document.body.appendChild(modal)
}
</script>
