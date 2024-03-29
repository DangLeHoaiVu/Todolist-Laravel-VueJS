<template>
    <v-card>
        <v-layout>
            <v-navigation-drawer class="bg-deep-purple" theme="dark" permanent>
                <h1 class="text-3xl font-extrabold text-white mx-5 my-5">{{ data.title }}</h1>
                <v-list>

                    <router-link v-for="(item, index) in data.item" :key="index" :to="{ name: item.name }">
                        <v-list-item :value="item.title.toLowerCase()">
                            <v-icon>{{ item.icon }}</v-icon>
                            {{ item.title }}
                        </v-list-item>
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
import { ref, watch } from 'vue';
import Header from "../components/Header.vue";

export default {
    props: {
        data: {
            type: Object,
            required: true,
            default: { title: '', item: [{ name: '', title: '', icon: '' }] }
        },
    },
    setup(props) {
        const data = ref(props.data)

        watch(() => props.data, newVal => {
            data.value = newVal
        })

        return {
            data
        }
    }, components: {
        Header
    },
}
</script>