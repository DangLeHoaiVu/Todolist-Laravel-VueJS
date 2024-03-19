const home = [
    {
        path: '/home',
        component: () => import("../layouts/home.vue"),
        children: [
            {
                path: 'board',
                name: "home-board",
                component: () => import("../pages/home/board/index.vue")
            },
            {
                path: 'issues',
                name: "home-issues",
                component: () => import("../pages/home/issues/index.vue")
            }
        ]
    }
]

export default home