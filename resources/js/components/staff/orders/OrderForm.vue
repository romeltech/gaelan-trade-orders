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
        <div class="col-12">
          <v-card :loading="loadingOrder" :disabled="loadingOrder">
            <v-card-title>
              <h4 class="textcolor--text">Order Form</h4>
            </v-card-title>
            <v-card-text class="py-5">
              <v-autocomplete
                v-model="orderObj.location"
                :items="locationList"
                label="Location"
                item-text="name"
                item-value="id"
                outlined
                @click="setLocations"
                @blur="setLocations"
                :loading="loadingLocation"
              >
              </v-autocomplete>
              <div class="d-flex align-center mb-3">
                <div class="text-subtitle-1 textcolor--text">Order Details</div>
                <v-btn @click="addItem" class="primary mx-3">Add Item</v-btn>
              </div>

              <v-simple-table class="elevation-1">
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">No.</th>
                      <th class="text-left">SKU</th>
                      <!-- <th class="text-left">Item Name</th> -->
                      <th class="text-left">Non-FoC Quantity</th>
                      <th class="text-left">FoC Quantity</th>
                      <th class="text-left">Total Quantity</th>
                      <!-- <th class="text-left">Unit of Measure</th> -->
                      <th class="text-left">Unit Price Excl. VAT</th>
                      <th class="text-left">Line Amount Excl. VAT</th>
                      <th class="text-right">Action</th>
                    </tr>
                  </thead>
                  <tbody v-if="orderDetails.length > 0">
                    <tr v-for="item in orderDetails" :key="item.id">
                      <td>{{ item.sku }}</td>
                      <td>{{ item.location_code }}</td>
                      <td>{{ item.non_foc_quantity }}</td>
                      <td>{{ item.foc_quantity }}</td>
                      <td>{{ item.total_quantity }}</td>
                      <td>{{ item.price }}</td>
                      <td>{{ item.line_price }}</td>
                      <td class="text-right">
                        <v-btn
                          fab
                          x-small
                          depressed
                          @click="viewSubmission(item)"
                          class="transparent mr-1"
                        >
                          <v-icon small> mdi-eye </v-icon>
                        </v-btn>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </div>
      </v-row>
    </v-container>

    <!-- Dialogs -->
    <v-dialog v-model="dialogOrder" persistent max-width="600px">
      <v-card :loading="loadingOrder" :disabled="loadingOrder">
        <v-card-title>
          <div class="text-capitalize mb-3">Add Item</div>
        </v-card-title>
        <v-card-text>
          <ValidationObserver ref="order_observer" v-slot="{ valid }">
            <v-form ref="form">
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Non-FoC Quantity"
              >
                <v-autocomplete
                  v-model="orderObj.item"
                  :items="itemList"
                  label="Item"
                  item-text="sku"
                  item-value="id"
                  outlined
                  @click="setItems"
                  @blur="setItems"
                  :loading="loadingItem"
                >
                </v-autocomplete>
              </ValidationProvider>

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

              <!-- <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Location Name"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="locationDialogData.name"
                  label="Location Name"
                  outlined
                  required
                  name="Location Name"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Code"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="locationDialogData.code"
                  label="Code"
                  outlined
                  required
                  name="Code"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider> -->
              <div class="d-flex">
                <v-spacer></v-spacer>
                <v-btn
                  color="primary darken-1"
                  class="mr-2"
                  text
                  @click="dialogOrder = false"
                >
                  cancel
                </v-btn>
                <v-btn
                  class="primary"
                  :loading="loadingOrder"
                  @click="submitOrder"
                  >Submit</v-btn
                >
              </div>
            </v-form>
          </ValidationObserver>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapState, mapStores } from "pinia";
import { useLocationsStore } from "../../../stores/locations";
import { useItemsStore } from "../../../stores/items";
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
      itemList: [],
      loadingItem: false,

      locationList: [],
      loadingLocation: false,
      loadingPage: false,
      loadingOrder: false,
      orderObj: {},
      orderDetails: [],
      dialogOrder: false,
    };
  },
  computed: {
    ...mapState(useLocationsStore, ["location_list"]),
    ...mapStores(useLocationsStore),
    ...mapState(useItemsStore, ["item_list"]),
    ...mapStores(useItemsStore),
  },
  methods: {
    setItems() {
      this.loadingItem = true;
      if (this.itemList.length == 0) {
        this.itemsStore.fetchAllItems().then(() => {
          this.itemList = this.item_list;
          this.loadingItem = false;
          console.log("this.itemList", this.itemList);
        });
      } else {
        this.loadingItem = false;
      }
    },
    addItem() {
      this.dialogOrder = true;
    },
    submitOrder() {
      console.log("submitOrder");
    },
    setLocations() {
      this.loadingLocation = true;
      if (this.locationList.length == 0) {
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
