<!-- Représente l'interface de connexion  -->

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { inject } from 'vue'
import axios from 'axios';

const identifiant = ref('')
const motdepasse = ref('')

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

/**
 * Gère la connexion à partir des identifiants / mot de passe renseignés
 */
const connexion = () => {
    // On appelle le controller de connexion
    axios.post('/login', {
        Mail: identifiant.value,
        Mot_De_Passe: motdepasse.value,
    })
        .then(response => {
            // Tout c'est bien passé, on est emmené sur l'application
            console.log(response);
            window.location.href = '/app';
        })
        .catch(error => {
            // Une erreur est survenue
            if (error.response.status === 422) { // Erreur 422 = erreurs dans les identifiants / mot de passe
                afficherMessageFunc("Email ou mot de passe incorrect.", "Erreur");
            } else {
                afficherMessageFunc("Une erreur s'est produite lors de la tentative de connexion.", "Erreur");
            }
        });
}

const router = useRouter() // Récupération du router vue-router pour la navigation

/**
 * Emmène sur l'interface de connexion
 */
const inscription = () => {
    router.push({
        path: '/inscription-page1',
        query: {
            mail: "",
            nid: ""
        }

    });
}

/**
 * Emmène sur l'interface de mot de passe oublié
 */
const motdepasseoublie = () => {
    router.push({
        path: '/mot-de-passe-oublie'

    });
}


</script>

<template>
    <div class="bloc-principal">

        <div class="fr-container">
            <h1 class="fr-h1">Connexion</h1>
            <div class="fr-grid-row fr-grid-row--center fr-mb-3w">
                <div class="fr-col-12 fr-col-md-6">
                    <div class="fr-input-group">
                        <label for="identifiant" class="fr-label">Mail ou NID</label>
                        <input type="text" id="identifiant" class="fr-input" v-model="identifiant"
                            placeholder="Mail ou NID" />
                    </div>
                    <div class="fr-input-group fr-mt-2w">
                        <label for="motdepasse" class="fr-label">Mot de Passe</label>
                        <input type="password" id="motdepasse" class="fr-input" v-model="motdepasse"
                            placeholder="Mot de Passe" />
                    </div>
                    <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
                        <div class="fr-col">
                            <button class="fr-btn" @click="connexion">Connexion</button>
                        </div>
                        <div class="fr-col sub">
                            <button class="fr-btn fr-btn--secondary" @click="inscription">S'inscrire</button>
                        </div>
                    </div>
                    <p class="fr-mt-3w">
                        <a @click="motdepasseoublie" class="fr-link">Mot de passe oublié ?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.bloc-label {
    display: flex;
    flex-direction: row;
    height: 50px;
    width: 75%;
    margin-left: 12.5%;
    justify-content: space-between;
    margin-bottom: 10px;
}

input {
    width: 90%;
    font-size: medium;
    border: none;
    border-bottom: 1px solid #dddddd;
}

#icone {
    background-size: 30px 30px;
    background-repeat: no-repeat;
    background-position: center;
    width: 50px;
    height: 50px;
}

.icone-mail {
    background: url('assets/icons/connexion-arobase.png');
}

.icone-cadenas {
    background: url('assets/icons/connexion-cadenas.png');
}

h1 {
    margin-top: 2%;
    margin-bottom: 4%;
}

p,
h1 {
    text-align: center;
    border-top: 5px;
    color: black;
}

.boutons {
    display: flex;
    margin-left: 10%;
    margin-top: 20px;
    justify-content: space-between;
}

.connexion,
.inscription {
    background-color: #bbbbbb;
    width: 120px;
    height: 35px;
    margin: auto;
    border-radius: 10px;
}

.lien-mot-de-passe-oublie {
    border-top: auto;
    justify-content: right;
}
</style>
