/**
 * Store for Items
 */

import { defineStore } from "pinia";

export const useItemsStore = defineStore({
    // id is required so that Pinia can connect the store to the devtools
    id: "items",
    state: () => {
        return {
            itemList: []
        };
    },
    actions: {
        async fetchAllItems() {
            if (Object.keys(this.itemList).length > 0) {
                return;
            }
            const response = await axios.get("/d/items/fetch/all");
            this.itemList = response.data.items;
            // console.log("itemList", this.itemList);
        }
    },
    getters: {
        item_list: state => state.itemList
    }
});
