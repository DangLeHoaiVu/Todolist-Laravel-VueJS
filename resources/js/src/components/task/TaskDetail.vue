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
                            <v-text-field label="Title" v-model="task_props_value.title" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea label="Description" v-model="task_props_value.description"
                                required></v-textarea>
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
                                    @input="onStartDateChange" :max="formattedEndDate || null"></v-date-picker>
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
                                    @input="onEndDateChange" :min="formattedStartDate || null"></v-date-picker>
                            </v-menu>
                        </v-col>

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
import { ref, watch, computed } from 'vue';

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

        const menuStartDate = ref(false);
        const menuEndDate = ref(false);
        const menuStartDateRef = ref(null);
        const menuEndDateRef = ref(null);

        const startDate = ref(null);
        const endDate = ref(null);

        const startDateSelect = ref(null)
        const endDateSelect = ref(null)

        watch(() => props.task, (newVal) => {
            task_props_value.value = JSON.parse(JSON.stringify(newVal));
            startDate.value = task_props_value.value.start_date;
            endDate.value = task_props_value.value.end_date;
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
            return formattedDate(startDate.value);
        });

        const formattedEndDate = computed(() => {
            if (endDateSelect.value) {
                return formattedDate(endDateSelect.value);
            }
            return formattedDate(endDate.value);
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

        const onStartDateChange = (newStartDate) => {
            if (endDate.value && newStartDate > endDate.value) {
                console.warn('Start date cannot be after end date. Resetting end date.');
                endDate.value = null;
            }
            startDate.value = newStartDate;
            task_props_value.value.start_date = newStartDate;
            menuStartDate.value = false;
        };

        const onEndDateChange = (newEndDate) => {
            if (startDate.value && newEndDate < startDate.value) {
                console.warn('End date cannot be before start date. Resetting start date.');
                startDate.value = null;
            }
            endDate.value = newEndDate;
            task_props_value.value.end_date = newEndDate;
            menuEndDate.value = false;
        };

        const formattedDateToTimestamp = (date_props) => {
            const dateToTimestamp = new Date(date_props);
            dateToTimestamp.setDate(dateToTimestamp.getDate() + 1);
            dateToTimestamp.setHours(0, 0, 0, 0);

            return dateToTimestamp.getTime()
        }

        const saveTask = () => {
            if (startDateSelect.value && endDateSelect.value) {
                const startDateToServe = new Date(startDateSelect.value);
                startDateToServe.setDate(startDateToServe.getDate() + 1);
                startDateToServe.setHours(0, 0, 0, 0);

                const endDateToServe = new Date(endDateSelect.value);
                endDateToServe.setDate(endDateToServe.getDate() + 1);
                endDateToServe.setHours(0, 0, 0, 0);

                task_props_value.value.start_date = startDateToServe.getTime()
                task_props_value.value.end_date = endDateToServe.getTime()

                startDate.value = formattedDate(startDateSelect.value)
                endDate.value = formattedDate(endDateSelect.value)
                emit('update:task', task_props_value);
            }
            else if (startDateSelect.value) {
                const startDateToServe = new Date(startDateSelect.value);
                startDateToServe.setDate(startDateToServe.getDate() + 1);
                startDateToServe.setHours(0, 0, 0, 0);

                const endDateToServe = new Date(task_props_value.value.end_date);
                endDateToServe.setDate(endDateToServe.getDate() + 1);
                endDateToServe.setHours(0, 0, 0, 0);

                task_props_value.value.start_date = startDateToServe.getTime()
                task_props_value.value.end_date = endDateToServe.getTime()

                startDate.value = formattedDate(startDateSelect.value)
                emit('update:task', task_props_value);
            }
            else if (endDateSelect.value) {
                const endDateToServe = new Date(endDateSelect.value);
                endDateToServe.setDate(endDateToServe.getDate() + 1);
                endDateToServe.setHours(0, 0, 0, 0);

                const startDateToServe = new Date(task_props_value.value.start_date);
                startDateToServe.setDate(startDateToServe.getDate() + 1);
                startDateToServe.setHours(0, 0, 0, 0);

                task_props_value.value.end_date = endDateToServe.getTime()
                task_props_value.value.start_date = startDateToServe.getTime()

                endDate.value = formattedDate(endDateSelect.value)
                emit('update:task', task_props_value);
            } else {
                task_props_value.value.start_date = formattedDateToTimestamp(task_props_value.value.start_date)
                task_props_value.value.end_date = formattedDateToTimestamp(task_props_value.value.end_date)
                emit('update:task', task_props_value);
            }
            closeDialog();
        };

        const closeDialog = () => {
            emit('closeDialog');
            startDateSelect.value = null
            endDateSelect.value = null
        };

        return {
            task_props_value,
            dialog,
            closeDialog,
            saveTask,
            usersOptions,
            statusOptions,
            menuStartDate,
            menuEndDate,
            startDate,
            endDate,
            formattedDate,
            formattedStartDate,
            formattedEndDate,
            menuStartDateRef,
            menuEndDateRef,
            openMenu,
            onStartDateChange,
            onEndDateChange,
            startDateSelect,
            endDateSelect,
        };
    }
};
</script>