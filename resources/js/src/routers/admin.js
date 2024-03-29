import authenticated from "../store/authenticated";

const admin = [
    {
        path: '/admin',
        component: () => import("../layouts/admin.vue"),
        children: [
            {
                path: 'users',
                name: "admin-users",
                component: () => import("../pages/admin/users/index.vue"),

            },
            {
                path: 'tasks',
                name: "admin-tasks",
                component: () => import("../pages/admin/tasks/index.vue"),

            }
        ],
        meta: { requiresAuth: true },
        beforeEnter: (to, from, next) => {
            const isAdmin = authenticated.state.user.role == 'ADMIN'
            if (isAdmin) {
                next()
            }
            next(from)
        }
    }
]

export default admin