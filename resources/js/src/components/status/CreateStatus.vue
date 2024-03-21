<template>
    <div>
        <v-dialog v-model="dialog" max-width="600" @click:outside="closeDialog">
            <v-card>
                <template #title>
                    <span>Create status</span>
                </template>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field label="Status name" v-model="initStatusValue.name" required></v-text-field>
                        </v-col>
                        <v-col cols="12" v-if="statuses">
                            <v-select label="Select" :items="statusOptions" :item-props="itemProps"
                                v-model="initStatusValue.statusSetting"
                                hint="Your can change from new status to this status you choose and opposite"
                                multiple></v-select>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn text="Close" variant="plain" @click="closeDialog"></v-btn>

                    <v-btn color="primary" text="Save" variant="tonal" @click="saveStatus"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false
        },
        statuses: {
            type: Array,
            required: true
        }
    },
    setup(props, { emit }) {
        const dialog = ref(props.showDialog);
        const statuses = ref(props.statuses)

        const initStatusValue = ref({
            name: null,
            statusSetting: []
        })

        watch(() => props.showDialog, (newVal) => {
            dialog.value = newVal;
        });

        watch(() => props.statuses, (newVal) => {
            statuses.value = newVal;
        });

        const statusOptions = computed(() => {
            return props.statuses.map(status => ({ id: status.id, name: status.name }));
        });

        const itemProps = (status) => {
            return {
                title: status.name,
                value: status.id
            }
        }

        const saveStatus = () => {
            emit('create:status', initStatusValue)
            closeDialog()
        }

        const closeDialog = () => {
            emit('closeDialog');
        };



        return {
            dialog,
            statuses,
            statusOptions,
            itemProps,
            closeDialog,
            initStatusValue,
            saveStatus
        }
    }
}
</script>