<script setup>
import Vehicle from '@/components/Vehicle.vue'
import api from '@/api'
import { computed, onMounted, ref } from 'vue'

const data = ref([])
const selectedBrand = ref('')

// Liste des marques, sans doublons, construite à partir des véhicules
const brands = computed(() => [...new Set(data.value.map((d) => d.vehicle.manufacturer))].sort())

// Filtre les véhicules en fonction de la marque sélectionnée
const filteredVehicles = computed(() =>
  data.value.filter((d) => !selectedBrand.value || d.vehicle.manufacturer === selectedBrand.value),
)

onMounted(async () => {
  try {
    const response = await api.get('/vehicles')
    data.value = response.data
  } catch (error) {
    console.error('Erreur API :', error)
  }
})
</script>

<template>
  <div class="vehicle-catalog">
    <div class="filter">
      <label for="brand">Marque</label>
      <select id="brand" v-model="selectedBrand">
        <option value="">Toutes les marques</option>
        <option v-for="brand in brands" :key="brand" :value="brand">{{ brand }}</option>
      </select>
    </div>

    <div class="vehicle-grid">
      <Vehicle v-for="d in filteredVehicles" :key="d.reference" :data="d" />
    </div>
  </div>
</template>
<style>
html {
  background-color: #343434;
}

.vehicle-catalog {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 20px;
}

.filter {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #ffffff;
}

.filter select {
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 14px;
}

.vehicle-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 24px;
  width: 100%;
  max-width: 1100px;
}

@media (max-width: 900px) {
  .vehicle-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .vehicle-grid {
    grid-template-columns: 1fr;
  }
}
</style>
