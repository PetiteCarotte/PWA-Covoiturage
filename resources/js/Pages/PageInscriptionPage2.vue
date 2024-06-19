<!-- Représente l'affichage de la seconde page d'inscription  -->

<script setup>
    import { ref } from 'vue'
    import { useRouter } from 'vue-router'
    import { inject } from 'vue'
    import axios from 'axios';

    const props = defineProps({
        mail: String,
        nid: String,
        mdp: String
    })

    /* Constante pour les données saisies */
    const mail = ref(props.mail)
    const nid = ref(props.nid)
    const mdp = ref(props.mdp)

    const unite = ref('')
    const numPoste = ref('')
    const prenom = ref('')
    const nomFamille = ref('')
    const adressePostale = ref('')
    const telephone = ref('')

    const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App_Connexion.vue

    const routerV = useRouter() // Récupération du router vue-router pour la navigation

    /**
     * Inscription à partir des données saisies
     */
    const continuer = () => {
        axios.post('/utilisateur', {
                NID: nid.value,
                Nom: nomFamille.value,
                Prenom: prenom.value,
                Unite: unite.value,
                Numero_De_Poste: numPoste.value,
                Numero_De_Telephone: telephone.value,
                Mail: mail.value,
                Mot_De_Passe: mdp.value,
        })
        .then(response => {
            console.log(response);
            afficherMessageFunc("La demande d'inscription a été enregistrée avec succèes", "Succès");
            routerV.push({
                path: '/'

            });
        })
        .catch(error => {
            afficherMessageFunc(error, "Erreur");
            console.error(error);
        });
    }

    /**
     * Ramène vers la première page (y affiche les données mail et nid qui avaient été validées)
     */
    const annuler = () => {
        routerV.push({
            path: '/inscription-page1',
            query: {
                mail: mail.value,
                nid: nid.value
            }

        });
    }
</script>

<template>
  <div class="bloc-principal">
    <div class="fr-container">
    <h1 class="fr-h1">Inscription</h1>
    <div class="fr-grid-row fr-grid-row--center fr-mb-3w">
      <div class="fr-col-12 fr-col-md-6">
        <div class="fr-input-group">
          <label for="unite" class="fr-label">Unité</label>
          <input type="text" id="unite" class="fr-input" v-model="unite" placeholder="Unité" />
        </div>
        <div class="fr-input-group fr-mt-2w">
          <label for="numPoste" class="fr-label">N° de Poste</label>
          <input type="text" id="numPoste" class="fr-input" v-model="numPoste" placeholder="N° de Poste" />
        </div>
        <div class="fr-input-group fr-mt-2w">
          <label for="prenom" class="fr-label">Prénom</label>
          <input type="text" id="prenom" class="fr-input" v-model="prenom" placeholder="Prénom" />
        </div>
        <div class="fr-input-group fr-mt-2w">
          <label for="nomFamille" class="fr-label">Nom de Famille</label>
          <input type="text" id="nomFamille" class="fr-input" v-model="nomFamille" placeholder="Nom de Famille" />
        </div>
        <div class="fr-input-group fr-mt-2w">
          <label for="adressePostale" class="fr-label">Adresse Postale</label>
          <input type="text" id="adressePostale" class="fr-input" v-model="adressePostale" placeholder="Adresse Postale" />
        </div>
        <div class="fr-input-group fr-mt-2w">
          <label for="telephone" class="fr-label">Téléphone</label>
          <input type="text" id="telephone" class="fr-input" v-model="telephone" placeholder="Téléphone" />
        </div>
        <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
          <div class="fr-col">
            <button class="fr-btn fr-btn--secondary" @click="annuler">Annuler</button>
          </div>
          <div class="fr-col">
            <button class="fr-btn" @click="continuer">Continuer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<style scoped>
    .bloc-label {
        display: flex;
        flex-direction: row;
        height : 50px;
        width: 75%;
        margin-left : 12.5%;
        justify-content: space-between;
        margin-bottom : 10px;
    }

    input {
        width : 90%;
        font-size: medium;
        border : none;
        border-bottom: 1px solid #dddddd;
    }

    h1 {
        margin-bottom : 2%;
        margin-top : 2%;
    }

    p, h1 {
        text-align: center;
        border-top: 5px;
        color: black;
    }

    .boutons {
        display: flex;
        margin-left : 10%;
        margin-top : 20px;
        justify-content: space-between;
    }

    .annuler, .continuer {
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


