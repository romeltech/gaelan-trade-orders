<template>
  <div>
    <page-title :title="'Items'"></page-title>
    <v-container class="py-8">
      <v-row v-if="loadingPage == true">
        <v-col cols="12">
          <v-skeleton-loader
            class="mx-auto"
            max-width="100%"
            type="list-item-avatar-three-line, image, article"
          ></v-skeleton-loader>
        </v-col>
      </v-row>
      <v-row v-else>
        <div class="col-md-8 mx-auto">
          <v-card :loading="loadingOrder" :disabled="loadingOrder">
            <v-card-title>
              <h4>Order Form</h4>
            </v-card-title>
            <v-card-text class="py-5">
              <ValidationObserver ref="order_form_observer" v-slot="{ valid }">
                <v-form ref="form">
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules=""
                    name="Location"
                  >
                    <v-autocomplete
                      v-model="orderObj.location"
                      :items="locationList"
                      label="Location"
                      item-text="name"
                      item-value="id"
                      outlined
                      @click="setLocations"
                      @blur="setLocations"
                      :error-messages="errors"
                      :loading="loadingLocation"
                    >
                    </v-autocomplete>
                  </ValidationProvider>
                  <v-divider class="py-3"></v-divider>
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="required"
                    name="Non-FoC Quantity"
                  >
                    <v-text-field
                      type="number"
                      outlined
                      v-model="orderObj.non_foc_quantity"
                      label="Non-FoC Quantity*"
                      :error-messages="errors"
                      required
                    ></v-text-field>
                  </ValidationProvider>
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="required"
                    name="FoC Quantity"
                  >
                    <v-text-field
                      type="number"
                      outlined
                      v-model="orderObj.foc_quantity"
                      label="FoC Quantity*"
                      :error-messages="errors"
                      required
                    ></v-text-field>
                  </ValidationProvider>

                  <v-card-actions class="px-0">
                    <v-btn
                      x-large
                      class="primary px-5"
                      :disabled="!valid"
                      :loading="loadingOrder"
                      @click="submitOrder"
                      >Submit</v-btn
                    >
                  </v-card-actions>
                </v-form>
              </ValidationObserver>
            </v-card-text>
          </v-card>
        </div>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapState, mapStores } from "pinia";
import { useLocationsStore } from "../../../stores/locations";
import {
  ValidationObserver,
  ValidationProvider,
} from "vee-validate/dist/vee-validate.full";
export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      locationList: [],
      loadingLocation: false,
      loadingPage: false,
      loadingOrder: false,
      orderObj: {},
    };
  },
  computed: {
    ...mapState(useLocationsStore, ["location_list"]),
    ...mapStores(useLocationsStore),
  },
  methods: {
    submitOrder() {
      console.log("submitOrder");
    },
    setLocations() {
      this.loadingLocation = true;
      if (this.location_list.length == 0) {
        this.locationsStore.fetchAllLocations().then(() => {
          this.locationList = this.location_list;
          this.loadingLocation = false;
          console.log("this.locationList", this.locationList);
        });
      } else {
        this.loadingLocation = false;
        this.locationList = this.location_list;
      }
    },
  },
  created() {
    this.loadingPage = false;
  },
};
</script>
