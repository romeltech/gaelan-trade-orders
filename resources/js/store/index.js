import Vue from "vue";
import Vuex from "vuex";
import authUser from "./modules/authUser";
import items from "./modules/items";
import locations from "./modules/locations";
import salesRep from "./modules/salesRep";
Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        authUser,
        items,
        locations,
        salesRep
    }
});

export default store;
