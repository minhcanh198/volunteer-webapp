<template>
    <b-card>

        <b-row>

            <!-- User Info: Left col -->
            <b-col
                cols="21"
                xl="6"
                class="d-flex justify-content-between flex-column"
            >
                <!-- User Avatar & Action Buttons -->
                <div class="d-flex justify-content-start">
                    <b-avatar
                        :src="userData.avatar"
                        :text="avatarText(userData.fullName)"
                        :variant="`light-${resolveUserRoleVariant(userData.role)}`"
                        size="104px"
                        rounded
                    />
                    <div class="d-flex flex-column ml-1">
                        <div class="mb-1">
                            <h4 class="mb-0">
                                {{ `${userData.firstname} ${userData.lastname}` }}
                            </h4>
                            <span class="card-text">{{ userData.email }}</span>
                        </div>
                        <div class="d-flex flex-wrap">
                            <b-button
                                :to="{ name: 'apps-users-edit', params: { id: userData.id } }"
                                variant="primary"
                            >
                                Edit
                            </b-button>
                            <b-button
                                variant="outline-danger"
                                class="ml-1"
                            >
                                Delete
                            </b-button>
                        </div>
                    </div>
                </div>
            </b-col>

            <!-- Right Col: Table -->
            <b-col
                cols="12"
                xl="6"
            >
                <table class="mt-2 mt-xl-0 w-100">
                    <tr>
                        <th class="pb-50">
                            <feather-icon
                                icon="StarIcon"
                                class="mr-75"
                            />
                            <span class="font-weight-bold">Role</span>
                        </th>
                        <td class="pb-50 text-capitalize">
                            {{ userData.roles[0].name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <feather-icon
                                icon="PhoneIcon"
                                class="mr-75"
                            />
                            <span class="font-weight-bold">Contact</span>
                        </th>
                        <td>
                            {{ userData.phone_number }}
                        </td>
                    </tr>
                    <tr>
                        <th class="pb-50 pt-50">
                            <feather-icon
                                icon="CalendarIcon"
                                class="mr-75"
                            />
                            <span class="font-weight-bold">Created At</span>
                        </th>
                        <td class="pb-50">
                            {{ userData.created_at }}
                        </td>
                    </tr>
                </table>
            </b-col>
        </b-row>
    </b-card>
</template>

<script>
import {
    BCard, BButton, BAvatar, BRow, BCol,
} from 'bootstrap-vue'
import {avatarText} from '@core/utils/filter'
import useUsersList from '../users-list/useUsersList'

export default {
    components: {
        BCard, BButton, BRow, BCol, BAvatar,
    },
    props: {
        userData: {
            type: Object,
            required: true,
        },
    },
    setup() {
        const {resolveUserRoleVariant} = useUsersList()
        return {
            avatarText,
            resolveUserRoleVariant,
        }
    },
}
</script>

<style>

</style>
