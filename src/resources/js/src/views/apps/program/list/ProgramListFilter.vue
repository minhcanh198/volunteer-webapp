<template>
    <b-card no-body>
        <b-card-header class="pb-50">
            <h5>
                Filters
            </h5>
        </b-card-header>
        <b-card-body>
            <b-row>
                <b-col
                    cols="12"
                    md="4"
                    class="mb-md-0 mb-2"
                >
                    <label>Country</label>
                    <v-select
                        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                        :value="countryFilter"
                        :options="countryOptions"
                        class="w-100"
                        :reduce="val => val.value"
                        @input="(val) => $emit('update:countryFilter', val)"
                    />
                </b-col>
                <b-col
                    cols="12"
                    md="4"
                    class="mb-md-0 mb-2"
                >
                    <label>Category</label>
                    <v-select
                        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                        :value="categoryFilter"
                        :options="categoryOptions"
                        class="w-100"
                        :reduce="val => val.value"
                        @input="(val) => $emit('update:categoryFilter', val)"
                    />
                </b-col>
                <b-col
                    cols="12"
                    md="4"
                    class="mb-md-0 mb-2"
                >
                    <label>Status</label>
                    <v-select
                        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                        :value="statusFilter"
                        :options="statusOptions"
                        class="w-100"
                        :reduce="val => val.value"
                        @input="(val) => $emit('update:statusFilter', val)"
                    />
                </b-col>
            </b-row>
        </b-card-body>
    </b-card>
</template>

<script>
import {BCard, BCardHeader, BCardBody, BRow, BCol} from 'bootstrap-vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'

export default {
    components: {
        BRow,
        BCol,
        BCard,
        BCardHeader,
        BCardBody,
        vSelect,
        flatPickr,
    },
    props: {
        statusFilter: {
            type: [String, null],
            default: null,
        },
        countryFilter: {
            type: [Number, null],
            default: null,
        },
        categoryFilter: {
            type: [Number, null],
            default: null,
        },
        statusOptions: {
            type: Array,
            required: true,
        },
        countryOptions: {
            type: Array,
            required: true,
        },
        categoryOptions: {
            type: Array,
            required: true,
        },
    },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/vue-select.scss';
@import '~@core/scss/vue/libs/vue-flatpicker.scss';
</style>
