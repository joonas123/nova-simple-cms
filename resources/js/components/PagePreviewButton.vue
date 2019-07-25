<template>
    <a
        class="cursor-pointer text-70 hover:text-primary mr-3"
        :title="__('View')"
        @click.prevent="()"
    >
        <icon type="view" width="22" height="18" view-box="0 0 22 16" />
    </a>

</template>
<script>
import { Errors, Minimum } from 'laravel-nova'

export default {
    props: {
        resourceName: {
            type: String,
            default: 'pages',
        },
    },

    data: () => ({
        relationResponse: null,
    }),

    async created() {

    },

    methods: {
    
    },

    computed: {
    },
}
</script>