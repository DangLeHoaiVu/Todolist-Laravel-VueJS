<template>
    <div>
        <v-dialog v-model="dialog" max-width="600" @click:outside="closeDialog">
            <v-card prepend-icon="mdi-account-file" title="Task detail">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field label="Title" v-model="task_props_value.title" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea label="Description" v-model="task_props_value.description"
                                required></v-textarea>
                        </v-col>

                        <!-- <v-col cols="12" sm="6">
                            <v-select :items="statusOptions" label="Status" v-model="task_props_value.status_name"
                                required></v-select>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-select :items="usersOptions" label="Assign"
                                v-model="task_props_value.user_implement_name"></v-select>
                        </v-col> -->
                        <v-col cols="12" sm="6">
                            <p class="text-sm text-gray-500">Create at: <span class="font-bold">{{
            task_props_value.create_at }}</span></p>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <p class="text-sm text-gray-500">Create by: <span class="font-bold">{{
            task_props_value.user_create_name }}</span></p>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn text="Close" variant="plain" @click="closeDialog"></v-btn>

                    <v-btn color="primary" text="Save" variant="tonal" @click="saveTask"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>


<script>
import { ref, watch } from 'vue';

export default {
    props: {
        task: {
            type: Object,
            required: true
        },
        showDialog: {
            type: Boolean,
            default: false
        }
    },
    setup(props, { emit }) {
        const dialog = ref(props.showDialog);
        const task_props_value = ref(JSON.parse(JSON.stringify(props.task)));
        const usersOptions = ref([]);
        const statusOptions = ref([]);

        watch(() => props.task, (newVal) => {
            task_props_value.value = JSON.parse(JSON.stringify(newVal));
        });

        watch(() => props.showDialog, (newVal) => {
            dialog.value = newVal;
        });

        watch(() => props.users, (newVal) => {
            usersOptions.value = newVal.map(user => ({
                text: user.name,
                value: user.id
            }));
        });

        watch(() => props.statuses, (newVal) => {
            statusOptions.value = newVal.map(status => ({
                text: status.name,
                value: status.id
            }));
        });

        const saveTask = () => {
            emit('update:task', task_props_value);
            closeDialog()
        };

        const closeDialog = () => {
            emit('closeDialog');
        };

        return { task_props_value, dialog, closeDialog, saveTask, usersOptions, statusOptions };
    }
}
</script>