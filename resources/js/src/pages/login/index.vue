<template>
    <div class="flex flex-col justify-center items-center mt-20">
        <div class="bg-white shadow-2xl p-10 space-y-5 rounded-lg">
            <h1 class="text-center font-bold text-xl">Welcome back!</h1>
            <v-sheet class="mx-auto" width="300">
                <v-form fast-fail ref="form" @submit.prevent="login">
                    <v-text-field v-model="email" :rules="emailRules" label="Email"></v-text-field>

                    <v-text-field v-model="password" :rules="passwordRules" type="password"
                        label="Password"></v-text-field>
                    <p v-if="errorMessage" class="text-red-600">{{ errorMessage }}</p>
                    <v-btn class="mt-2" type="submit" block>Submit</v-btn>
                </v-form>
            </v-sheet>
        </div>
    </div>
</template>

<script>
import authenticated from '../../store/authenticated'

export default {
    data: () => ({
        email: '',
        emailRules: [
            v => !!v || 'Email is required',
            v => (v && v.length <= 20) || 'Email must be less than 20 characters',
        ],
        password: '',
        passwordRules: [
            v => !!v || 'Password is required',
        ],
        errorMessage: '',
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

        async login() {
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/login', {
                    email: this.email,
                    password: this.password
                });
                const data = response.data;

                if (data) {
                    const userData = data.user;
                    const tokenUserData = `${data.token}.user:${JSON.stringify(userData)}`;

                    authenticated.dispatch('login', data.user);
                    localStorage.setItem('accessToken', tokenUserData);
                    if (data.user.role === "ADMIN") {
                        this.$router.push('/admin/users');
                    } else {
                        this.$router.push('/home/board');
                    }
                } else {
                    throw new Error('Token not found in response data');
                }
            } catch (error) {
                console.error('Error logging in:', error);
                if (error.response && error.response.status === 401) {
                    this.errorMessage = 'Wrong email or password.';
                } else {
                    this.errorMessage = 'An error occurred. Please try again later.';
                }
            }
        }
    },
}
</script>