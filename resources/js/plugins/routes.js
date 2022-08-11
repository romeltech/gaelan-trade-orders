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


import OrderForm from "../components/staff/orders/OrderForm";
import NewOrder from "../components/staff/orders/NewOrder";
import EditOrder from "../components/staff/orders/EditOrder";
import Orders from "../components/staff/orders/Orders";
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
        name: "Users"
    },
    {
        path: "/d/user/new",
        component: NewUser,
        name: "NewUser"
    },
    {
        path: "/d/user/edit/:id",
        component: EditUser,
        name: "EditUser"
    },

    /**
     * Items
     */
    {
        path: "/d/items",
        component: Items,
        name: "Items"
    },
    {
        path: "/d/items/import",
        component: ImportItem,
        name: "ImportItem"
    },
    {
        path: "/d/items/page/:page",
        component: Items,
        name: "PaginatedItems"
    },

    /**
     * Locations
     */
    {
        path: "/d/locations",
        component: Locations,
        name: "Locations"
    },
    {
        path: "/d/locations/import",
        component: ImportLocation,
        name: "ImportLocation"
    },
    {
        path: "/d/locations/page/:page",
        component: Locations,
        name: "PaginatedLocations"
    },

    /**
     * Locations
     */
    {
        path: "/staff/order/new",
        component: NewOrder,
        name: "NewOrder"
    },
    {
        path: "/staff/order/edit/:ordernum",
        component: EditOrder,
        name: "EditOrder"
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
        name: "StaffOrders"
    },
    {
        path: "/staff/orders/:status/page/:page",
        component: Orders,
        name: "PaginatedStaffOrders"
    },


    /**
     * Admin - Orders
     */
    {
        path: "/d/orders",
        component: AdminOrders,
        name: "AdminOrders"
    },
    {
        path: "/d/orders/page/:page",
        component: AdminOrders,
        name: "PaginatedAdminOrders"
    },
];
