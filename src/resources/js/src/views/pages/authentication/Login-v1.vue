<template>
    <div class="auth-wrapper auth-v1 px-2">
        <div class="auth-inner py-2">

            <!-- Login v1 -->
            <b-card class="mb-0">
                <b-link class="brand-logo">
                    <vuexy-logo/>
                    <h2 class="brand-text text-primary ml-1">
                        Hub
                    </h2>
                </b-link>

                <b-card-title class="mb-1">
                    Volunteer Hub 👋
                </b-card-title>
                <b-card-text class="mb-2">
                    Please sign-in to your account!
                </b-card-text>

                <!-- form -->
                <validation-observer
                    ref="loginForm"
                    #default="{invalid}"
                >
                    <b-form
                        class="auth-login-form mt-2"
                        @submit.prevent="login"
                    >
                        <!-- email -->
                        <b-form-group
                            label="Email"
                            label-for="login-email"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Email"
                                vid="email"
                                rules="required"
                            >
                                <b-form-input
                                    id="login-email"
                                    v-model="username"
                                    :state="errors.length > 0 ? false:null"
                                    name="login-email"
                                    placeholder="john@example.com"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>

                        <!-- forgot password -->
                        <b-form-group>
                            <div class="d-flex justify-content-between">
                                <label for="login-password">Password</label>
                                <b-link :to="{name:'auth-forgot-password'}">
                                    <small>Forgot Password?</small>
                                </b-link>
                            </div>
                            <validation-provider
                                #default="{ errors }"
                                name="Password"
                                vid="password"
                                rules="required"
                            >
                                <b-input-group
                                    class="input-group-merge"
                                    :class="errors.length > 0 ? 'is-invalid':null"
                                >
                                    <b-form-input
                                        id="login-password"
                                        v-model="password"
                                        :state="errors.length > 0 ? false:null"
                                        class="form-control-merge"
                                        :type="passwordFieldType"
                                        name="login-password"
                                        placeholder="Password"
                                    />
                                    <b-input-group-append is-text>
                                        <feather-icon
                                            class="cursor-pointer"
                                            :icon="passwordToggleIcon"
                                            @click="togglePasswordVisibility"
                                        />
                                    </b-input-group-append>
                                </b-input-group>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>

                        <!-- checkbox -->
                        <b-form-group>
                            <b-form-checkbox
                                id="remember-me"
                                v-model="status"
                                name="checkbox-1"
                            >
                                Remember Me
                            </b-form-checkbox>
                        </b-form-group>

                        <!-- submit buttons -->
                        <b-button
                            type="submit"
                            variant="primary"
                            block
                            :disabled="invalid"
                        >
                            LOGIN NOW
                        </b-button>
                    </b-form>
                </validation-observer>
            </b-card>
            <!-- /Login v1 -->
        </div>
    </div>
</template>

<script>
import {ValidationProvider, ValidationObserver} from 'vee-validate'
import {
    BButton,
    BForm,
    BFormInput,
    BFormGroup,
    BCard,
    BLink,
    BCardTitle,
    BCardText,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
} from 'bootstrap-vue'
import useJwt from '@/auth/jwt/useJwt'
import store from '@/store/index'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {required, email} from '@validations'
import {togglePasswordVisibility} from '@core/mixins/ui/forms'
import {getHomeRouteForLoggedInUser} from '@/auth/utils'
import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

export default {
    components: {
        // BSV
        BButton,
        BForm,
        BFormInput,
        BFormGroup,
        BCard,
        BCardTitle,
        BLink,
        VuexyLogo,
        BCardText,
        BInputGroup,
        BInputGroupAppend,
        BFormCheckbox,
        ValidationProvider,
        ValidationObserver,
    },
    mixins: [togglePasswordVisibility],
    data() {
        return {
            status: '',
            password: 'admin123',
            username: 'superadmin@mail',
            sideImg: require('@/assets/images/pages/login-v2.svg'),

            // validation rules
            required,
            email,
        }
    },
    computed: {
        passwordToggleIcon() {
            return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        imgUrl() {
            if (store.state.appConfig.layout.skin === 'dark') {
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                this.sideImg = require('@/assets/images/pages/login-v2-dark.svg')
                return this.sideImg
            }
            return this.sideImg
        },
    },
    methods: {
        login() {
            this.$refs.loginForm.validate().then(success => {
                if (success) {
                    useJwt.login({
                        username: this.username,
                        password: this.password,
                    })
                        .then(response => {
                            useJwt.setToken(response.data.access_token)
                            useJwt.getUserInfo()
                                .then(userDataRes => {
                                    const userData = userDataRes.data
                                    localStorage.setItem('userData', JSON.stringify(userData))

                                    this.$ability.update([
                                        {
                                            action: 'manage',
                                            subject: 'all',
                                        },
                                    ])

                                    this.$router.replace(getHomeRouteForLoggedInUser(userData.roles[0].name)).then(() => {
                                        this.$toast({
                                            component: ToastificationContent,
                                            position: 'top-right',
                                            props: {
                                                title: `Welcome ${userData.title} ${userData.firstname} ${userData.lastname}`,
                                                icon: 'CoffeeIcon',
                                                variant: 'success',
                                                text: `You have successfully logged in as ${userData.roles[0].name}. Now you can start to explore!`,
                                            },
                                        })
                                    })
                                }).catch(error => {
                                this.$refs.loginForm.setErrors(error.response.data.error)
                            })

                        })
                        .catch(error => {
                            this.$refs.loginForm.setErrors(error.response.data.error)
                        })
                }
            })
        },
    },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/pages/page-auth.scss';
</style>
