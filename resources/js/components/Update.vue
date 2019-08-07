<template>
    <loading-view :loading="loading">
        <heading class="mb-3">{{ __('Edit :resource', { resource: singularName }) }}</heading>

        <card class="overflow-hidden">

            <form @submit.prevent="updateResource" autocomplete="off">

                <div class="flex border-b border-40">
                    <div class="w-1/5 py-6 px-8">
                        <label for="blueprint" class="inline-block text-80 pt-2 leading-tight">
                            {{ __('Blueprint') }}
                        </label>
                    </div> 
                    <div class="py-6 px-8 w-1/2">
                        <select class="w-full form-control form-input form-input-bordered" v-if="!blueprintOptions.disabled" v-model="blueprint" name="blueprint" @change="getFields()">
                            <option v-for="(value, key) in blueprints" :key="key" :value="key">{{ __(value) }}</option>
                        </select>
                        <input v-else class="w-full form-control form-input form-input-bordered" readonly name="blueprint" :value="blueprint">
                        <!-- <div class="help-text help-text mt-2" v-if="blueprint && blueprints[blueprint].help">{{ blueprints[blueprint].help }}</div> -->
                    </div>
                </div>

                <loading-view :loading="fields && loadingFields">
                    <!-- Validation Errors -->
                    <validation-errors :errors="validationErrors" />
                    
                    <component
                        :class="{ 'remove-bottom-border': index == fields.length - 1 }"
                        v-for="(field, index) in fields"
                        :key="index"
                        :is="`form-${field.component}`"
                        :errors="validationErrors"
                        :resource-id="resourceId"
                        :resource-name="resourceName"
                        :field="field"
                        :via-resource="viaResource"
                        :via-resource-id="viaResourceId"
                        :via-relationship="viaRelationship"
                        @file-deleted="updateLastRetrievedAtTimestamp"
                    />

                    <!-- Update Button -->
                    <div class="flex items-center px-8 py-4">
                        <cancel-button />

                        <progress-button
                            class="mr-3"
                            dusk="update-and-continue-editing-button"
                            @click.native="updateAndContinueEditing"
                            :disabled="isWorking"
                            :processing="submittedViaUpdateAndContinueEditing"
                        >
                            {{ __('Update & Continue Editing') }}
                        </progress-button>

                        <progress-button
                            dusk="update-button"
                            type="submit"
                            :disabled="isWorking"
                            :processing="submittedViaUpdateResource"
                        >
                            {{ __('Update :resource', { resource: singularName }) }}
                        </progress-button>
                    </div>
                </loading-view>
            </form>
        </card>
    </loading-view>
</template>

<script>
import { Errors, InteractsWithResourceInformation } from 'laravel-nova'

export default {
    mixins: [InteractsWithResourceInformation],

    props: {
        resourceName: {
            type: String,
            required: true,
        },
        resourceId: {
            required: true,
        },
        viaResource: {
            default: '',
        },
        viaResourceId: {
            default: '',
        },
        viaRelationship: {
            default: '',
        },
    },

    data: () => ({
        relationResponse: null,
        loading: true,
        loadingFields: false,
        submittedViaUpdateAndContinueEditing: false,
        submittedViaUpdateResource: false,
        fields: [],
        validationErrors: new Errors(),
        lastRetrievedAt: null,
        blueprints: [],
        blueprint: '',
        blueprintOptions: []
    }),

    async created() {
        if (Nova.missingResource(this.resourceName)) return this.$router.push({ name: '404' })

        const { data: blueprints } = await Nova.request().get(`/nova-vendor/nova-simple-cms/blueprints`,
        {
            params: {
                editing: true,
            },
        })
        this.blueprints = blueprints
        
        this.loading = false
        
        this.getFields()
        this.updateLastRetrievedAtTimestamp()
    },

    methods: {
        /**
         * Get the available fields for the resource.
         */
        async getFields() {

            if(!this.blueprints) { return false }

            this.loadingFields = true

            this.fields = []
            const {
                data: { fields, blueprint, blueprintOptions },
                } = await Nova.request()
                .get(`/nova-vendor/nova-simple-cms/${this.resourceName}/${this.resourceId}/update-fields`, 
                {
                    params: {
                        editing: true,
                        editMode: 'update',
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                        changedblueprint: this.blueprint
                    },
                })
                .catch(error => {
                    if (error.response.status == 404) {
                        this.$router.push({ name: '404' })
                        return
                    }
                })

            this.blueprint = blueprint
            this.fields = fields
            this.blueprintOptions = blueprintOptions
            this.loadingFields = false
        },

        /**
         * Update the resource using the provided data.
         */
        async updateResource() {
            this.submittedViaUpdateResource = true

            try {
                const {
                    data: { redirect },
                } = await this.updateRequest()

                this.submittedViaUpdateResource = false

                this.$toasted.show(
                    this.__('The :resource was updated!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                this.$router.push({ path: redirect })
            } catch (error) {
                this.submittedViaUpdateResource = false
                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }

                if (error.response.status == 409) {
                    this.$toasted.show(
                        this.__(
                            'Another user has updated this resource since this page was loaded. Please refresh the page and try again.'
                        ),
                        { type: 'error' }
                    )
                }
            }
        },

        /**
         * Update the resource and reset the form
         */
        async updateAndContinueEditing() {
            this.submittedViaUpdateAndContinueEditing = true

            try {
                const response = await this.updateRequest()

                this.submittedViaUpdateAndContinueEditing = false

                this.$toasted.show(
                    this.__('The :resource was updated!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                // Reset the form by refetching the fields
                this.getFields()

                this.validationErrors = new Errors()

                this.updateLastRetrievedAtTimestamp()
            } catch (error) {
                this.submittedViaUpdateAndContinueEditing = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }

                if (error.response.status == 409) {
                    this.$toasted.show(
                        this.__(
                            'Another user has updated this resource since this page was loaded. Please refresh the page and try again.'
                        ),
                        { type: 'error' }
                    )
                }
            }
        },

        /**
         * Send an update request for this resource
         */
        updateRequest() {

            return Nova.request().post(
                `/nova-vendor/nova-simple-cms/${this.resourceName}/${this.resourceId}`,
                this.updateResourceFormData,
                {
                    params: {
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                    },
                }
            )

        },

        /**
         * Update the last retrieved at timestamp to the current UNIX timestamp.
         */
        updateLastRetrievedAtTimestamp() {
            this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
        },
    },

    computed: {
        /**
         * Create the form data for creating the resource.
         */
        updateResourceFormData() {
            return _.tap(new FormData(), formData => {
                _(this.fields).each(field => {
                    field.fill(formData)
                })

                formData.append('_method', 'PUT')
                formData.append('_retrieved_at', this.lastRetrievedAt)
                formData.append('blueprint', this.blueprint)

            })
        },

        singularName() {
            if (this.relationResponse) {
                return this.relationResponse.singularLabel
            }

            return this.resourceInformation.singularLabel
        },

        isRelation() {
            return Boolean(this.viaResourceId && this.viaRelationship)
        },

        /**
         * Determine if the form is being processed
         */
        isWorking() {
            return this.submittedViaUpdateResource || this.submittedViaUpdateAndContinueEditing
        },

    },
}
</script>
