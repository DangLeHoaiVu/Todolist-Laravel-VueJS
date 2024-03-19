<template>
    <v-card>
        <v-layout>
            <v-navigation-drawer class="bg-deep-purple" theme="dark" permanent>
                <h1 class="text-3xl font-extrabold text-white mx-5 my-5">Task Manager</h1>
                <v-list>
                    <router-link :to="{ name: 'home-board' }">
                        <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" value="dashboard">
                        </v-list-item>
                    </router-link>
                    <router-link :to="{ name: 'home-issues' }">
                        <v-list-item prepend-icon="mdi-clipboard-check" title="Issues" value="issues"></v-list-item>
                    </router-link>
                </v-list>

                <template v-slot:append>
                    <div class="pa-2">
                        <v-btn block>
                            Logout
                        </v-btn>
                    </div>
                </template>
            </v-navigation-drawer>
            <v-main class="h-full">
                <Header />
                <div class="mt-20">
                    <router-view></router-view>
                </div>
            </v-main>
        </v-layout>
    </v-card>
</template>

<script>
import Header from "../components/Header.vue";

export default {
    components: {
        Header
    }
}
</script>