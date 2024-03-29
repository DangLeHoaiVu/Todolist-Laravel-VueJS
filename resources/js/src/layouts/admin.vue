<template>
    <Sidebar :data="sidebarData" />
    <router-view></router-view>
</template>

<script>
import Header from "../components/Header.vue";
import Sidebar from "../components/Sidebar.vue";

export default {
    components: {
        Header,
        Sidebar
    },
    data() {
        return {
            sidebarData: {
                title: 'Admin',
                item: [{
                    name: 'admin-users',
                    title: 'Users',
                    icon: 'mdi-view-dashboard'
                }, {
                    name: 'admin-tasks',
                    title: 'Tasks',
                    icon: 'mdi-clipboard-check'
                },
                ]
            }
        }
    }
}
</script>