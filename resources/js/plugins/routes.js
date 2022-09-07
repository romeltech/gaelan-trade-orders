import Dashboard from "../components/admin/Dashboard";
import ForbiddenPage from "../components/common/ForbiddenPage";
import NotFoundPage from "../components/common/NotFoundPage";
import Users from "../components/admin/users/Users";
import EditUser from "../components/admin/users/EditUser";
import NewUser from "../components/admin/users/NewUser";

import Items from "../components/admin/items/Items";
import ImportItem from "../components/admin/items/ImportItem";

import Locations from "../components/admin/locations/Locations";
import ImportLocation from "../components/admin/locations/ImportLocation";

import AdminOrders from "../components/admin/orders/AdminOrders";

import NewOrder from "../components/staff/orders/NewOrder";
import EditOrder from "../components/staff/orders/EditOrder";
import Orders from "../components/staff/orders/Orders";

import AccountSettings from "../components/common/user/AccountSettings";
// Vuex Store
import store from "../store";
let auth_user = store.state.authUser;
let admin_access = ["admin", "manager"];
let staff_access = ["staff"];
let staff_admin_access = ["staff", "admin"];

export const routes = [
    /**
     * Admin Pages
     */
    {
        path: "/forbidden",
        component: ForbiddenPage,
        name: "ForbiddenPage"
    },
    {
        path: "/notfound",
        component: NotFoundPage,
        name: "NotFoundPage"
    },
    {
        path: "/d/dashboard",
        component: Dashboard,
        name: "dashboard"
    },

    /**
     * Users
     *
     */
    {
        path: "/d/users",
        component: Users,
        name: "Users",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/user/new",
        component: NewUser,
        name: "NewUser",
        beforeEnter: (to, from, next) => {
            auth_user.userObject.role == 'admin'
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/user/edit/:id",
        component: EditUser,
        name: "EditUser",
        beforeEnter: (to, from, next) => {
            auth_user.userObject.role == 'admin'
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },

    /**
     * Items
     */
    {
        path: "/d/items",
        component: Items,
        name: "Items",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/items/import",
        component: ImportItem,
        name: "ImportItem",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/items/page/:page",
        component: Items,
        name: "PaginatedItems",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },

    /**
     * Locations
     */
    {
        path: "/d/customers",
        component: Locations,
        name: "Locations",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/customers/import",
        component: ImportLocation,
        name: "ImportLocation",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/customers/page/:page",
        component: Locations,
        name: "PaginatedLocations",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },

    /**
     * Locations
     */
    {
        path: "/staff/order/new",
        component: NewOrder,
        name: "NewOrder",
        beforeEnter: (to, from, next) => {
            staff_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/staff/order/edit/:ordernum",
        component: EditOrder,
        name: "EditOrder",
        beforeEnter: (to, from, next) => {
            staff_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    // {
    //     path: "/staff/order-form",
    //     component: OrderForm,
    //     name: "OrderForm"
    // },

    /**
     * Orders
     */
    // {
    //     path: "/staff/orders",
    //     component: Orders,
    //     name: "Orders"
    // },
    {
        path: "/staff/orders/:status?",
        component: Orders,
        name: "StaffOrders",
        beforeEnter: (to, from, next) => {
            staff_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/staff/orders/:status/page/:page",
        component: Orders,
        name: "PaginatedStaffOrders",
        beforeEnter: (to, from, next) => {
            staff_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },

    /**
     * Admin - Orders
     */
    {
        path: "/d/orders/:status?",
        component: AdminOrders,
        name: "AdminOrders",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    {
        path: "/d/orders/:status/page/:page",
        component: AdminOrders,
        name: "PaginatedAdminOrders",
        beforeEnter: (to, from, next) => {
            admin_access.includes(auth_user.userObject.role) == true
                ? next()
                : next({ name: "NotFoundPage" });
        }
    },
    // {
    //     path: "/d/orders/submitted",
    //     component: AdminOrders,
    //     name: "AdminOrders",
    //     beforeEnter: (to, from, next) => {
    //         admin_access.includes(auth_user.userObject.role) == true
    //             ? next()
    //             : next({ name: "NotFoundPage" });
    //     }
    // },
    // {
    //     path: "/d/orders/completed",
    //     component: AdminOrdersCompleted,
    //     name: "AdminOrdersCompleted",
    //     beforeEnter: (to, from, next) => {
    //         admin_access.includes(auth_user.userObject.role) == true
    //             ? next()
    //             : next({ name: "NotFoundPage" });
    //     }
    // },

    {
        path: "/user/account-settings",
        component: AccountSettings,
        name: "AccountSettings",
        beforeEnter: (to, from, next) => {
            auth_user.userObject.role !== ""
                ? next()
                : next({ name: "NotFoundPage" });
        }
    }
];
