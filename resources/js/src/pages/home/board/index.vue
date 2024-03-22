<template>
    <div class="mb-10">
        <div class="flex justify-between mr-10">
            <h2 class="text-2xl font-bold m-5">To do List</h2>
            <button type="button"
                class="w-42 my-5 p-2 rounded-lg flex items-center justify-center text-gray-700 hover:bg-gray-300"
                @click="openCreateStatusDialog()">
                <v-icon icon="mdi-plus"></v-icon> Create status</button>
        </div>

        <template>
            <div class="text-center">
                <v-snackbar v-model="snakeBarValue.snakeBar" :timeout="snakeBarValue.timeout">
                    {{ snakeBarValue.text }}
                    <template v-slot:actions>
                        <v-btn color="red" variant="text" @click="snakeBarValue.snakeBar = false">
                            Close
                        </v-btn>
                    </template>
                </v-snackbar>
            </div>
        </template>

        <CreateStatus :showDialog="isDialogCreateStatusOpen" :statuses="statuses" @create:status="handleCreateStatus"
            @closeDialog="closeCreateStatusDialog" />
        <CreateTask :showDialog="isDialogCreateOpen" :statusId="statusIdProps" @create:task="handleCreateTask"
            @closeDialog="closeCreateDialog" />
        <TaskDetail :task="task_props_value" :showDialog="isDialogOpen" @update:task="handleUpdateTask"
            @closeDialog="closeDialog" />
        <ConfirmPopUp :showDialog="isDialogConfirmOpen" :confirmDataProps="confirmDataProps"
            @submit:confirm="handleActionStatus" @closeDialog="closeConfirmDialog" />

        <ul class=" grid grid-cols-4 gap-3 mx-3">
            <li v-for="status in statuses" :key="status.id + 'Status'"
                class="col-span-1 min-h-40 bg-gray-200 py-3 px-5 rounded-md" @drop="handleDrop"
                @dragenter.prevent="handleDragEnter(status.id)" @dragover.prevent>
                <div>

                    <div class="flex justify-between items-center">
                        <v-menu :location="end">
                            <template v-slot:activator="{ props }">
                                <button type="button" v-bind="props" class="p-2 rounded-lg group hover:bg-gray-300">
                                    <v-icon icon="mdi-dots-horizontal"></v-icon>
                                </button>
                            </template>
                            <v-list class="max-h-52 overflow-y-auto">
                                <v-list-item v-for="item in items" :key="item.value"
                                    class="cursor-pointer hover:bg-gray-200"
                                    @click="openConfirmDialog(item.value, status.id)">
                                    <v-list-item-title>
                                        {{ item.title }}
                                    </v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <p class="text-sm font-bold text-gray-500">{{ status.name.toUpperCase() }}</p>
                    </div>

                    <div class="mt-10 mx-2 max-h-[500px] overflow-y-auto">
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
                                <div class="flex justify-between items-center mt-3">
                                    <p class="text-sm font-semibold text-gray-500 select-none">{{
                    formattedDate(task.start_date) }} - {{ formattedDate(task.end_date) }}</p>
                                </div>
                                <div class="ml-5 flex justify-between items-center mt-3">
                                    <p class="text-xs font-semibold text-gray-400 select-none">Task - {{ task.id }}</p>
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
                <button type="button"
                    class="w-full mt-5 p-2 rounded-lg flex items-center justify-center text-gray-700 hover:bg-gray-300"
                    variant="tonal" @click="openCreateDialog(status.id)">
                    Create task
                    <v-icon icon="mdi-plus"></v-icon>
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
import { ref, computed, mergeProps } from 'vue'
import draggable from "vuedraggable";
import CreateStatus from '../../../components/status/CreateStatus.vue';
import TaskDetail from '../../../components/task/TaskDetail.vue';
import CreateTask from '../../../components/task/CreateTask.vue';
import ConfirmPopUp from '../../../components/ConfirmPopUp.vue';
import axios from 'axios';
import { useStore } from 'vuex';

