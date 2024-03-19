<template>
    <div>
        <h2 class="text-2xl font-bold m-5">To do List</h2>

        <TaskDetail :task="task_props_value" :showDialog="isDialogOpen" @update:task="handleUpdateTask"
            @closeDialog="closeDialog" />

        <ul class="grid grid-cols-4 gap-3 mx-3">
            <li v-for="status in statuses" :key="status.id + 'Status'"
                class="col-span-1 min-h-40 bg-gray-200 py-3 px-5 rounded-md" @drop="handleDrop"
                @dragenter.prevent="handleDragEnter(status.id)" @dragover.prevent>
                <div>

                    <p class="text-sm font-bold text-right text-gray-500">{{ status.name.toUpperCase() }}</p>

                    <div class="mt-10 mx-2">
                        <div :class="{ 'bg-gray-400 rounded-full h-1 w-full': sourceStatusId === status.id }"></div>
                        <ul class="space-y-3">
                            <li v-for="(task, index) in filteredTasks(tasks, status.id)" :key="'task-' + index"
                                class="bg-white p-5 rounded-lg" draggable="true"
                                :drag="{ group: 'my-tasks', ghostClass: 'my-drag-ghost' }"
                                @dragstart="handleDragStart(task, status.id)" @drop="handleDrop()">
                                <div class="flex justify-between items-start">
                                    <p class="text-md font-semibold select-none">{{ task.title }}</p>
                                    <button type="button"
                                        class="underline hover:underline-offset-4 text-gray-700 hover:text-purple-600"
                                        variant="tonal" @click="openDialog(task)">
                                        <v-icon icon="mdi-pencil"></v-icon>
                                    </button>
                                </div>
                                <p class="text-md select-none">{{ task.description }}</p>
                                <div class="ml-5 flex justify-between items-center mt-3">
                                    <p class="text-sm font-semibold text-gray-500 select-none">Task - {{ task.id }}</p>
                                    <div class="text-center">
                                        <v-menu>
                                            <template v-slot:activator="{ props: menu }">
                                                <v-tooltip location="right">
                                                    <span v-tooltip:hover.title="task.user_implement_name">
                                                        {{ task.user_implement_name || 'No assign' }}
                                                    </span>
                                                    <template v-slot:activator="{ props: tooltip }">
                                                        <button v-bind="mergeProps(menu, tooltip)">
                                                            <img :src="getTaskAvatar(task)" alt="avatar"
                                                                :class="{ 'default-avatar': !task.user_implement_avatar_url } + 'max-h-6 max-w-6 p-1 rounded-full bg-gray-400 select-none'" />
                                                        </button>
                                                    </template>
                                                </v-tooltip>
                                            </template>
                                            <v-list class="max-h-52 overflow-y-auto">
                                                <v-list-item v-for="user in filteredUsers(users)"
                                                    :key="user.id + 'user'" class="cursor-pointer hover:bg-gray-200"
                                                    @click="handleChangeUserAssign(task, user)">
                                                    <v-list-item-title>
                                                        {{ user.name }}
                                                    </v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { ref, mergeProps } from 'vue'
import draggable from "vuedraggable";
import TaskDetail from '../../../components/task/TaskDetail.vue';

export default {
    setup() {
        const statuses = ref([])
        const tasks = ref([])
        const users = ref([])

        const task_props_value = ref(null);
        const isDialogOpen = ref(false);

        const enabled = true
        const dragging = ref(false)
        const draggedTask = ref(null);
        const sourceStatusId = ref(null);

        const getStatus = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/status');
                statuses.value = response.data
            } catch (error) {
                console.error('Error fetching status:', error);
            }
        };

        const getTasks = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/task');
                tasks.value = response.data
            } catch (error) {
                console.error('Error fetching tasks:', error);
            }
        }

        const getUsers = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/users');
                users.value = response.data
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        }

        getStatus();
        getTasks();
        getUsers();

        const filteredTasks = (tasks, statusId) => {
            return tasks ? tasks.filter((task) => task.status_id === statusId) : [];
        };

        const getTaskAvatar = (task) => {
            if (task.avatar_url && validateUrl(task.avatar_url)) {
                return task.avatar_url;
            } else {
                return "https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/User-avatar.svg/2048px-User-avatar.svg.png";
            }
        };

        const filteredUsers = (users) => {
            return users ? users.filter((user) => user.role !== "ADMIN") : [];
        };

        const handleChangeUserAssign = async (task, user) => {
            if (task.implementer_id !== user.id) {
                try {
                    const response = await axios.put(`http://127.0.0.1:8000/api/task/${task.id}/assign`, {
                        implementer_id: user.id,
                    });
                    const userAssignIndex = tasks.value.findIndex((f_task) => f_task.id === task.id);
                    if (userAssignIndex !== -1 && response.status === 200) {
                        tasks.value[userAssignIndex].implementer_id = user.id;
                        tasks.value[userAssignIndex].user_implement_avatar_url = user.avatar_url;
                        tasks.value[userAssignIndex].user_implement_name = user.name;
                    }
                } catch (error) {
                    console.error('Error updating user assign task:', error);
                }
            }
        };

        const handleDragStart = (task, statusId) => {
            draggedTask.value = task;
            sourceStatusId.value = statusId;
        };

        const handleDragEnter = (statusId) => {
            sourceStatusId.value = statusId;
        };

        const handleDrop = async () => {
            if (draggedTask.value.id !== null) {
                try {
                    const response = await axios.put(`http://127.0.0.1:8000/api/task/${draggedTask.value.id}/status`, {
                        status_id: sourceStatusId.value,
                    });
                    const taskIndex = tasks.value.findIndex((task) => task.id === draggedTask.value.id);
                    if (taskIndex !== -1 && response.status === 200) {
                        tasks.value[taskIndex].status_id = sourceStatusId.value;
                    }
                } catch (error) {
                    console.error('Error updating task status:', error);
                }
            }
            draggedTask.value = null;
            sourceStatusId.value = null
        };

        const openDialog = (task_props) => {
            task_props_value.value = task_props
            isDialogOpen.value = true;
        };

        const closeDialog = () => {
            isDialogOpen.value = false;
        };

        const handleUpdateTask = async (newTaskValue) => {
            try {
                if (task_props_value.value !== newTaskValue.value) {
                    const response = await axios.put(`http://127.0.0.1:8000/api/task/${newTaskValue.value.id}/updateDetail`, {
                        title: newTaskValue.value.title,
                        description: newTaskValue.value.description,
                    });
                    const taskIndex = tasks.value.findIndex((task) => task.id === newTaskValue.value.id);
                    if (taskIndex !== -1 && response.status === 200) {
                        tasks.value[taskIndex] = newTaskValue.value;
                    }
                }
            } catch (error) {
                console.error('Error updating task status:', error);
            }
        }

        return {
            statuses,
            tasks,
            users,
            enabled,
            dragging,
            filteredTasks,
            getTaskAvatar,
            filteredUsers,
            handleChangeUserAssign,
            handleDragStart,
            handleDragEnter,
            handleDrop,
            mergeProps,
            sourceStatusId,
            task_props_value,
            openDialog,
            isDialogOpen,
            closeDialog,
            handleUpdateTask
        }
    },
    components: {
        draggable,
        TaskDetail,
    },
};
</script>
