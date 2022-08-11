let state = {
    itemList: [],
};
let getters = {
    all_item_list: state => state.itemList
};
const actions = {
    async fetchAllItems({ commit }) {
        const response = await axios.get("/d/items/fetch/all");
        commit("setItems", response.data.items);
    }
};
const mutations = {
    setItems: (state, fetchAllItems) => (state.itemList = fetchAllItems)
};

export default {
    // namespaced: true,
    state,
    getters,
    actions,
    mutations
};
