import { createRouter, createWebHistory } from "vue-router";
import admin from './admin'
import login from "./login";
import home from './home'
import authenticated from "../store/authenticated";

const routes = [...login, ...admin, ...home]

const router = createRouter({
    history: createWebHistory(),
    routes
})
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('hashedTokenUserData');


    if (!token && to.path === '/login') {
        next();
    } else if (!token) {
        next('/login');
    } else {
        const userDataString = token.split('.user:')[1];
        if (userDataString) {
            const userData = JSON.parse(userDataString || {})
            authenticated.dispatch('login', userData);
            next();
        } else {
            next('/login');
        }
    }

})

export default router