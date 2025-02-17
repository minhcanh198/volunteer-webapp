<template>
    <div>
        <lead-list-filter
            :category-options="categoryOptions"
            :category-filter.sync="categoryFilter"
            :provider-options="[]"
            :country-options="countryOptions"
            :country-filter.sync="countryFilter"
            @date-changed="updateDateFilter"
        ></lead-list-filter>
        <b-card title="All leads">
            <!-- search input -->
            <div class="custom-search d-flex justify-content-end">
                <b-form-group>
                    <div class="d-flex align-items-center">
                        <label class="mr-1">Search</label>
                        <b-form-input
                            v-model="searchTerm"
                            placeholder="Search"
                            type="text"
                            class="d-inline-block"
                            @input="handleSearch"
                        />
                    </div>
                </b-form-group>
            </div>

            <!-- table -->
            <vue-good-table
                mode="remote"
                :isLoading.sync="isLoading"
                :columns="columns"
                :rows="rows"
                :rtl="direction"
                :search-options="{
                    enabled: true,
                    externalQuery: searchTerm
                  }"
                :select-options="{
                    enabled: true,
                    selectOnCheckboxOnly: true,
                    selectionInfoClass: 'custom-class',
                    selectionText: 'rows selected',
                    clearSelectionText: 'clear',
                    disableSelectInfo: false,
                    selectAllByGroup: true,
                  }"
                :pagination-options="{
                    enabled: true,
                  }"

                @on-sort-change="onSortChange"
            >
                <template
                    slot="table-row"
                    slot-scope="props"
                >
                      <span
                          v-if="props.column.field === 'lead_id'"
                          class="text-nowrap"
                      >
                      <span class="text-nowrap">{{ formatLeadID(props.row.id) }}</span>
                    </span>
                    <!-- Column: Program Name-->
                    <span
                        v-if="props.column.field === 'program_name'"
                        class="text-nowrap"
                    >
                      <span class="text-nowrap">{{ props.row.program.title }}</span>
                    </span>
                    <span
                        v-if="props.column.field === 'created_at_date'"
                        class="text-nowrap"
                    >
                      <span class="text-nowrap">{{ formatCreatedAtDate(props.row.created_at) }}</span>
                    </span>
                    <span
                        v-if="props.column.field === 'created_at_time'"
                        class="text-nowrap"
                    >
                      <span class="text-nowrap">{{ formatCreatedAtTime(props.row.created_at) }}</span>
                    </span>


                    <!-- Column: Status -->
                    <span v-else-if="props.column.field === 'status'">
                      <b-badge :variant="statusVariant(props.row.status)">
                        {{ props.row.status }}
                      </b-badge>
                    </span>

                    <!-- Column: Action -->
                    <span v-else-if="props.column.field === 'action'">
                        <span>
                            <b-dropdown
                                variant="link"
                                toggle-class="text-decoration-none"
                                no-caret
                            >
                              <template v-slot:button-content>
                                <feather-icon
                                    icon="MoreVerticalIcon"
                                    size="16"
                                    class="text-body align-middle mr-25"
                                />
                              </template>
                              <b-dropdown-item>
                                <feather-icon
                                    icon="FileTextIcon"
                                    class="mr-50"
                                />
                               <b-link
                                   :to="{ name: 'lead-view', params: { id: props.row.id } }"
                               >
                                   <span>View</span>
                               </b-link>
                              </b-dropdown-item>
                                <b-dropdown-item>
                                <feather-icon
                                    icon="Edit2Icon"
                                    class="mr-50"
                                />
                               <b-link
                               >
                                   <span>Edit</span>
                               </b-link>
                              </b-dropdown-item>
                              <b-dropdown-item>
                                <feather-icon
                                    icon="TrashIcon"
                                    class="mr-50"
                                />
                                <span>Delete</span>
                              </b-dropdown-item>
                            </b-dropdown>
                        </span>
                     </span>

                    <!-- Column: Common -->
                    <span v-else>
                          {{ props.formattedRow[props.column.field] }}
                    </span>
                </template>

                <!-- pagination -->
                <template
                    slot="pagination-bottom"
                    slot-scope="props"
                >
                    <div class="d-flex justify-content-between flex-wrap">

                        <!-- page length -->
                        <div class="d-flex align-items-center mb-0 mt-1">
                            <span class="text-nowrap ">
                              Showing
                            </span>
                            <b-form-select
                                v-model="pageLength"
                                :options="pages"
                                class="mx-1"
                                @input="handlePageChange"
                            />
                            <span class="text-nowrap"> of {{ total }} entries </span>
                        </div>

                        <!-- pagination -->
                        <div>
                            <b-pagination
                                :value="1"
                                :total-rows="total"
                                :per-page="pageLength"
                                first-number
                                last-number
                                align="right"
                                prev-class="prev-item"
                                next-class="next-item"
                                class="mt-1 mb-0"
                                @change="handleChangePage"
                            >
                                <template #prev-text>
                                    <feather-icon
                                        icon="ChevronLeftIcon"
                                        size="18"
                                    />
                                </template>
                                <template #next-text>
                                    <feather-icon
                                        icon="ChevronRightIcon"
                                        size="18"
                                    />
                                </template>
                            </b-pagination>
                        </div>
                    </div>
                </template>
                <div slot="selected-row-actions">
                    <button class="btn btn-danger">Delete</button>
                </div>
            </vue-good-table>
        </b-card>
    </div>
</template>

