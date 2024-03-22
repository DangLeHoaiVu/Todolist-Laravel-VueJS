const admin = [
    {
        path: '/admin',
        component: () => import("../layouts/admin.vue"),
        children: [
            {
                path: 'users',
                name: "admin-users",
                component: () => import("../pages/admin/users/index.vue"),

            }
        ],
        meta: { requiresAuth: true },
        beforeEnter: (to, from, next) => {
            console.log('admin');
            next()
        }
    }
]

export default admin