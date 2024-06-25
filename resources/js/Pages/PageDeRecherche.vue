<!-- Représente l'interface de recherche de trajets  -->

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { inject } from 'vue'
import axios from 'axios'

const props = defineProps({
    ptDepart: String,
    ptArrive: String,
    booleenTrajetBaseDomicile: String
})

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

// Constantes pour contenir toutes les adresses en fonction de leur type
const domiciles = ref('')
const basesAeriennes = ref('')

// Constantes contenant les adresses proposées dans les champs Départ/Arrivé
const departs = ref()
const arrives = ref()

// Constantes des contenus des différents champs Départ / Arrivé
const depart = ref(props.ptDepart)
const arrive = ref(props.ptArrive)
const heure = ref()
const date = ref()

// Gèrent le grisage de la date (trajet régulier)
const dateId = ref(['non-grise', 'grise'])
const indexBouttonSwitch = ref(0)
const estGrise = ref(false)
const trajetRegulier = ref(false)

const jours = ref(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']);
const jourValeur = ref([false, false, false, false, false, false, false]); // Tableau pour le modèle des checkboxes

// Constantes pour l'affichage des données de recherche
const typesDeTrajet = ref(['Domicile -> Base militaire', 'Base militaire -> Domicile'])
const temp = ref() // Utilisé pour l'échange de données

// Constante pour le sens du trajet
const booleenTrajetBaseDomicile = ref(0)

const router = useRouter() // Récupération du router vue-router pour la navigation

// Constantes pour les passer les ids des base et domicile choisi dans la recherche
const idDomicile = ref()
const idBase = ref()

/**
 * Récupère tout les domiciles de la base de données
 */
const recuperationDomiciles = async () => {
    try {
        const response = await axios.get('/api/adresses/domicile')
        domiciles.value = response.data

        // Chargement du contenu du menu déroulant chargé avec les domiciles
        if (props.booleenTrajetBaseDomicile == 'true') {
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
        if (props.booleenTrajetBaseDomicile === 'true') {
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

    // Récupération de la date et de l'heure actuelle
    const maintenant = new Date()
    const anneeActuelle = maintenant.getFullYear()
    const moisActuel = maintenant.getMonth() + 1 // Les mois commencent à partir de 0
    const jourActuel = maintenant.getDate()

    date.value = `${anneeActuelle}-${moisActuel.toString().padStart(2, '0')}-${jourActuel.toString().padStart(2, '0')}`

    const heureActuelle = maintenant.getHours()
    const minuteActuelle = maintenant.getMinutes()

    heure.value = `${heureActuelle.toString().padStart(2, '0')}:${minuteActuelle.toString().padStart(2, '0')}`

})

const changerIdGrise = () => {
    indexBouttonSwitch.value = (indexBouttonSwitch.value + 1) % dateId.value.length
    estGrise.value = !estGrise.value
}

if (props.booleenTrajetBaseDomicile === 'true') {
    booleenTrajetBaseDomicile.value = 1
}

/**
 * Permet l'inversion du sens du trajet
 */
const basculerTypeDeTrajet = () => {
    booleenTrajetBaseDomicile.value = (booleenTrajetBaseDomicile.value + 1) % typesDeTrajet.value.length

    temp.value = depart.value
    depart.value = arrive.value
    arrive.value = temp.value

    /* Echange des propositions d'adresse entre les champs départ et arrivé */
    if (booleenTrajetBaseDomicile.value === 1) {
        departs.value = basesAeriennes.value
        arrives.value = domiciles.value
    } else {
        departs.value = domiciles.value
        arrives.value = basesAeriennes.value
    }

}

/**
 * Charge tout les trajets dans le résultats de recherche
 */
const recherche = () => {
    const trajetBaseDomicile = ref();
    if (booleenTrajetBaseDomicile.value === 1) {
        trajetBaseDomicile.value = true
    } else {
        trajetBaseDomicile.value = false
    }

    idDomicile.value = -1
    idBase.value = -1

    // On récupère les ids des champs départ/arrivé saisies
    for (const domicileParam of domiciles.value) {
        if (booleenTrajetBaseDomicile.value === 1) { // Si base
            if (domicileParam.Intitule == arrive.value) {
                idDomicile.value = domicileParam.Id_Adresse
            }
        } else {
            if (domicileParam.Intitule == depart.value) {
                idDomicile.value = domicileParam.Id_Adresse
            }
        }
    }
    for (const baseParam of basesAeriennes.value) {
        if (booleenTrajetBaseDomicile.value === 1) {
            if (baseParam.Intitule == depart.value) {
                idBase.value = baseParam.Id_Adresse
            }
        } else {
            if (baseParam.Intitule == arrive.value) {
                idBase.value = baseParam.Id_Adresse
            }
        }
    }

    let typeTrajet = ref("Ponctuel")
    if (trajetRegulier.value) {
        typeTrajet.value = "Régulier"
    }

    // S'ils ont été trouvés :
    if (idDomicile.value > -1 && idBase.value > -1) { // TODO : peut etre verifie que l'heure et la date sont anterieures
        router.push({
            path: '/resultat-recherche',
            query: {
                idBase: idBase.value,
                idDomicile: idDomicile.value,
                booleenTrajetBaseDomicile: trajetBaseDomicile.value,
                typeTrajet: typeTrajet.value,
                jours: [jourValeur.value[0], jourValeur.value[1], jourValeur.value[2], jourValeur.value[3], jourValeur.value[4], jourValeur.value[5], jourValeur.value[6]],
                heure: heure.value,
                date: date.value
            }
        });
    } else {
        // TODO : gestion graphique pour les champs qui poses problemes
        afficherMessageFunc("La recherche a échouée, verifiez que votre saisie est correcte", "Erreur")
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
    <div class="fr-container">
        <div class="fr-grid-row fr-grid-row--center">
            <div class="fr-col-12 fr-col-md-6">
                <div>
                    <h3 class="fr-h3">Rechercher un covoiturage</h3>
                </div>
                <div class="" >
                    <h4 class="fr-h4">{{ typesDeTrajet[booleenTrajetBaseDomicile] }}</h4>
                </div>

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

                <div class="fr-grid-row fr-grid-row--center fr-my-2w">
                    <div class="fr-col-auto">
                        <button class="fr-btn" @click="recherche">Rechercher</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.fr-header__body{
    color: #0053b3;

}
.fr-logo-img {
    max-height: 60px; 
    width: auto; 
    display: block; 
    margin: 0 auto; 
    border-radius: 10%;
    justify-content: center;
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

.type-de-trajet {
    background-color: #dddddd;
    width: 90%;
    height: 80px;
    margin: auto 5%;
    border-radius: 30px;
}

.intitule-type-de-trajet {
    color: black;
    text-align: center;
    font-size: 30px;
    margin-top: 12px;
}

.bloc-label-depart-arrive {
    display: flex;
    flex-direction: row;
    height: 50px;
    width: 75%;
    margin-left: 12.5%;
    justify-content: space-between;
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
