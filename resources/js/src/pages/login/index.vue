<template>
    <div class="flex flex-col justify-center items-center mt-20">
        <div class="bg-white shadow-2xl p-10 space-y-5 rounded-lg">
            <h1 class="text-center font-bold text-xl">Welcome back!</h1>
            <v-sheet class="mx-auto" width="300">
                <v-form fast-fail @submit.prevent>
                    <v-text-field v-model="username" :rules="usernameRules" label="Username"></v-text-field>

                    <v-text-field v-model="password" :rules="passwordRules" type="password"
                        label="Password"></v-text-field>

                    <v-btn class="mt-2" type="submit" block>Submit</v-btn>
                </v-form>
            </v-sheet>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        username: '',
        usernameRules: [
            v => !!v || 'Username is required',
            v => (v && v.length <= 20) || 'Username must be less than 20 characters',
        ],
        password: '',
        passwordRules: [
            v => !!v || 'Password is required',
        ],
    }),

    methods: {
        async validate() {
            const { valid } = await this.$refs.form.validate()

            if (valid) alert('Form is valid')
        },
        reset() {
            this.$refs.form.reset()
        },
        resetValidation() {
            this.$refs.form.resetValidation()
        },
    },
}
</script>