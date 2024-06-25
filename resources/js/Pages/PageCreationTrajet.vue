<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { inject } from 'vue'
import axios from 'axios'

const props = defineProps({
    ptDepart: String,
    ptArrive: String,
    booleenTrajetBaseDomicile: Number,
    trajetRegulier: Number,
    date: String,
    heure: String,
    jours: Array,
    bagages: Number,
    nombrePassagers: Number,
    description: String
})

const router = useRouter() // Récupération du router vue-router pour la navigation

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

// Constantes pour contenir toutes les adresses en fonction de leur type (domicile ou base)
const domiciles = ref('')
const basesAeriennes = ref('')
const departs = ref()
const arrives = ref()

// Initialisation des constantes vis à vis des props
const depart = ref(props.ptDepart)
const arrive = ref(props.ptArrive)
const booleenTrajetBaseDomicile = ref(Boolean(Number(props.booleenTrajetBaseDomicile)))
const heure = ref(props.heure)
const date = ref(props.date)
const bagage = ref(props.bagages)
const nombrePassagers = ref(props.nombrePassagers)
const description = ref(props.description)
const trajetRegulier = ref(Boolean(Number(props.trajetRegulier)))

// const lundi = ref(props.jours[0])
// const mardi = ref(props.jours[1])
// const mercredi = ref(props.jours[2])
// const jeudi = ref(props.jours[3])
// const vendredi = ref(props.jours[4])
// const samedi = ref(props.jours[5])
// const dimanche = ref(props.jours[6])

// Gèrent le grisage de la date (trajet régulier)
const dateId = ref(['non-grise', 'grise'])
const indexBouttonSwitch = ref(0)
const estGrise = ref(false)