<script>
import {
    BAvatar, BBadge, BPagination, BFormGroup, BFormInput, BFormSelect, BDropdownItem, BDropdown, BCard, BLink,
} from 'bootstrap-vue'
import LeadListFilter from "./LeadListFilter";
import {VueGoodTable} from 'vue-good-table'
import store from '@/store/index'
import {onUnmounted, watch} from "@vue/composition-api";
import leadStoreModule from "../leadStoreModule";
import ToastificationContent from "../../../../@core/components/toastification/ToastificationContent";

export default {
    components: {
        LeadListFilter,
        VueGoodTable,
        BAvatar,
        BBadge,
        BPagination,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BDropdownItem,
        BDropdown,
        BCard,
        BLink,
    },
    data() {
        return {
            isLoading: false,
            log: [],
            total: 0,
            pageLength: 5,
            currentPage: 1,
            searchTerm: '',
            dir: false,
            pages: ['3', '5', '10'],
            columns: [
                {
                    label: 'Lead ID',
                    field: 'lead_id',
                },
                {
                    label: 'Date',
                    field: 'created_at_date',
                },
                {
                    label: 'Time',
                    field: 'created_at_time',
                },
                {
                    label: 'Fist Name',
                    field: 'first_name',
                },
                {
                    label: 'Last Name',
                    field: 'last_name',
                },
                {
                    label: 'Email',
                    field: 'email',
                },
                {
                    label: 'Program ID',
                    field: 'program_id',
                },
                {
                    label: 'Provider',
                    field: 'provider_id',
                },
                {
                    label: 'Program Name',
                    field: 'program_name',
                },
                {
                    label: 'Action',
                    field: 'action',
                },
            ],
            rows: [],
            status: [{
                1: 'Current', 2: 'Professional', 3: 'Rejected', 4: 'Resigned', 5: 'Applied',
            },
                {
                    1: 'light-primary', 2: 'light-success', 3: 'light-danger', 4: 'light-warning', 5: 'light-info',
                }],
            countryOptions: [],
            categoryOptions: [],
            providerOptions: [],
            countryFilter: null,
            categoryFilter: null,
            dateFilter: null,
        }
    },
    computed: {
        statusVariant() {
            const statusColor = {
                /* eslint-disable key-spacing */
                Current: 'light-primary',
                Professional: 'light-success',
                Rejected: 'light-danger',
                Resigned: 'light-warning',
                Applied: 'light-info',
                /* eslint-enable key-spacing */
            }

            return status => statusColor[status]
        },
        direction() {
            if (store.state.appConfig.isRTL) {
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                this.dir = true
                return this.dir
            }
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.dir = false
            return this.dir
        },
    },
    created() {
        this.fetchLeads()
        this.fetchCountries()
        this.fetchCategories()
    },

    watch: {
        categoryFilter() {
            this.fetchLeads()
        },
        countryFilter() {
            this.fetchLeads()
        },
        dateFilter() {
            this.fetchLeads()
        },
    },

    methods: {
        formatCreatedAtDate(dateTime) {
            return dateTime.split(' ')[0]
        },
        formatCreatedAtTime(dateTime) {
            return dateTime.split(' ')[1]
        },
        formatLeadID(leadID) {
            const idAsStringLength = leadID.toString().length
            if (idAsStringLength >= 6)
                return leadID
            if (idAsStringLength === 5) {
                return '0' + leadID
            }
            if (idAsStringLength === 4) {
                return '00' + leadID
            }
            if (idAsStringLength === 3) {
                return '000' + leadID
            }
            if (idAsStringLength === 2) {
                return '0000' + leadID
            }
            return '00000' + leadID
        },
        handleSearch(searching) {
            this.log.push(`The user searched for: ${searching}`)
        },
        handleChangePage(page) {
            this.currentPage = page
            this.fetchLeads()
        },
        handlePageChange(active) {
            this.pageLength = active
            this.currentPage = 1
            this.fetchLeads()
        },
        onSortChange(params) {
            this.log.push(`the user ordered:  ${params[0].type}`)
        },

        fetchLeads() {
            this.isLoading = true
            this.$store.dispatch('app-lead/fetchLeads', {
                'per_page': this.pageLength,
                'search': this.searchTerm,
                'page': this.currentPage,
                'category': this.categoryFilter,
                'country': this.countryFilter,
                'date': this.dateFilter,
            })
                .then(res => {
                    this.isLoading = false
                    this.rows = res.data.data
                    this.total = res.data.total
                })
                .catch(err => {
                    this.isLoading = false
                    console.log(err)
                })
        },

        fetchCountries() {
            this.$store.dispatch('app-lead/fetchCountries')
                .then(res => {
                    res.data.forEach(({id, name}) => {
                        this.countryOptions.push({label: name, value: id})
                    })
                })
                .catch(err => {
                    console.log(err)
                })
        },
        fetchCategories() {
            this.$store.dispatch('app-lead/fetchCategories')
                .then(res => {
                    res.data.forEach(({id, name}) => {
                        this.categoryOptions.push({label: name, value: id})
                    })
                })
                .catch(err => {
                    console.log(err)
                })
        },
        updateDateFilter(date) {
            this.dateFilter = date
        }
    },

    setup() {
        const LEAD_STORE_MODULE_NAME = 'app-lead'

        // Register module
        if (!store.hasModule(LEAD_STORE_MODULE_NAME)) store.registerModule(LEAD_STORE_MODULE_NAME, leadStoreModule)

        // UnRegister on leave
        onUnmounted(() => {
            if (store.hasModule(LEAD_STORE_MODULE_NAME)) store.unregisterModule(LEAD_STORE_MODULE_NAME)
        })
    }
}
</script>


<style lang="scss">
@import '~@core/scss/vue/libs/vue-good-table.scss';
</style>

