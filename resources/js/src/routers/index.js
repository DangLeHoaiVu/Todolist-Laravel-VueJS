import { createRouter, createWebHistory } from "vue-router";
import admin from './admin'
import login from "./login";
import home from './home'

const routes = [...login, ...admin, ...home]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router