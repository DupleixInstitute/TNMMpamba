<template>
    <div class="flex min-h-screen overflow-hidden">
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover"
                 src="../../Assets/land-bg.png"
                 alt="">
        </div>
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-gradient-to-br from-white to-gray-50">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden">
                        <jet-authentication-card-logo/>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome back</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Sign in to access your account
                    </p>
                </div>

                <div class="mt-8">
                    <div class="mt-6">
                        <jet-validation-errors class="mb-4"/>
                        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                            {{ status }}
                        </div>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <jet-label for="email" value="Email address" class="text-sm font-medium text-gray-700"/>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                    </div>
                                    <jet-input id="email" type="email" class="mt-1 block w-full pl-10 pr-3" v-model="form.email"
                                           required autofocus/>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <jet-label for="password" value="Password" class="text-sm font-medium text-gray-700"/>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <jet-input id="password" type="password" class="mt-1 block w-full pl-10 pr-3"
                                           v-model="form.password" required
                                           autocomplete="current-password"/>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <jet-checkbox name="remember" v-model:checked="form.remember"/>
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>
                                <inertia-link v-if="canResetPassword" :href="route('password.request')"
                                              class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                                    Forgot password?
                                </inertia-link>
                            </div>

                            <div>
                                <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                        class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                                    Sign in
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <teleport to="head">
        <title>{{ pageTitle }}</title>
        <meta property="og:description" :content="pageDescription">
    </teleport>
</template>

<script>
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'

export default {
    components: {
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors
    },

    props: {
        canResetPassword: Boolean,
        status: String
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
            }),
            pageTitle: "Login",
            pageDescription: "Login",
        }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: this.form.remember ? 'on' : ''
                }))
                .post(this.route('login'), {
                    //log some data in console
                    onFinish: () => this.form.reset('password'),
                })
        }
    }
}
</script>
