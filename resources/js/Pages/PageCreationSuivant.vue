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

const showDialog = ref(false)
const showAddCityDialog = ref(false)
const newCity = ref('')
const villesAjoutees = ref([])
const jours = ref([])

const router = useRouter() // Récupération du router vue-router pour la navigation

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

onMounted(() => {
    jours.value = [props.jours[0] == "true", props.jours[1] == "true", props.jours[2] == "true", props.jours[3] == "true", props.jours[4] == "true", props.jours[5] == "true", props.jours[6] == "true"]
})

const retour = () => {
    router.push({
        path: '/creation-trajet',
        query: {
            ptDepart: props.ptDepart,
            ptArrive: props.ptArrive,
            booleenTrajetBaseDomicile: props.booleenTrajetBaseDomicile,
            trajetRegulier: props.trajetRegulier,
            date: props.date,
            heure: props.heure,
            jours: props.jours,
            bagages: props.bagages,
            nombrePassagers: props.nombrePassagers,
            description: props.description
        }
    });
}

const ajouterVille = () => {
    console.log("Ville ajoutée :", newCity.value)
    villesAjoutees.value.push(newCity.value)
    newCity.value = ''
    showAddCityDialog.value = false
}

const supprimerVille = (index) => {
    villesAjoutees.value.splice(index, 1)
}

const creerTrajet = () => {

    axios.post('/create-trajets', {
        Date_Depart: props.date,
        Heure_Depart: props.heure,
        Nbre_Places: props.nombrePassagers,
        Qte_Bagages: props.bagages,
        Description: props.description,
        Trajet_Regulier: props.trajetRegulier,
        Domicile_Base: props.booleenTrajetBaseDomicile,
        ptDepart: props.ptDepart,
        ptArrive: props.ptArrive,
        jours: jours.value
    })
        .then(response => {
            console.log(response);
            afficherMessageFunc("Le trajet a été crée avec succès", "Succès");
            router.push({
                path: '/vos-trajets'

            });
        })
        .catch(error => {
            afficherMessageFunc(error, "Erreur");
            console.error(error);
        });
}

const confirmerCreationTrajet = () => {
    showDialog.value = false;
    creerTrajet();
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
    <div>       
        <!-- Contenu principal -->
        <div class="fr-container">
            <div class="fr-grid-row fr-grid-row--center">
                <div class="fr-col-12 fr-col-md-8">
                    <!-- Villes ajoutées -->
                    <div class="villes-ajoutees">
                        <h5>Saisis tes points d'arrêts :</h5>
                        <div v-for="(ville, index) in villesAjoutees" :key="index" class="ville">
                            <span>{{ ville }}</span>
                            <button class="fr-btn fr-btn--tertiary-no-outline fr-btn--icon" @click="supprimerVille(index)">
                                X
                            </button>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="fr-grid-row fr-grid-row--right fr-mt-3w">
                        <div class="fr-col-auto">
                            <button class="fr-btn fr-btn--primary" @click="showAddCityDialog = true">Ajouter</button>
                        </div>

                    </div>
                    <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
                        <div class="fr-col-auto">
                            <button class="fr-btn fr-btn--secondary" @click="retour()">Précédent</button>
                        </div>
                        <div class="fr-col-auto">
                            <button class="fr-btn fr-btn--success" @click="showDialog = true">Publier le trajet</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          <!-- Boîte de dialogue pour ajouter une ville -->
          <div v-if="showAddCityDialog" class="dialog">
            <div class="dialog-content">
                <h2>Ajouter une ville</h2>
                <input type="text" class="fr-input" v-model="newCity" placeholder="Saisir le nom de la ville" />
                <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
                    <div class="fr-col-auto">
                        <button class="fr-btn fr-btn--secondary" @click="showAddCityDialog = false">Annuler</button>
                    </div>
                    <div class="fr-col-auto">
                        <button class="fr-btn fr-btn--success" @click="ajouterVille">Ajouter</button>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Boîte de dialogue pour confirmer la création du trajet -->
        <div v-if="showDialog" class="dialog">
            <div class="dialog-content">
                <h2>Confirmation</h2>
                <p>Êtes-vous sûr de vouloir publier ce trajet ?</p>
                <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
                    <div class="fr-col-auto">
                        <button class="fr-btn fr-btn--secondary" @click="showDialog = false">Annuler</button>
                    </div>
                    <div class="fr-col-auto">
                        <button class="fr-btn fr-btn--success" @click="confirmerCreationTrajet">Confirmer</button>
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
.fr-h3{
    padding-top: 34px;
}

.fr-grid-row--gutters {
    justify-content: space-between;
}
.dialog {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.dialog-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dialog-buttons {
    padding-top: 40px;
    display: flex;
    justify-content: space-between;
}

.entete {
    width: 100%;
    height: 100px;
    background-color: white;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
}

.retour {
    width: 60px;
    height: 60px;
    top: 20px;
    left: 20px;
    position: absolute;
}

.entete>h1 {
    width: 100%;
    color: black;
    text-align: center;
}

p,
h2,
h3 {
    color: black;
}

.bouton-ajouter,
.bouton-creer,
.bouton-precedent,
.bouton-annuler {
    width: 130px;
    height: 30px;
    border-radius: 10%;
}

.boutons-a {
    display: flex;
    flex-direction: row;
    margin: 10px 0;
    justify-content: flex-end;
}

.boutons-s {
    display: flex;
    margin: 10px 0;
    justify-content: space-between;
    flex-direction: row-reverse;
}

.bouton-creer {
    background-color: green;
}


.bloc-creation-suivant {
    width: 60%;
    height: auto;
    position: fixed;
    top: 100px;
    bottom: 100px;
    padding: 10px 0;
    left: 20%;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.villes-ajoutees {
    margin-top: 20px;
}

.villes-ajoutees h3 {
    margin-bottom: 10px;
}

.ville {
    color: black;
    background-color: #f0f0f0;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ville span {
    margin-right: 10px;
}

.bouton-supprimer {
    color: black;
    background-color: transparent;
    border: none;
    font-size: 16px;
    cursor: pointer;
}


@media (max-height: 750px) {
    .entete {
        height: 60px;
    }

    .retour {
        background-size: 30px 30px;

    }

    .entete>h1 {
        font-size: medium;
        width: 100%;
        color: black;
        text-align: center;
    }

    .bloc-creation-suivant {
        bottom: 80px;
        top: 80px;
    }
}

@media (max-width : 1300px) {
    .bloc-creation-suivant {
        width: 70%;
        left: 15%;
    }
}

@media (max-width : 900px) {
    .bloc-creation-suivant {
        width: 80%;
        left: 10%;
    }
}

@media (max-width : 800px) {
    .bloc-creation-suivant {
        width: 85%;
        left: 7.5%;
    }
}

@media (max-width : 700px) {
    .bloc-creation-suivant {
        width: 90%;
        left: 5%;
    }

    .entete>h1 {
        font-size: 20px;
        margin: auto 0;
    }
}

@media (max-width : 600px) {
    .bloc-creation-suivant {
        width: 96%;
        left: 2%;
    }
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