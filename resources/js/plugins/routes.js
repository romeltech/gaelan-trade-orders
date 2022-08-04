import Dashboard from "../components/admin/Dashboard";
import ForbiddenPage from "../components/common/ForbiddenPage";
import NotFoundPage from "../components/common/NotFoundPage";
import Users from "../components/admin/users/Users";
import EditUser from "../components/admin/users/EditUser";
import NewUser from "../components/admin/users/NewUser";

import Items from "../components/admin/items/Items";
import ImportItem from "../components/admin/items/ImportItem";
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
];
