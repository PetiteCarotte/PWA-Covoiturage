<!-- Représente l'affichage de un trajet pour un résultat de la recherche de trajets  -->

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BlocResultatRecherche from './BlocResultatRecherche.vue'
import axios from 'axios'

const props = defineProps({
    idBase: Number,
    idDomicile: Number,
    booleenTrajetBaseDomicile: String,
    typeTrajet: String,
    jours: Array,
    heure: String,
    date: String
})

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const jours = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
    const mois = ["", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
    return `${jours[date.getDay()]} ${date.getDate()} ${mois[date.getMonth() + 1]}`;
};

const formattedDate = ref(formatDate(props.date));

const route = useRoute();

const idBase = ref(route.query.idBase);
const idDomicile = ref(route.query.idDomicile);

const resultatsRecherche = ref('')


/**
 * Récupère tout les trajets de la base de données
 */
const recuperationTrajets = async () => {
    try {
        if (props.typeTrajet === "Ponctuel") {
            const response = await axios.get('/api/recherche-trajets/' + props.date)
            //console.log("result",response.data) 
            resultatsRecherche.value = response.data
        } else {
            const response = await axios.get('/api/recherche-trajets')
            //console.log("result",response.data) 
            resultatsRecherche.value = response.data
        }
    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/* Constantes pour les champs des adresses */
const domicile = ref('')
const villeDomicile = ref('')
const base = ref('')
const villeBase = ref('')

/**
 * Récupère du paramètre domicile
 */
const recuperationDomicile = async () => {
    try {
        const response = await axios.get('/api/adresses/domicile/' + props.idDomicile)
        //console.log("result",response.data) 

        domicile.value = response.data.Intitule
        villeDomicile.value = response.data.Ville

        if (props.booleenTrajetBaseDomicile === "true") {
            arrive.value = villeDomicile.value
        } else {
            depart.value = villeDomicile.value
        }

    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/**
 * Récupère du paramètre base
 */
const recuperationBase = async () => {
    try {
        const response = await axios.get('/api/adresses/base-aerienne/' + props.idBase)
        base.value = response.data.Intitule
        villeBase.value = response.data.Ville

        if (props.booleenTrajetBaseDomicile === "true") {
            depart.value = villeBase.value
        } else {
            arrive.value = villeBase.value
        }

    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/* Constantes pour l'affichage des paramètres de la recherche */
const depart = ref("")
const arrive = ref("")

/**
 * Appellé au chargement de la page, récupère alors tout les trajets (TODO : faire une recherche à paramètres)
 */
onMounted(() => {
    recuperationTrajets()
    recuperationDomicile()
    recuperationBase()
})

/* Variable pour l'affichage de l'heure */
var directionTrajet = ref("Arrivé")
if (props.booleenTrajetBaseDomicile === "true") {
    var directionTrajet = ref("Départ")
}

const router = useRouter() // Récupération du router vue-router pour la navigation

const ptDepart = ref('')
const ptArrive = ref('')

/**
 * Ramène sur l'interface de recherche
 */
const retour = () => {
    if (props.booleenTrajetBaseDomicile === "true") {
        ptArrive.value = domicile.value
        ptDepart.value = base.value
    } else {
        ptDepart.value = domicile.value
        ptArrive.value = base.value
    }

    router.push({
        path: '/recherche',
        query: {
            ptDepart: ptDepart.value,
            ptArrive: ptArrive.value,
            booleenTrajetBaseDomicile: props.booleenTrajetBaseDomicile
        }
    });
}

/**
 * Création d'une alerte lorsqu'un trajet correspondant est publié
 */
function alerte() {
    window.open('https://javascript.info');

    // TODO :
}


</script>

<template>
    <div class="fr-container fr-mt-4w">
        <div class="fr-grid-row fr-grid-row--gutters">
            <div class="fr-col-12 fr-col-md-3">
                <button class="fr-btn fr-fi-arrow-left-line fr-btn--icon-left" @click="retour">Retour</button>
            </div>
            <div class="fr-col-12 fr-col-md-9">
                <h1 class="fr-h2">{{ depart }} - {{ arrive }}</h1>
                <!-- <p class="fr-text--lg">Trajet {{ typeTrajet }} - {{ directionTrajet }} à {{ heure }}</p> -->
                <p class="fr-text--lg">Trajet {{ typeTrajet }} pour le {{ formattedDate  }}</p>
            </div>
        </div>

        <div class="fr-grid-row fr-grid-row--gutters fr-mt-2w">
            <div class="fr-col-12" v-for="(resultat, index) in resultatsRecherche" :key="index">
                <BlocResultatRecherche :idTrajet="resultat.idTrajet" :ptDepart="resultat.ptDepart"
                    :ptArrive="resultat.ptArrive" :typeTrajet="resultat.typeTrajet" :heureDepart="resultat.heureDepart"
                    :heureArrive="resultat.heureArrive" :prenomConducteur="resultat.prenomConducteur"
                    :nomConducteur="resultat.nomConducteur" :uniteConducteur="resultat.uniteConducteur"
                    :idDomicile="props.idDomicile" />
            </div>
        </div>
        <div class="fr-grid-row fr-grid-row--center fr-mt-4w">
            <button class="fr-btn fr-btn--success fr-btn--icon-right fr-fi-information-line" @click="alerte">Créer une
                alerte</button>
        </div>
    </div>
</template>

<style scoped>
.fr-container {
    padding: 0 1rem;
}

.fr-grid-row {
    display: flex;
    flex-wrap: wrap;
}

.fr-col-12 {
    flex: 0 0 100%;
}

.fr-col-md-3 {
    flex: 0 0 25%;
}

.fr-col-md-9 {
    flex: 0 0 75%;
}

.fr-mt-4w {
    margin-top: 4rem;
}

.fr-mt-2w {
    margin-top: 2rem;
}

.fr-grid-row--center {
    justify-content: center;
}

.fr-btn--icon-left {
    padding-left: 1.5rem;
}

.fr-btn--icon-right {
    padding-right: 1.5rem;
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
