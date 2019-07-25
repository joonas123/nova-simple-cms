Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'nova-simple-cms',
            path: '/nova-simple-cms/:resourceName',
            component: require('./components/Tool'),
            props: route => {
                return {
                    resourceName: route.params.resourceName,
                }
            },
        },
        {
            name: 'nova-simple-cms-create',
            path: '/nova-simple-cms/:resourceName/create',
            component: require('./components/Create'),
            props: route => {
                return {
                    resourceName: route.params.resourceName,
                }
            },
        },
        {
            name: 'nova-simple-cms-edit',
            path: '/nova-simple-cms/:resourceName/:resourceId/edit',
            component: require('./components/Update'),
            props: route => {
                return {
                    resourceName: route.params.resourceName,
                    resourceId: route.params.resourceId,
                    viaResource: route.query.viaResource,
                    viaResourceId: route.query.viaResourceId,
                    viaRelationship: route.query.viaRelationship,
                }
            },
        },
    ])
})
