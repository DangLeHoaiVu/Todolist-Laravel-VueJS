<template>
    <div>
        <div class="flex justify-between items-start">
            <p class="text-md font-semibold select-none">{{ task.title }}</p>
            <button><v-icon icon="mdi-pencil"></v-icon></button>
        </div>
        <p class="text-md select-none">{{ task.description }}</p>
        <div class="ml-5 flex justify-between items-center mt-3">
            <p class="text-sm font-semibold text-gray-500 select-none">Task - {{ task.id
                }}
            </p>
            <div class="text-center">
                <v-menu>
                    <template v-slot:activator="{ props: menu }">
                        <v-tooltip location="right">
                            <span v-tooltip:hover.title="task.user_name">
                                {{ task.user_name || 'No assign' }}
                            </span>
                            <template v-slot:activator="{ props: tooltip }">
                                <button v-bind="mergeProps(menu, tooltip)">
                                    <img :src="getTaskAvatar(task)" alt="avatar"
                                        :class="{ 'default-avatar': !task.avatar_url } + 'max-h-6 max-w-6 p-1 rounded-full bg-gray-400 select-none'" />

                                </button>
                            </template>
                        </v-tooltip>
                    </template>
                    <v-list class="max-h-52 overflow-y-auto">
                        <v-list-item v-for="user in filteredUsers(users)" :key="user.id + 'user'"
                            class="cursor-pointer hover:bg-gray-200" @click="handleClick(user)">
                            <v-list-item-title>
                                {{ user.name }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </div>
    </div>
</template>

<script>
const props = {
    name: "raw-displayer",
    title: {
        required: true,
        type: String
    },
    value: {
        required: true
    }
};

console.log(JSON.stringify(this.value._rawValue, null, 2));

export default {
    props,
    computed: {
        valueString() {
            return JSON.stringify(this.value, null, 2);
        }
    }
};
</script>