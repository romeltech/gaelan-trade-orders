<template>
  <div>
    <v-autocomplete
      v-model="orderData.location_code"
      :items="locationList"
      label="Location"
      item-text="name"
      item-value="code"
      outlined
      @click="setLocations"
      @blur="setLocations"
      :loading="loadingLocation"
    >
    </v-autocomplete>
    <div class="d-flex align-center mb-3">
      <div class="text-subtitle-1 textcolor--text">Order Details</div>
      <v-btn
        @click="dialogOrder = true"
        class="primary mx-3"
        >Add Item</v-btn
      >
    </div>
    <v-simple-table v-if="orderObj.order_details" class="elevation-0" style="border: 1px solid #ddd">
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
        <tbody v-if="orderObj.order_details.length > 0">
          <tr v-for="(item, index) in orderDetails" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.sku }}</td>
            <!-- <td>{{ item.location_code }}</td> -->
            <td>{{ item.non_foc_quantity }}</td>
            <td>{{ item.foc_quantity }}</td>
            <td>{{ item.total_quantity }}</td>
            <td>{{ item.price }}</td>
            <td>{{ item.line_price }}</td>
            <td class="text-right">
              <v-btn
                icon
                x-small
                depressed
                @click="editOrder(item)"
                class="transparent mr-1"
              >
                <v-icon small color="primary"> mdi-pencil </v-icon>
              </v-btn>
              <v-btn
                icon
                x-small
                depressed
                @click="removeOrder(item)"
                class="transparent"
              >
                <v-icon small color="primary"> mdi-trash-can </v-icon>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
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
                name="Item SKU"
              >
                <v-autocomplete
                  v-model="orderData.item"
                  :items="itemList"
                  label="Item SKU"
                  item-text="sku"
                  item-value="id"
                  outlined
                  @click="setItems"
                  @blur="setItems"
                  @change="changeItem"
                  :loading="loadingItem"
                >
                </v-autocomplete>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Item Name"
              >
                <v-text-field
                  readonly
                  outlined
                  v-model="orderData.item_name"
                  label="Item Name*"
                  :error-messages="errors"
                  required
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules=""
                name="Non-FoC Quantity"
              >
                <v-text-field
                  type="number"
                  outlined
                  v-model="orderData.non_foc_quantity"
                  label="Non-FoC Quantity*"
                  :error-messages="errors"
                  @change="calculateTotalQuantity"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules=""
                name="FoC Quantity"
              >
                <v-text-field
                  type="number"
                  outlined
                  v-model="orderData.foc_quantity"
                  label="FoC Quantity*"
                  :error-messages="errors"
                  @change="calculateTotalQuantity"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Total Quantity"
              >
                <v-text-field
                  readonly
                  type="number"
                  outlined
                  v-model="orderData.total_quantity"
                  label="Total Quantity*"
                  :error-messages="errors"
                  required
                ></v-text-field>
              </ValidationProvider>
              <!-- <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Unit of Measurement"
              >
                <v-text-field
                  type="text"
                  outlined
                  v-model="orderData.total_quantity"
                  label="Unit of Measurement*"
                  :error-messages="errors"
                  required
                ></v-text-field>
              </ValidationProvider> -->
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Unit Price Excl. VAT"
              >
                <v-text-field
                  readonly
                  type="number"
                  outlined
                  v-model="orderData.price"
                  label="Unit Price Excl. VAT"
                  :error-messages="errors"
                  required
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Line Amount Excl. VAT"
              >
                <v-text-field
                  readonly
                  type="number"
                  outlined
                  v-model="orderData.line_price"
                  label="Line Amount Excl. VAT*"
                  :error-messages="errors"
                  required
                ></v-text-field>
              </ValidationProvider>
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
                  :loading="loadingDialogOrder"
                  @click="addItem"
                  >Add</v-btn
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
  props: {
    orderProp: {
      type: Object,
      default: null,
    },
  },
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
      orderDetails: [],
      orderData: {
        location_code: null,
        order_id: null,
        order_number: null,
        sku: null,
        item_name: null,
        non_foc_quantity: null,
        foc_quantity: null,
        total_quantity: null,
        oum: null,
        price: null,
        line_price: null,
      },
      orderObj: {},
      totalPrice: null,
      dialogOrder: false,
      loadingDialogOrder: false,
    };
  },
  watch: {
    orderProp: {
      handler(newVal, oldVal) {
        this.orderObj = newVal;
        this.orderDetails = newVal.order_details ? newVal.order_details : [];
        if (this.orderObj.location_code == null) {
          this.setLocations();
        }
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    ...mapState(useLocationsStore, ["location_list"]),
    ...mapStores(useLocationsStore),
    ...mapState(useItemsStore, ["item_list"]),
    ...mapStores(useItemsStore),
  },
  methods: {
    clearOrderData() {
      this.$refs.order_observer.reset();
      this.orderData = {
        location_code: null,
        order_number: null,
        order_id: null,
        sku: null,
        item_name: null,
        non_foc_quantity: null,
        foc_quantity: null,
        total_quantity: null,
        oum: null,
        price: null,
        line_price: null,
      };
    },
    removeOrder() {},
    calculateTotalQuantity() {
      this.orderData.total_quantity = Math.abs(
        parseInt(this.orderData.non_foc_quantity) +
          parseInt(this.orderData.foc_quantity)
      );

      if (this.orderData.price && this.orderData.total_quantity) {
        this.totalPrice =
          parseFloat(this.orderData.price) *
          parseInt(this.orderData.total_quantity);
        this.orderData.line_price = this.totalPrice;
      }
    },
    changeItem() {
      let selectedItem = this.itemList.filter(
        (i) => i.id == this.orderData.item
      );
      selectedItem = selectedItem[0];
      // set item data
      this.orderData.item_name = selectedItem.name;
      this.orderData.sku = selectedItem.sku;
      this.orderData.price = selectedItem.price;
    },
    setItems() {
      this.loadingItem = true;
      if (this.itemList.length == 0) {
        this.itemsStore.fetchAllItems().then(() => {
          this.itemList = this.item_list;
          this.loadingItem = false;
        });
      } else {
        this.loadingItem = false;
      }
    },
    editOrder(item) {
      this.dialogOrder = true;
      this.orderData = item;
      console.log("editOrder", this.orderData);
    },
    async addItem() {
      this.loadingDialogOrder = true;
      this.orderData.order_number = this.$route.params.ordernum;
      this.orderData.order_id = this.orderObj.id;
      await axios
        .post("/staff/order/add-item", this.orderData)
        .then((response) => {
          this.dialogOrder = false;
          this.loadingDialogOrder = false;
          this.$emit("saved", true);
        })
        .catch((err) => {
          console.log(err);
          this.loadingDialogOrder = false;
        });
    },
    setLocations() {
      this.loadingLocation = true;
      if (this.locationList.length == 0) {
        this.locationsStore.fetchAllLocations().then(() => {
          this.locationList = this.location_list;
          this.loadingLocation = false;
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
