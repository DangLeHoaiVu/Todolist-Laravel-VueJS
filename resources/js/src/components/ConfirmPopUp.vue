<template>
    <div>
        <div>
            <v-dialog v-model="dialog" max-width="600" @click:outside="closeDialog">
                <v-card>
                    <template #title>
                        <span>{{ confirmData.title }}</span>
                    </template>
                    <v-card-text>
                        <p>{{ confirmData.content }}</p>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn text="Close" variant="plain" @click="closeDialog"></v-btn>

                        <v-btn color="primary" text="Confirm" variant="tonal" @click="confirm"></v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';

export default {
    props: {
        confirmDataProps: {
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
        const confirmData = ref(props.confirmDataProps)

        watch(() => props.showDialog, (newVal) => {
            dialog.value = newVal;
        });

        watch(() => props.confirmDataProps, (newVal) => {
            confirmData.value = newVal;
        });

        const confirm = () => {
            emit('submit:confirm', confirmData.value)
            closeDialog()
        }

        const closeDialog = () => {
            emit('closeDialog');
        };

        return {
            dialog,
            confirmData,
            confirm,
            closeDialog
        }
    }
}

</script>