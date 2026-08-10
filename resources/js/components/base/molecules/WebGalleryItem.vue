<template>
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="service-block mb-5">
      <img
        :src="imageUrl"
        :alt="title"
        class="img-fluid"
        style="cursor: zoom-in;"
        id="prevImg"
        @click="openPreview"
      >
      <div class="content">
        <h4 class="mt-4 mb-2 title-color d-inline">{{ title }}</h4>
        <small class="float-right"><i class="icofont-calendar mr-2"></i><strong> {{ createdAt }}</strong></small>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  imageUrl: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  createdAt: {
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
