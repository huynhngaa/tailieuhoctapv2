import { createApp } from 'vue';

import App from './App.vue';
import router from './router';
import { setupStore } from './store';
import 'dayjs/locale/vi'
import '@/styles/index.scss';
import SvgIcon from './icons'; // icon

import './permission'; // permission control
import vPermission from './directive/permission/index'; // permission control
import { checkEnableLogs } from './utils/error-log'; // error log
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faUserSecret } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(faUserSecret)

const app = createApp(App);
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
  }
setupStore(app);
app.use(router);
app.component('font-awesome-icon', FontAwesomeIcon);
app.component('svg-icon', SvgIcon);
app.directive('permission', vPermission);
checkEnableLogs(app);
app.mount('#app');