export default {
    data: () => ({
        items: [
            { title: 'Delete', value: 'DELETE_STATUS' },
        ],
    }),
    setup() {

        const store = useStore();
        const user = computed(() => store.state.user);

        const statuses = ref([])
        const tasks = ref([])
        const users = ref([])
        const statusSettings = ref([])

        const isDialogCreateStatusOpen = ref(false);

        const isDialogCreateOpen = ref(false);
        const statusIdProps = ref(null);

        const task_props_value = ref(null);
        const isDialogOpen = ref(false);

        const enabled = true
        const dragging = ref(false)
        const draggedTask = ref(null);
        const sourceStatusId = ref(null);

        const isDialogConfirmOpen = ref(false);
        const confirmDataProps = ref({
            title: '',
            content: '',
            action: '',
            data: null
        })

        const snakeBarValue = ref({
            text: '',
            snakeBar: false,
            timeout: 2000,
        })

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

        const getStatusSettings = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/status_settings');
                statusSettings.value = response.data
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        }

        getStatus();
        getTasks();
        getUsers();
        getStatusSettings();

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

        const formattedDate = (date_props) => {
            if (!date_props) return null;
            const date = new Date(date_props);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const filteredUsers = (users) => {
            return users ? users.filter((user) => user.role !== "ADMIN") : [];
        };

        const handleChangeUserAssign = async (task, user) => {
            if (task.implementer_id !== user.id) {
                const today = new Date().getTime()
                try {
                    const response = await axios.put(`http://127.0.0.1:8000/api/task/${task.id}/assign`, {
                        implementer_id: user.id,
                        update_at: today,
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
            const isStatusMatch = statusSettings.value
                .some(statusSetting => {
                    return (draggedTask.value.status_id == statusSetting.current_status &&
                        sourceStatusId.value == statusSetting.next_status) ||
                        (sourceStatusId.value == statusSetting.current_status &&
                            draggedTask.value.status_id == statusSetting.next_status);
                })

            if (!isStatusMatch) {
                const statusSettingFlow = statusSettings.value.map(statusSetting => {
                    if (draggedTask.value.status_id == statusSetting.current_status) {
                        return `${draggedTask.value.status_name} -> ${statusSetting.next_status_name}`;
                    }
                    return null
                }).filter(value => value !== null);

                snakeBarValue.value.text = `Status can follow this flow [ ${statusSettingFlow.join(' || ')} ] and oppsite`;
                snakeBarValue.value.snakeBar = true;
                snakeBarValue.value.timeout = 30000;
            } else {
                if (draggedTask.value.id !== null) {
                    const today = new Date().getTime()
                    try {
                        const response = await axios.put(`http://127.0.0.1:8000/api/task/${draggedTask.value.id}/status`, {
                            status_id: sourceStatusId.value,
                            update_at: today,
                        });
                        const taskIndex = tasks.value.findIndex((task) => task.id === draggedTask.value.id);
                        if (taskIndex !== -1 && response.status === 200) {
                            // tasks.value[taskIndex].status_id = sourceStatusId.value;
                            await getTasks()
                        }
                    } catch (error) {
                        console.error('Error updating task status:', error);
                    }
                }
            }
            draggedTask.value = null;
            sourceStatusId.value = null

        };

        const openConfirmDialog = (action, payload) => {
            confirmDataProps.value.title = 'Delete status'
            confirmDataProps.value.content = `Do you want to delete ${payload.name}`
            confirmDataProps.value.action = action
            confirmDataProps.value.data = payload
            isDialogConfirmOpen.value = true
        }

        const closeConfirmDialog = () => {
            isDialogConfirmOpen.value = false
        }

        const openCreateStatusDialog = () => {
            isDialogCreateStatusOpen.value = true
        }

        const closeCreateStatusDialog = () => {
            isDialogCreateStatusOpen.value = false
        }

        const openCreateDialog = (status_id) => {
            statusIdProps.value = status_id
            isDialogCreateOpen.value = true
        }

        const openDialog = (task_props) => {
            task_props_value.value = task_props
            isDialogOpen.value = true;
        };

        const closeCreateDialog = () => {
            isDialogCreateOpen.value = false
        }

        const closeDialog = () => {
            isDialogOpen.value = false;
        };

        const handleActionStatus = async (payload) => {
            switch (payload.action) {
                case "DELETE_STATUS":
                    try {
                        const response = await axios.delete(`http://127.0.0.1:8000/api/status/${payload.data}/deleteStatus`)
                        if (response.status === 200) {
                            statuses.value = statuses.value.filter(status => status.id !== payload.data)
                        }

                    } catch (error) {
                        console.error('Error creating task status:', error);
                        snakeBarValue.value.text = error.response.data.message
                        snakeBarValue.value.snakeBar = true
                        snakeBarValue.value.timeout = 5000
                    }
                default: {
                    console.log(payload.action);
                }
            }
        }

        const handleCreateStatus = async (newStatusValue) => {
            try {
                const responseCreateStatus = await axios.post('http://127.0.0.1:8000/api/status/createStatus', {
                    name: newStatusValue.value.name,
                    create_by: user.value.id,
                })
                if (responseCreateStatus.status === 200) {
                    const responseCreateStatusSetting = await axios.post('http://127.0.0.1:8000/api/status_setting/createStatusSetting', {
                        create_by: user.value.id,
                        current_status: responseCreateStatus.data.data,
                        next_status: newStatusValue.value.statusSetting
                    })

                    if (responseCreateStatusSetting.status === 200) {
                        // statuses.value.push({
                        //     id: responseCreateStatus.data.data,
                        //     name: newStatusValue.value.name,
                        //     create_by: 2,
                        // })
                        await getStatus();
                        await getStatusSettings();
                    }
                }
            } catch (error) {
                console.error('Error creating task status:', error);
            }
        }

        const handleCreateTask = async (newTaskValue) => {
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/task/createTask', {
                    title: newTaskValue.value.title,
                    description: newTaskValue.value.description,
                    status_id: newTaskValue.value.status_id,
                    create_by: user.value.id,
                    start_date: newTaskValue.value.start_date,
                    end_date: newTaskValue.value.end_date,
                })
                if (response.status == 200) {
                    // tasks.value.push(newTaskValue.value)
                    await getTasks()
                }
            } catch (error) {
                console.error('Error creating task status:', error);
            }
        }

        const handleUpdateTask = async (newTaskValue) => {
            try {
                if (task_props_value.value !== newTaskValue.value) {
                    const today = new Date().getTime()
                    const response = await axios.put(`http://127.0.0.1:8000/api/task/${newTaskValue.value.id}/updateDetailTask`, {
                        title: newTaskValue.value.title,
                        description: newTaskValue.value.description,
                        start_date: newTaskValue.value.start_date,
                        end_date: newTaskValue.value.end_date,
                        update_at: today,
                    });
                    const taskIndex = tasks.value.findIndex((task) => task.id === newTaskValue.value.id);
                    if (taskIndex !== -1 && response.status === 200) {
                        const set_new_start_date = new Date(newTaskValue.value.start_date)
                        set_new_start_date.setDate(set_new_start_date.getDate() - 1)
                        newTaskValue.value.start_date = set_new_start_date

                        const set_new_end_date = new Date(newTaskValue.value.end_date)
                        set_new_end_date.setDate(set_new_end_date.getDate() - 1)
                        newTaskValue.value.end_date = set_new_end_date

                        tasks.value[taskIndex] = newTaskValue.value;
                    }
                }
            } catch (error) {
                console.error('Error updating task:', error);
            }
        }

        return {
            statuses,
            tasks,
            users,
            statusSettings,
            snakeBarValue,
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
            handleActionStatus,
            sourceStatusId,
            task_props_value,
            isDialogCreateStatusOpen,
            openCreateStatusDialog,
            closeCreateStatusDialog,
            handleCreateStatus,
            openDialog,
            openCreateDialog,
            statusIdProps,
            confirmDataProps,
            isDialogConfirmOpen,
            openConfirmDialog,
            closeConfirmDialog,
            isDialogCreateOpen,
            closeCreateDialog,
            isDialogOpen,
            closeDialog,
            handleCreateTask,
            handleUpdateTask,
            formattedDate
        }
    },
    components: {
        draggable,
        CreateStatus,
        TaskDetail,
        CreateTask,
        ConfirmPopUp
    },
};
</script>
