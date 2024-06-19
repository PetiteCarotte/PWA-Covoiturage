import { createApp, h } from 'vue';
import App from './App.vue';
import routerApp from './routesApp';

import '@gouvfr/dsfr/dist/dsfr/dsfr.min.css';
import '@gouvfr/dsfr/dist/utility/icons/icons.min.css';
import '@gouvfr/dsfr/dist/dsfr.css';

// Création de l'application depuis le composant App.vue
const app = createApp({
  render: () => h(App),
});

app.use(routerApp);
app.mount('#app');
