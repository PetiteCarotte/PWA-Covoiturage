<template>
  <div class="fr-container fr-mt-4w">
    <div class="fr-grid-row fr-grid-row--gutters">
      <div class="fr-col-12">

        <button class="fr-btn fr-fi-arrow-left-line fr-btn--icon-left" @click="goBack">Retour</button>
        <h1 class="fr-h2 fr-mt-3w">Mon trajet en conducteur</h1>
        <div>
          <div class="fr-col-12">
            <h4 class="fr-h4 fr-mt-3w">Informations</h4>
            <div class="">
              <p><span class="label">Date de départ:</span> {{ trip.Date_Depart }}</p>
              <p><span class="label">Départ:</span> {{ trip.ptDepart }}</p>
              <p><span class="label">Arrivée:</span> {{ trip.ptArrive }}</p>
            </div>
            <h2 class="passengers-label">Passagers enregistrés</h2>
            <div class="passenger-info" v-for="passager in trip.passagers" :key="passager.id"
              @click="voirReservation(passager)">
              <p class="name"><span class="label">Nom:</span> {{ passager.prenomPassager }} {{ passager.nomPassager }}
              </p>
              
              <p class="unite"><span class="label"> Unité:</span> {{ passager.unite }}
                <!-- <div v-if="passager.statut == 0" class="cercle"></div> -->
              </p>
              <p><span class="phone"><span class="label">Téléphone:</span> {{ passager.telephone }}</span></p>
              <p class="contact-info">
                <span class="route"><span class="label">Adresse:</span> {{ passager.adresse.Intitule }} <span
                    v-if="passager.statut == 0" class="reservation"><span class="clignotant"></span>Réservation en attente d'une
                    réponse...
                  </span></span>
              </p>
            </div>
            <p class="passenger-count">{{ trip.nbPassagers }}/{{ trip.nbMaxPassagers }} passagers</p>

            <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
              <div class="fr-col-auto">
                <button class="fr-btn fr-fi-edit-fill fr-btn--icon-left" @click="modifyTrip">
                  Modifier
                </button>
              </div>
              <div class="fr-col-auto">
                <button class="fr-btn fr-btn--secondary fr-icon-delete-fill fr-btn--icon-left" @click="deleteTrip">
                  Supprimer mon covoiturage
                </button>
              </div>
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


const trip = ref({});
const route = useRoute();
const router = useRouter(); // Récupération du router vue-router pour la navigation

const props = defineProps({
  idTrajet: Number
})

onMounted(() => {
  fetchTripDetails();
});

const fetchTripDetails = async () => {
  console.log(route.query.idTrajet)
  try {
    const response = await axios.get('/api/trajets/' + route.query.idTrajet)
    trip.value = response.data
  } catch (error) {
    console.error('Error fetching trajets:', error)
  }
}

function goBack() {
  router.back();
}

function modifyTrip() {
  router.push({ path: '/modification-trajet' });
}

async function deleteTrip() {
  try {
    const response = await axios.delete('/api/trajets/' + route.query.idTrajet);
    afficherMessageFunc("Le trajet a été supprimé avec succès", "Succès");
    router.push({
      path: '/vos-trajets',
    });
  } catch (error) {
    console.error(error);
    afficherMessageFunc(error.response?.data?.error || 'Erreur inconnue', "Erreur");
    errorMessage.value = error.response?.data?.error || 'Erreur inconnue';
  }
}

function voirReservation(passager) {
  if (passager.statut == 0)
    router.push({
      path: '/reservation',
      query: { idReservation: passager.idReservation }
    });
}

</script>

<style scoped>
.reservation{
    display: flex;
    align-items: center;
    color: green;
    animation: clignoter 2s infinite;
    margin-bottom: 0%

}

.clignotant {
    height: 20px;
    width: 20px;
    background-color: green;
    border-radius: 50%;
    display: inline-block;
    margin-left: 5px;
    margin-right: 5px;
   
}

@keyframes clignoter {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}
.fr-grid-row--gutters {
  justify-content: space-between;
}

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

.cercle {
  padding: 10px;
  width: 20px;
  color: #000;
  float: right;
  text-align: center;

  background: url('assets/icons/trajet-rond.png');
  background-size: 20px 20px;
  background-repeat: no-repeat;
  background-position: center;
}

.reservation {
  float: right;
}

.passenger-info {
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  margin-top: 10px;
  border: 1px solid #ccc;
  
}
.passenger-info:hover{
background-color: #f0f0f0;
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

.modify-button,
.delete-button {
  padding: 10px 20px;
  margin: 0 10px;
  font-size: 1rem;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.modify-button {
  background-color: #4CAF50;
}

.modify-button:hover {
  background-color: #45a049;
}

.delete-button {
  background-color: #f44336;
}

.delete-button:hover {
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
