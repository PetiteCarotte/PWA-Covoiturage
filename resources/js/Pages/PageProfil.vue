<!-- Représente l'interface Profil  -->

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { inject } from 'vue'
import axios from 'axios';

const afficherMessageFunc = inject('afficherMessageFunc'); // Fonction qui gère l'affichage de messages généraux sur App.vue

/* Constantes pour l'affichage des données du profil, TODO : charger depuis la bdd */

const profil = ref({
  prenom: '',
  nom: '',
  unite: '',
  adresse: '',
  mail: '',
  telephone: ''
})

const fetchProfil = async () => {
  try {
    const response = await axios.get('/profil');
    const profilData = response.data['']; // Accès aux données du profil à partir de la clé vide
    profil.value = profilData;

  } catch (error) {
    console.error('Erreur lors de la récupération du profil :', error);
  }
}

onMounted(fetchProfil);

const router = useRouter() // Récupération du router vue-router pour la navigation

/**
 * Amène l'interface de modification des données du profil
 */
const modifier = () => {
  router.push({
    path: '/modification-profil',
    query: {
      prenom: profil.value.Prenom,
      nomFamille: profil.value.Nom,
      unite: profil.value.Unite,
      adressePostale: profil.value.AdressePostale,
      mail: profil.value.Mail,
      telephone: profil.value.Numero_De_Telephone,
      numPoste: profil.value.Numero_De_Poste
    }
  });
}

/**
 * Déconnecte l'utilisateur actuel
 */
const deconnexion = () => {
  axios.post('/logout', {
  })
    .then(response => {
      // Déconnexion réussie
      console.log(response);
      afficherMessageFunc("Déconnexion réussie", "Succès");
      window.location.href = '/';
    })
    // Erreur lors de la déconnexion
    .catch(error => {
      afficherMessageFunc(error.message, "Erreur");
      console.error(error);
    });
}

/**
 * Ouvre l'interface de rapport de bug
 */
const rapportBug = () => {
  router.push({
    path: '/rapport-bug'
  });
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
  <div class="fr-container fr-mt-3w">
    <div class="fr-grid-row fr-grid-row--gutters">
      <div class="fr-col-12 fr-col-md-4">
        <div class="fr-card">
          <div class="fr-card__header">
            <div class="fr-card__title">
              <h3>Profil</h3>
            </div>
          </div>
          <div class="fr-card__body">
            <p><strong>Prénom : </strong>{{ profil.Prenom }}</p>
            <p><strong>Nom : </strong>{{ profil.Nom }}</p>
            <p><strong>Unité : </strong>{{ profil.Unite }}</p>
            <p><strong>Adresse :</strong> {{ profil.AdressePostale }}</p>
            <p><strong>Mail :</strong> {{ profil.Mail }}</p>
            <p><strong>Téléphone :</strong> {{ profil.Numero_De_Telephone }}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
      <div class="fr-col-auto">
        <button class="fr-btn fr-fi-edit-fill fr-btn--icon-left" @click="modifier">
          Modifier
        </button>
      </div>
      <div class="fr-col-auto">
        <button class="fr-btn fr-btn--secondary fr-fi-logout-box-r-line fr-btn--icon-left" @click="deconnexion">
          Déconnexion
        </button>
      </div>
    </div>

    <div class="fr-grid-row fr-grid-row--gutters fr-mt-3w">
      <div class="fr-col-12">
        <span class="fr-icon-arrow-right-up-line" aria-hidden="true" @click="rapportBug">Rapporter un bug / Suggérer une amélioration</span>

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
.fr-grid-row--gutters{
  justify-content: space-between;
}
.fr-container {
  max-width: 1280px;
  margin: 0 auto;
}

.fr-card__body {
  display: flex;
  flex-direction: column;
  align-items: left;
  text-align: left;
}

.profil-photo {
  background: url('assets/icons/default-avatar.png') no-repeat center center;
  background-size: cover;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 1rem;
}

.fr-text--bold {
  font-weight: bold;
}

.fr-btn--icon-left.fr-fi-edit-fill::before {
  margin-right: 0.5rem;
}

.fr-btn--icon-left.fr-fi-logout-box-r-line::before {
  margin-right: 0.5rem;
}

.fr-btn--icon-left.fr-fi-alert-line::before {
  margin-right: 0.5rem;
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