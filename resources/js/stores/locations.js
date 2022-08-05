/**
 * Store for locations
 */

import { defineStore } from "pinia";

export const useLocationsStore = defineStore({
    // id is required so that Pinia can connect the store to the devtools
    id: "locations",
    state: () => {
        return {
            locationList: []
        };
    },
    actions: {
        async fetchAllLocations() {
            if (Object.keys(this.locationList).length > 0) {
                return;
            }
            const response = await axios.get("/d/locations/fetch/all");
            this.locationList = response.data.locations;
            // console.log("locationList", this.locationList);
        }
    },
    getters: {
        location_list: state => state.locationList
    }
});
