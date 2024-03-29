<template>
    <Sidebar :data="sidebarData" />
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
                title: 'Task Manager',
                item: [{
                    name: 'home-board',
                    title: 'Dashboard',
                    icon: 'mdi-view-dashboard'
                }, {
                    name: 'home-issues',
                    title: 'Issues',
                    icon: 'mdi-clipboard-check'
                },
                ]
            }
        }
    }
}
</script>