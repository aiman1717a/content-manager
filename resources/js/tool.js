Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'content-manager',
      path: '/content-manager',
      component: require('./components/Tool'),
    },
  ])
})
