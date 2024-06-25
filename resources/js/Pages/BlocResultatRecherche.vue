<!-- Représente l'affichage de un trajet pour un résultat de la recherche de trajets  -->

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
    idTrajet: Number,
    ptDepart: String,
    ptArrive: String,
    typeTrajet: String,
    heureDepart: String,
    heureArrive: String,
    nomConducteur: String,
    prenomConducteur: String,
    uniteConducteur: String,
    idDomicile: Number
})

const idTrajet = ref(props.idTrajet) // On stocke le props de l'id du trajet dans une constante pour puvoir l'utiliser dans la réservation
const idDomicile = ref(props.idDomicile)

const router = useRouter() // Récupération du router vue-router pour la navigation

/**
 * Charge l'affichage détaillé du trajet, où la réservation est possible
 */
const ouvrir = () => {
    router.push({
        path: '/detail-trajet-reservation',
        query: {
            idTrajet: idTrajet.value,
            idDomicile: idDomicile.value
        }
    });
}
</script>

<template>
    <div class="bloc-resultat" @click="ouvrir">
        <div class="bloc-heure">
            <p>{{ heureDepart }}</p>
            <!-- <p>{{ heureArrive }}</p> -->
            <p class="bas">{{ typeTrajet }}</p>
        </div>
        <div>

        </div>
        
            <div class="fr-highlight bloc-depart-arrive ">
                <p class="adresse ">{{ ptDepart }}</p>
                <p class=" fr-fi-arrow-down-line fr-btn--icon-left"></p>
                <p class="adresse">{{ ptArrive }}</p>
            </div>
        
        <div class="bloc-conducteur">
            <p class="conducteur"><span class="fr-icon-account-circle-line" aria-hidden="true"></span>
                {{ prenomConducteur }}
                {{ nomConducteur }}</p>
            <p class="conducteur bas"><span class="fr-icon-shield-line" aria-hidden="true"></span>
                {{ uniteConducteur }}</p>
        </div>
    </div>
</template>

<style scoped>
.bas{
    margin-bottom: 0%;
}
.conducteur{
text-align: left;
}
.fr-fi-arrow-down-line{
    margin-bottom: 0%;
    text-align: left;
}
.fr-highlight{
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
</style>
