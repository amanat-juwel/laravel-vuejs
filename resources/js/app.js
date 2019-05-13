
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Moment Js
 */
import moment from 'moment';
/**
 * V Form
 */
// Declaring global form messages
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

/**
 * Vue Filter
 */
Vue.filter('upText', function(text){
  return text.charAt(0).toUpperCase() + text.slice(1)
});

Vue.filter('myDate',function(created){
  return moment(created).format('MMMM Do YYYY');
});
/**
 * Vue Progress Bar
 */
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
  })

/**
 * Sweet Alert
 */
import swal from 'sweetalert2'
window.Swal = swal;

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});
window.Toast = Toast;

/**
 * Global Event Handler
 */
window.Event = new Vue();

/**
 * Vue Router
 */
import VueRouter from 'vue-router'
Vue.use(VueRouter)

/**
 * Vue Gate for authentication , window user gate is also added on master layout
 */
import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);



/**
 * Laravel Passport Components
*/
Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue').default
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
  'not-found',
  require('./components/NotFound.vue').default
);

/**
 * Laravel Vue Pagination
*/
Vue.component('pagination', require('laravel-vue-pagination'));

// Define some routes and route components( without .default() will give an error )
let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default },
    { path: '/tasks', component: require('./components/Task/TaskManagement.vue').default },
    { path: '/users', component: require('./components/Users.vue').default },
    { path: '/profile', component: require('./components/Profile.vue').default },
    { path: '/categories', component: require('./components/Blog/Category.vue').default },
    { path: '/posts', component: require('./components/Blog/Post.vue').default },
    { path: '/order', component: require('./components/Order.vue').default },
    { path: '*', component: require('./components/NotFound.vue').default }
  ]

//Create the router instance and pass the `routes` option
const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
  })
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('task-form', require('./components/Task/TaskForm.vue').default);
Vue.component('task-list', require('./components/Task/TaskList.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// add router in app instance
const app = new Vue({
    router,
    data:{
        //search: ''
    },
    methods:{
        // searchit: _.debounce(() => {
        //     Event.$emit('searching');
        // },1000) // after 1 sec
    }
  }).$mount('#app')