const jours = ref(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']);
const jourValeur = ref([false, false, false, false, false, false, false]); // Tableau pour le modèle des checkboxes


/**
 * Récupère tout les domiciles de la base de données
 */
const recuperationDomiciles = async () => {
    try {
        const response = await axios.get('/api/adresses/domicile')
        domiciles.value = response.data

        // Chargement du contenu du menu déroulant chargé avec les domiciles
        if (props.booleenTrajetBaseDomicile == '1') {
            arrives.value = domiciles.value
        } else {
            departs.value = domiciles.value
        }
    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/**
 * Récupère toutes les bases aériennes de la base de données
 */
const recuperationBases = async () => {
    try {
        const response = await axios.get('/api/adresses/base-aerienne')
        basesAeriennes.value = response.data

        // Chargement du contenu du menu déroulant chargé avec les bases
        if (props.booleenTrajetBaseDomicile === '1') {
            departs.value = basesAeriennes.value
        } else {
            arrives.value = basesAeriennes.value
        }
    } catch (error) {
        console.error('Error fetching trajets:', error)
    }
}

/**
 * Appellé au chargement de la page, récupère alors tout les trajets (TODO : faire une recherche à paramètres)
 */
onMounted(() => {
    recuperationDomiciles()
    recuperationBases()
})

const changerIdGrise = () => {
    indexBouttonSwitch.value = (indexBouttonSwitch.value + 1) % dateId.value.length
    estGrise.value = !estGrise.value
}

/**
 * Permet l'inversion du sens du trajet
 */
const basculerTypeDeTrajet = () => {
    // Inverser les valeurs des champs depart et arrive
    const tempVal = depart.value;
    depart.value = arrive.value;
    arrive.value = tempVal;

    // Inverser les listes departs et arrives
    const tempList = departs.value;
    departs.value = arrives.value;
    arrives.value = tempList;

    // Inverser booleenTrajetBaseDomicile pour refléter le changement de sens du trajet
    booleenTrajetBaseDomicile.value = !booleenTrajetBaseDomicile.value;

    /* Echange des propositions d'adresse entre les champs départ et arrivé */
    if (booleenTrajetBaseDomicile.value) {
        departs.value = basesAeriennes.value;
        arrives.value = domiciles.value;
    } else {
        departs.value = domiciles.value;
        arrives.value = basesAeriennes.value;
    }
};


const verificationChamps = () => {
    //Si un des champs est vide ou si le nombre de passagers inférieur à 1
    if (!depart.value || !arrive.value || !date.value || !heure.value || nombrePassagers.value < 1) {
        let champsManquants = [];
        if (!depart.value) champsManquants.push("Départ");
        if (!arrive.value) champsManquants.push("Arrivée");
        if (!date.value) champsManquants.push("Date");
        if (!heure.value) champsManquants.push("Heure");
        if (nombrePassagers.value < 1) champsManquants.push("Nombre de passagers doit être supérieur à 0");

        alert(`Erreur dans le champ suivant: ${champsManquants.join(", ")}`);
        return false; // Annule la navigation si des champs sont manquants
    }
    return true; // Autorise la navigation si tous les champs sont remplis
}

const pageSuivante = () => {
    if (verificationChamps()) {
        // Si tous les champs sont remplis, naviguer vers la page suivante
        router.push({
            path: '/creation-suivant',
            query: {
                ptDepart: depart.value,
                ptArrive: arrive.value,
                booleenTrajetBaseDomicile: Number(booleenTrajetBaseDomicile.value),
                trajetRegulier: Number(trajetRegulier.value),
                date: date.value,
                heure: heure.value,
                jours: [jourValeur.value[0], jourValeur.value[1], jourValeur.value[2], jourValeur.value[3], jourValeur.value[4], jourValeur.value[5], jourValeur.value[6]],
                bagages: bagage.value,
                nombrePassagers: nombrePassagers.value,
                description: description.value
            }
        });
    }
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
    <div class="fr-container" id="main-container">
        <div class="fr-grid-row fr-grid-row--center">
            <div class="fr-col-12 fr-col-md-6">
                <h1 class="fr-h3">Publier un trajet</h1>

                <!-- Départ -->
                <div class="fr-input-group fr-my-2w">
                    <label for="depart" class="fr-label">Départ</label>
                    <div class="fr-input-wrap">
                        <input list="liste-departs" v-model="depart" type="text" class="fr-input" id="depart"
                            placeholder="Départ" />
                        <datalist id="liste-departs">
                            <option v-for="option in departs" :value="option.Intitule">{{ option.Intitule }}</option>
                        </datalist>
                    </div>
                </div>
                <div class="fr-input-group fr-my-2w">
                    <button class="icone-arrows" @click="basculerTypeDeTrajet">
                    </button>
                </div>

            <!-- Arrivée -->
                <div class="fr-input-group fr-my-2w">
                    <label for="arrive" class="fr-label">Arrivée</label>
                    <div class="fr-input-wrap">
                        <input list="liste-arrives" v-model="arrive" type="text" class="fr-input" id="arrive"
                            placeholder="Arrivée" />
                        <datalist id="liste-arrives">
                            <option v-for="option in arrives" :value="option.Intitule">{{ option.Intitule }}</option>
                        </datalist>
                    </div>
                </div>

                <!-- Date et Heure -->
                <div class="fr-grid-row fr-grid-row--gutters">
                    <div class="fr-col-6">
                        <div class="fr-input-group fr-my-2w">
                            <label for="date" class="fr-label">Date</label>
                            <input type="date" v-model="date" class="fr-input" id="date" :disabled="trajetRegulier" />
                        </div>
                    </div>
                    <div class="fr-col-6">
                        <div class="fr-input-group fr-my-2w">
                            <label for="heure" class="fr-label">Heure</label>
                            <input type="time" v-model="heure" class="fr-input" id="heure" />
                        </div>
                    </div>
                </div>

                <!-- Trajet régulier et jours-->
                <div class="fr-checkbox-group fr-my-2w">
                    <input type="checkbox" id="trajetRegulier" class="fr-checkbox-input" v-model="trajetRegulier"
                        @change="changerIdGrise" />
                    <label for="trajetRegulier" class="fr-label">Trajet Régulier</label>
                </div>

                <div v-if="trajetRegulier" class="fr-grid-row fr-grid-row--gutters">
                    <div class="fr-checkbox-group fr-my-2w fr-col-4" v-for="(jour, index) in jours" :key="index">
                        <input type="checkbox" :id="jour" class="fr-checkbox-input" v-model="jourValeur[index]" />
                        <label :for="jour" class="fr-label">{{ jour }}</label>
                    </div>
                </div>

                <!-- Bagages -->
                <div class="fr-input-group fr-mt-3w">
                    <label class="fr-label">Bagages</label>
                    <select class="fr-select" v-model="bagage">
                        <option value="1">Beaucoup</option>
                        <option value="2">Moyen</option>
                        <option value="3">Peu</option>
                    </select>
                </div>

                <!-- Passagers -->
                <div class="fr-input-group fr-mt-3w">
                    <label class="fr-label">Passagers</label>
                    <input type="number" class="fr-input" v-model="nombrePassagers">
                </div>

                <!-- Description -->
                <div class="fr-input-group fr-mt-3w">
                    <label class="fr-label">Description</label>
                    <textarea class="fr-textarea custom-textarea" v-model="description" rows="2"></textarea>
                </div>

                <!-- Bouton Suivant -->
                <div class="fr-grid-row fr-grid-row--center fr-mt-3w">
                    <div class="fr-col-auto">
                        <button class="fr-btn fr-btn--secondary" @click="pageSuivante">Suivant</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>


<style scoped>
.fr-logo-img {
    max-height: 60px; 
    width: auto; 
    display: block; 
    margin: 0 auto; 
    border-radius: 10%;
    justify-content: center;
}
.fr-select{
    width: 50%;
}

.fr-container#maincontainer {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding-bottom: 20px;
}

.fr-grid-row {
    flex: 1;
}

.fr-h3 {
    padding-top: 20px;
}

.icone-arrows {
    background: url('assets/icons/fleches-bidirection.png');
    background-size: 30px 30px;
    background-repeat: no-repeat;
    background-position: center;
    width: 100%;
    height: 25px;
    justify-content: center;
}

.fr-btn {
    margin-bottom: 20px;
    position: relative;
    z-index: 10;
}

.fr-textarea.custom-textarea {
    background-color: white;
    border: 1px solid #ccc; 
    padding: 8px;
    font-size: 14px;
    width: 100%; 
    height: auto;
}

.label {
    border: none;
    border-bottom: 1px solid #dddddd;
    height: 50px;
    width: 100%;
}

.bagage {
    display: flex;
    flex-direction: row;
    width: 80%;
    margin-left: 10%;
    height: 50px;
    justify-content: space-between;
    align-items: center;
}

input[type="number"] {
    width: 20%;
    padding: 5px;
    margin-top: 5px;
    border: none;
    border-bottom: 1px solid black;
    outline: none;
    text-align: center;
    color: black;
}

input[type="date"],
input[type="time"] {
    width: 100%;
    height: 40px;
    float: right;
    color: black;
    border: 1px solid black;
    border-radius: 30px;
    padding-left: 10px;
    padding-right: 10px;
}

input[type="time"] {
    clear: both;
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
