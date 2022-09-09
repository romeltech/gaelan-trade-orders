let state = {
    salesRepList: [],
};
let getters = {
    all_sales_rep_list: state => state.salesRepList
};
const actions = {
    async fetchAllSalesRep({ commit }) {
        const response = await axios.get("/d/salesrep/fetch/all");
        commit("setSalesRep", response.data);
    }
};
const mutations = {
    setSalesRep: (state, fetchAllSalesRep) => (state.salesRepList = fetchAllSalesRep)
};

export default {
    // namespaced: true,
    state,
    getters,
    actions,
    mutations
};
