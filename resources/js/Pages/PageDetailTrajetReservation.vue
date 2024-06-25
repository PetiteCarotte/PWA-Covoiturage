<template>
  <div class="fr-container fr-mt-4w">
    <div class="fr-grid-row fr-grid-row--gutters">
      <div class="fr-col-12">

        <button class="fr-btn fr-fi-arrow-left-line fr-btn--icon-left" @click="goBack">Retour</button>
        <h1 class="fr-h2 fr-mt-3w">Détails du trajet</h1>
        <div>
          <div class="fr-col-12">
            <h4 class="fr-h4 fr-mt-3w">Conducteur</h4>
            <div class="">
              <p class="conducteur"> <span class="label">Nom:</span> {{ trip.prenomConducteur }} {{ trip.nomConducteur
                }} </p>
              <p class="conducteur"> <span class="label">Unité:</span> {{ trip.uniteConducteur }} </p>
            </div>
            <h4 class="fr-h4 fr-mt-3w">Informations</h4>
            <div class="">
              <p><span class="label">Date de départ:</span> {{ trip.Date_Depart }}</p>
              <p><span class="label">Départ:</span> {{ trip.ptDepart }}</p>
              <p><span class="label">Arrivée:</span> {{ trip.ptArrive }}</p>
            </div>
            <h4 class="fr-h4 fr-mt-3w">Passagers enregistrés</h4>
            <div class="passenger-info" v-for="passager in trip.passagers" :key="passager.id">
              <p class="name"><span class="label">Nom:</span> {{ passager.prenomPassager }} {{ passager.nomPassager }}</p>
              <p><span class="label"> Unité:</span> {{ passager.unite }}</p>
              <p class="contact-infos">
                <span class="route"><span class="label">Commune:</span> {{ passager.adresse.Ville }} </span>
              </p>
            </div>
            <p class="passenger-count">{{ trip.nbPassagers }}/{{ trip.nbMaxPassagers }} passagers</p>
            <div class="action-buttons">
              <button class="fr-btn fr-btn--success" @click="reserver">Réserver</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios'
import { inject } from 'vue'

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

const props = defineProps({
  idTrajet: Number,
  idDomicile: Number
})

const route = useRoute();
const router = useRouter(); // Récupération du router vue-router pour la navigation
const errorMessage = ref(null);

const idTrajet = ref(route.query.idTrajet);
const idDomicile = ref(route.query.idDomicile);

onMounted(() => {
  fetchTripDetails();
});

const trip = ref({});

const fetchTripDetails = async () => {
  try {
    const response = await axios.get('/api/trajets/' + idTrajet.value)
    trip.value = response.data
  } catch (error) {
    console.error('Error fetching trajets:', error)
  }
}



function goBack() {
  router.back();
}

function reserver() {

  axios.post('/reserver-trajet', {
    Id_Trajet: idTrajet.value,
    Id_Adresse: props.idDomicile
  })
    .then(response => {
      console.log(response);
      afficherMessageFunc("La demande de réservation a été envoyée", "Succès");
      router.push({
        path: '/vos-trajets'
      });
    })
    .catch(error => {
      // afficherMessageFunc(error, "Erreur");
      console.error(error);
      afficherMessageFunc(error.response?.data?.error || 'Erreur inconnue', "Erreur");
    });
}

</script>

<style scoped>
.bloc-principal {
  padding: 20px;
  overflow-y: auto;
  color: black;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.back-button {
  align-self: flex-start;
  margin-bottom: 1rem;
  cursor: pointer;
  background: none;
  border: none;
  color: #555;
  font-size: 1rem;
  font-weight: bold;
}

h1 {
  margin-bottom: 20px;
  color: #222;
}

.passengers-label {
  margin-top: 20px;
  font-size: 1.7rem;
  color: #222;
  text-align: left;
  ;
}

.right-aligned {
  float: right;
  margin-right: 20px;
}

.passenger-info p {
  clear: both;
  padding-bottom: 10px;
}

.contact-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1rem;
}

.phone {
  margin-right: 20px;
}

.route {
  text-align: left;
  flex: 1;
}

.detail {
  display: flex;
  justify-content: space-between;
}

.passenger-info {
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  margin-top: 10px;
  border: 1px solid #ccc;
}

.label {
  font-weight: bold;
  color: #353535;
}

p {
  font-size: 1rem;
  line-height: 1.5;
  margin-bottom: 0.5rem;
}

.passenger-count {
  text-align: right;
  font-size: 1.1rem;
  margin: 20px 0;
  color: #333;
}

.action-buttons {
  margin-top: 20px;
  text-align: center;
}

.reserver-button {
  padding: 10px 20px;
  margin: 0 10px;
  font-size: 1rem;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.reserver-button {
  background-color: #f44336;
}

.reserver-button:hover {
  background-color: #da190b;
}
@media only screen and (max-width: 600px) {
    .fr-container {
        padding-bottom: 75px;
    }
}

@media only screen and (max-width: 1024px) {
    .fr-container {
        padding-bottom: 75px;
    }
}

@media only screen and (max-width: 1440px) {
    .fr-container {
        padding-bottom: 75px;
    }
}

@media only screen and (max-width: 1920px) {
    .fr-container {
        padding-bottom: 75px;
    }
}
</style>
