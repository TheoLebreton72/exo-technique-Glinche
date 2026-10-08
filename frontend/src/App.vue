<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'

const status = ref('...')
onMounted(async () => {
  try {
    const { data } = await api.get('/ping')
    status.value = data.status
  } catch (e) {
    status.value = 'erreur : ' + (e.response?.status ?? e.message)
  }
})
</script>

<template>
  <h1>API : {{ status }}</h1>
</template>
