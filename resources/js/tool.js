Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'bios.detail', 
      path: '/bios/:resourceName',
      component: require('./components/Detail'),
    },
    {
      name: 'bios.edit', 
      path: '/bios/:resourceName/edit',
      component: require('./components/Update'),
    },
  ])
})
