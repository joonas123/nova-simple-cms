<template>
    <loading-view :loading="loading">
        <heading class="mb-3">{{ __('New :resource', { resource: singularName }) }}</heading>

        <card class="overflow-hidden">
            <form @submit.prevent="createResource" autocomplete="off">

                <div class="flex border-b border-40">
                    <div class="w-1/5 py-6 px-8">
                        <label for="blueprint" class="inline-block text-80 pt-2 leading-tight">
                            {{ __('Blueprint') }}
                        </label>
                    </div> 
                    <div class="py-6 px-8 w-1/2">
                        <select class="w-full form-control form-input form-input-bordered" v-model="blueprint" name="blueprint" @change="getFields()">
                            <option v-for="(value, key) in blueprints" :key="key" :value="value" :selected="blueprint == value">{{ __(value) }}</option>
                        </select>
                        <!-- <div class="help-text help-text mt-2" v-if="blueprint && blueprints[blueprint].help">{{ blueprints[blueprint].help }}</div> -->
                    </div>
                </div>

                <loading-view :loading="fields && loadingFields">

                    <!-- Validation Errors -->
                    <validation-errors :errors="validationErrors" />

                    <!-- Fields -->
                    <div v-for="field in fields">
                        <component
                            :is="'form-' + field.component"
                            :errors="validationErrors"
                            :resource-name="resourceName"
                            :field="field"
                            :via-resource="viaResource"
                            :via-resource-id="viaResourceId"
                            :via-relationship="viaRelationship"
                        />
                    </div>

                    <!-- Create Button -->
                    <div class="flex items-center px-8 py-4">
                        <a
                            @click="$router.back()"
                            class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
                        >
                            {{ __('Cancel') }}
                        </a>

                        <progress-button
                            class="mr-3"
                            dusk="create-and-add-another-button"
                            @click.native="createAndAddAnother"
                            :disabled="isWorking"
                            :processing="submittedViaCreateAndAddAnother"
                        >
                            {{ __('Create & Add Another') }}
                        </progress-button>

                        <progress-button
                            dusk="create-button"
                            type="submit"
                            :disabled="isWorking"
                            :processing="submittedViaCreateResource"
                        >
                            {{ __('Create :resource', { resource: singularName }) }}
                        </progress-button>
                    </div>
                </loading-view>
            </form>
        </card>
    </loading-view>
</template>

<script>
import { Errors, Minimum } from 'laravel-nova'

export default {
    props: {
        resourceName: {
            type: String,
            default: 'pages',
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
        submittedViaCreateAndAddAnother: false,
        submittedViaCreateResource: false,
        fields: [],
        validationErrors: new Errors(),
        blueprints: [],
        blueprint: ''
    }),

    async created() {
        // if (Nova.missingResource(this.resourceName)) return this.$router.push({ name: '404' })

        const { data: blueprints } = await Nova.request(
            `/nova-vendor/nova-simple-cms/blueprints`,
        )
        this.blueprints = blueprints
        this.blueprint = blueprints[0]

        this.loading = false

        this.getFields()

    },

    methods: {
        /**
         * Get the available fields for the resource.
         */
        async getFields() {

            if(!this.blueprints) { return false }

            this.loadingFields = true
            this.fields = []

            const { data: fields } = await Nova.request().get(
                `/nova-vendor/nova-simple-cms/${this.resourceName}/creation-fields`,
                {
                    params: {
                        editing: false,
                        editMode: 'create',
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                        blueprint: this.blueprint
                    },
                }
            )

            this.fields = fields
            this.loadingFields = false
        },

        /**
         * Create a new resource instance using the provided data.
         */
        async createResource() {
            this.submittedViaCreateResource = true

            try {
                const {
                    data: { redirect },
                } = await this.createRequest()

                this.submittedViaCreateResource = false

                this.$toasted.show(
                    this.__('The :resource was created!', {
                        resource: 'page',
                    }),
                    { type: 'success' }
                )

                this.$router.push({ path: redirect })
            } catch (error) {
                console.log(error)
                this.submittedViaCreateResource = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            }
        },

        /**
         * Create a new resource and reset the form
         */
        async createAndAddAnother() {
            this.submittedViaCreateAndAddAnother = true

            try {
                const response = await this.createRequest()

                this.submittedViaCreateAndAddAnother = false

                this.$toasted.show(
                    this.__('The :resource was created!', {
                        resource: 'page',
                    }),
                    { type: 'success' }
                )

                // Reset the form by refetching the fields
                this.getFields()

                this.validationErrors = new Errors()
            } catch (error) {
                this.submittedViaCreateAndAddAnother = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            }
        },

        /**
         * Send a create request for this resource
         */
        createRequest() {
            return Nova.request().post(
                `/nova-vendor/nova-simple-cms/${this.resourceName}`,
                this.createResourceFormData()
            )

            // return Nova.request().post(
            //     `/nova-api/${this.resourceName}`,
            //     this.createResourceFormData()
            // )
        },

        /**
         * Create the form data for creating the resource.
         */
        createResourceFormData() {
            return _.tap(new FormData(), formData => {
                _.each(this.fields, field => {
                    if(field.attribute != 'id')
                        field.fill(formData)
                })

                formData.append('viaResource', this.viaResource)
                formData.append('viaResourceId', this.viaResourceId)
                formData.append('viaRelationship', this.viaRelationship)
                formData.append('blueprint', this.blueprint)
            })
        },
    },

    computed: {
        singularName() {
            return 'page'
            // if (this.relationResponse) {
            //     return this.relationResponse.singularLabel
            // }

            // return this.resourceInformation.singularLabel
        },

        isRelation() {
            return Boolean(this.viaResourceId && this.viaRelationship)
        },

        /**
         * Determine if the form is being processed
         */
        isWorking() {
            return this.submittedViaCreateResource || this.submittedViaCreateAndAddAnother
        },
    },
}
</script>
