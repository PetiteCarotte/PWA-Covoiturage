<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'

const trajetsConducteur = ref()
const trajetsPassager = ref()

/**
 * Récupère tout les trajets dont l'utilisateur est conducteur
 */
const recuperationTrajetsConducteurs = async () => {
    try {
        const response = await axios.get('/trajets-conducteur')
        trajetsConducteur.value = response.data
        //Affiche toutes les données de tous les trajets conducteurs
        console.log(trajetsConducteur.value)
    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/**
 * Récupère tout les trajets dont l'utilisateur est passager
 */
const recuperationTrajetsPassagers = async () => {
    try {
        const response = await axios.get('/trajets-passager')
        trajetsPassager.value = response.data
    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/**
 * Appellé au chargement de la page, récupère alors tout les trajets (TODO : faire une recherche à paramètres)
 */
onMounted(() => {
    recuperationTrajetsConducteurs()
    recuperationTrajetsPassagers()
})

const router = useRouter() // Récupération du router vue-router pour la navigation

function goToConductorDetails(idTrajet) {
    router.push({
        path: '/detail-trajet-conducteur',
        query: { idTrajet: idTrajet }
    });
}

function goToPassengerDetails(idTrajet) {
    router.push({
        path: '/detail-trajet-passager',
        query: { idTrajet: idTrajet }
    });
}

function goToProposedDetails(tripId) {
    router.push({ path: '/detail-trajet-conducteur', params: { id: tripId } });
}


</script>

<template>
    <header role="banner" class="fr-header">
        <div class="fr-header__body">
            <div class="fr-container" id="container" style="padding-bottom: 15px;">
                <div class="fr-header__body-row">
                    <div class="fr-header__brand fr-enlarge-link">
                        <div class="fr-header__brand-top">
                            <div class="fr-header__logo">
                                <a href="/">
                                    <img src="/assets/logoapp.png" alt="Logo OuestGo" class="fr-logo-img" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="fr-container">
        <section>
            <h2 class="fr-h2">Mes trajets en passager</h2>
            <div v-for="trip in trajetsPassager" :key="trip.idTrajet" class="bloc-resultat"
                @click="goToPassengerDetails(trip.idTrajet)">
                <div class="bloc-heure">
                    <p>{{ trip.heure }}</p>
                    <p class="bas">{{ trip.date }}</p>
                </div>
                <div class="fr-highlight bloc-depart-arrive">
                    <p class="adresse">{{ trip.ptDepart }}</p>
                    <p class="fr-fi-arrow-down-line fr-btn--icon-left"></p>
                    <p class="adresse">{{ trip.ptArrive }}</p>
                </div>
                <div class="bloc-conducteur">
                    <p class="conducteur"><span class="fr-icon-account-circle-line" aria-hidden="true"></span>
                        {{ trip.prenomConducteur }}
                        {{ trip.nomConducteur }}
                    </p>
                </div>
            </div>
        </section>
        <section>
            <h2 class="fr-h2">Mes trajets en conducteur</h2>
            <div v-for="trip in trajetsConducteur" :key="trip.idTrajet" class="bloc-resultat"
                @click="goToConductorDetails(trip.idTrajet)">
                <div class="bloc-heure">

                    <p>{{ trip.heure }}</p>
                    <p class="bas">{{ trip.date }}</p>
                </div>
                <div class="fr-highlight bloc-depart-arrive">
                    <p class="adresse">{{ trip.ptDepart }}</p>
                    <p class="fr-fi-arrow-down-line fr-btn--icon-left"></p>
                    <p class="adresse">{{ trip.ptArrive }}</p>
                </div>
                <div class="bloc-conducteur">
                    <p class="conducteur"><span class="fr-icon-account-circle-line" aria-hidden="true"></span>
                        {{ trip.nbPassagers }} / {{ trip.nbMaxPassagers }} Passagers

                    </p>
                    <p v-if="trip.nouvellesDemandes" class="nouvelleDemandes">
                        <span  class="clignotant"></span>
                        Nouvelle demande
                    </p>
                </div>
            </div>
        </section>
        <section>
            <h2 class="fr-h2">Mes propositions de trajets</h2>
            <div v-for="trip in proposedTrips" :key="trip.id" class="bloc-resultat"
                @click="goToProposedDetails(trip.id)">
                <div class="bloc-heure">
                    <p>{{ trip.time }}</p>
                    <p class="bas">{{ trip.date }}</p>
                </div>
                <div class="fr-highlight bloc-depart-arrive">
                    <p class="adresse">{{ trip.from }}</p>
                    <p class="fr-fi-arrow-down-line fr-btn--icon-left"></p>
                    <p class="adresse">{{ trip.to }}</p>
                </div>
                <div class="bloc-conducteur">
                    <p class="conducteur"><span class="fr-icon-taxi-line" aria-hidden="true"></span>
                        {{ trip.passengers }} passagers - {{ trip.status }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.nouvelleDemandes{
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

.bloc-resultat{
    margin-top: 15px;
}
.fr-logo-img {
    max-height: 60px; 
    width: auto; 
    display: block; 
    margin: 0 auto; 
    border-radius: 10%;
    justify-content: center;
}

.fr-h2{
    padding-top: 15px;
}

.bas {
    margin-bottom: 0%;
}

.conducteur {
    text-align: left;
}

.fr-fi-arrow-down-line {
    margin-bottom: 0%;
    text-align: left;
}

.fr-highlight {
    margin-left: 0%;
}

.adresse {
    text-align: left;
    margin-bottom: 0px;
}

.bloc-resultat {
    display: flex;
    flex-direction: row;
    border-radius: 20px;
    background-color: white;
    box-shadow: #eeeeee;
    padding-top: 5px;
    border: 1px solid #ccc;

}

.bloc-heure {
    width: 20%;
}

.bloc-depart-arrive {
    width: 45%;
}

.bloc-conducteur {
    width: 35%;
}

.bloc-conducteur,
.bloc-depart-arrive,
.bloc-heure {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin: 13px;
}

p {
    color: black;
    text-align: center;
    font-size: medium;
}

.bloc-resultat:hover {
    background-color: #eeeeee;
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