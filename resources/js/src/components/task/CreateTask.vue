<template>
    <div>
        <v-dialog v-model="dialog" max-width="600" @click:outside="closeDialog">
            <v-card>
                <template #title>
                    <v-icon left>mdi-account-file</v-icon>
                    <span>Task detail</span>
                </template>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field v-model="createNewTask.title" label="Title" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="createNewTask.description" label="Description" required></v-textarea>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-menu v-model="menuStartDate" :close-on-content-click="false"
                                :return-value.sync="formattedStartDate" transition="scale-transition" offset-y
                                min-width="auto" ref="menuStartDateRef" lazy>
                                <template v-slot:activator="{ on }">
                                    <v-text-field v-model="formattedStartDate" :value="formattedStartDate"
                                        label="Start Date" readonly v-on="on"
                                        @click="openMenu('start_date')"></v-text-field>
                                </template>
                                <v-date-picker v-model="startDateSelect" :value="formattedStartDate"
                                    :max="formattedEndDate || null"></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-menu v-model="menuEndDate" :close-on-content-click="false"
                                :return-value.sync="formattedEndDate" transition="scale-transition" offset-y
                                min-width="auto" ref="menuEndDateRef" lazy>
                                <template v-slot:activator="{ on }">
                                    <v-text-field v-model="formattedEndDate" :value="formattedEndDate" label="End Date"
                                        readonly v-on="on" @click="openMenu('end_date')"></v-text-field>
                                </template>
                                <v-date-picker v-model="endDateSelect" :value="formattedEndDate"
                                    :min="formattedStartDate || null"></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn text="Close" variant="plain" @click="closeDialog"></v-btn>

                    <v-btn color="primary" text="Save" variant="tonal" @click="saveTask(status_id)"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>


<script>
import { ref, watch, computed } from 'vue';

export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false
        },
        statusId: {
            type: BigInt,
            require: true
        }
    },
    setup(props, { emit }) {
        const dialog = ref(props.showDialog);

        const createNewTask = ref({
            title: null,
            description: null,
            start_date: null,
            end_date: null,
            status_id: null
        })

        const menuStartDate = ref(false);
        const menuEndDate = ref(false);
        const menuStartDateRef = ref(null);
        const menuEndDateRef = ref(null);

        const startDateSelect = ref(null)
        const endDateSelect = ref(null)

        watch(() => props.statusId, (newVal) => {
            createNewTask.value.status_id = newVal
        })

        watch(() => props.showDialog, (newVal) => {
            dialog.value = newVal;
        });

        const formattedDate = (date_props) => {
            if (!date_props) return null;
            const date = new Date(date_props);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const formattedStartDate = computed(() => {
            if (startDateSelect.value) {
                return formattedDate(startDateSelect.value);
            }
        });

        const formattedEndDate = computed(() => {
            if (endDateSelect.value) {
                return formattedDate(endDateSelect.value);
            }
        });

        const openMenu = (dateType) => {
            if (dateType === 'start_date' && menuStartDateRef.value) {
                menuStartDate.value = true;
                menuStartDateRef.value.isActive = true;
            } else if (dateType === 'end_date' && menuEndDateRef.value) {
                menuEndDate.value = true;
                menuEndDateRef.value.isActive = true;
            } else {
                console.error('MenuRef is null or undefined');
            }
        };
        const formattedDateToTimestamp = (date_props) => {
            const dateToTimestamp = new Date(date_props);
            dateToTimestamp.setDate(dateToTimestamp.getDate() + 1);
            dateToTimestamp.setHours(0, 0, 0, 0);

            return dateToTimestamp.getTime()
        }

        const saveTask = () => {
            if (createNewTask.value.title && createNewTask.value.description && startDateSelect.value && endDateSelect.value) {
                createNewTask.value.start_date = formattedDateToTimestamp(startDateSelect.value)
                createNewTask.value.end_date = formattedDateToTimestamp(endDateSelect.value)

                emit('create:task', createNewTask);
            }
            closeDialog();
        };

        const closeDialog = () => {
            emit('closeDialog');
            startDateSelect.value = null
            endDateSelect.value = null
        };

        return {
            dialog,
            createNewTask,
            closeDialog,
            saveTask,
            menuStartDate,
            menuEndDate,
            formattedDate,
            formattedStartDate,
            formattedEndDate,
            menuStartDateRef,
            menuEndDateRef,
            openMenu,
            startDateSelect,
            endDateSelect,
        };
    }
};
</script>