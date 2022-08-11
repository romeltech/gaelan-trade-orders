let state = {
    locationList: [],
};
let getters = {
    all_location_list: state => state.locationList
};
const actions = {
    async fetchAllLocations({ commit }) {
        const response = await axios.get("/d/locations/fetch/all");
        commit("setLocations", response.data.locations);
    }
};
const mutations = {
    setLocations: (state, fetchAllLocations) => (state.locationList = fetchAllLocations)
};

export default {
    // namespaced: true,
    state,
    getters,
    actions,
    mutations
};
