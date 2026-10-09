<script setup>
//Récupération du prop
const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
})

// Raccourci vers les informations du véhicule pour simplifier leur affichage dans le template
const vehicle = props.data.vehicle

//EH, GO, ES, EL
switch (vehicle.energy) {
  case 'ES':
    vehicle.energy = 'ES - Essence'
    break
  case 'GO':
    vehicle.energy = 'GO - Diesel'
    break
  case 'EH':
    vehicle.energy = 'EH - Essence-Electrique'
    break
  case 'EL':
    vehicle.energy = 'EL - Electrique'
    break
}

// Récupération de l'image principale du véhicule
const mainPicture = props.data.pictures.find((picture) => picture.type === 'MAIN')
</script>
<template>
  <div class="vehicle-card">
    <img :src="mainPicture?.url" height="200" alt="" class="vehicle-image" />
    <div class="vehicle-content">
      <div class="brand">Marque : {{ vehicle.manufacturer }}</div>
      <div class="model">Modèle : {{ vehicle.model }}</div>
      <div class="version">Version : {{ vehicle.finish }}</div>
      <div class="info">Kilométrage : {{ vehicle.mileage }} km</div>
      <div class="info">Energie : {{ vehicle.energy }}</div>
      <div class="price">Prix : {{ vehicle.prices.merchantPrice }} €</div>
    </div>
  </div>
</template>
<style scoped>
.vehicle-card {
  background-color: #ffffff;
  border-radius: 8px;
  overflow: hidden;
}

/* Image */
.vehicle-image {
  width: 100%;
  height: 210px;
  object-fit: cover;
  display: block;
}

/* Contenu */
.vehicle-content {
  padding: 16px;
}

/* Marque */
.brand {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Modèle */
.model {
  margin-top: 2px;
  font-size: 20px;
  font-weight: 600;
}

/* Version */
.version {
  margin-top: 6px;
  font-size: 13px;
  line-height: 1.4;
}

/* Informations */
.info {
  margin-top: 10px;
  font-size: 13px;
}

/* Prix */
.price {
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid #000000;

  font-size: 19px;
  font-weight: 600;
}
</style>
