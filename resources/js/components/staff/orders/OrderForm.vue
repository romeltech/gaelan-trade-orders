<template>
  <div>
    <v-autocomplete
      v-model="orderData.location_id"
      :items="locationList"
      label="Customer"
      item-text="name"
      item-value="id"
      outlined
      clearable
      @click="setLocations"
      @blur="setLocations"
      :loading="loadingLocation"
    >
    </v-autocomplete>
    <div class="d-flex align-center mb-3">
      <div class="text-subtitle-1 textcolor--text">Order Details</div>
      <v-btn @click="() => openAddItem(null, 'add')" class="secondary mx-3"
        >Add Item</v-btn
      >
    </div>
    <v-simple-table
      v-if="orderObj.order_details && orderObj.order_details.length > 0"
      class="elevation-0"
      style="border: 1px solid #ddd"
    >
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
            <th class="text-left">Remarks</th>
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
            <td>{{ item.remarks }}</td>
            <td class="text-right">
              <v-btn
                icon
                x-small
                depressed
                @click="openAddItem(item, 'edit')"
                class="transparent mr-1"
              >
                <v-icon small color="primary"> mdi-pencil </v-icon>
              </v-btn>
              <v-btn
                icon
                x-small
                depressed
                @click="removeItem(item)"
                class="transparent"
              >
                <v-icon small color="primary"> mdi-trash-can </v-icon>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-sheet
      v-else
      rounded
      class="mx-auto text-center py-3"
      width="100%"
      color="grey lighten-5"
    >
      No orders to display
    </v-sheet>
    <div class="d-flex align-center mt-5" style="width: 100%">
      <v-spacer></v-spacer>
      <v-btn
        color="primary darken-1"
        class="mr-2"
        text
        :loading="loadingSaveLater"
        @click="() => updateOrder('draft', false, true)"
      >
        save as draft
      </v-btn>
      <v-btn
        v-if="orderObj.status == 'draft'"
        class="primary"
        :loading="loadingSubmit"
        @click="() => updateOrder('submitted', false, true)"
        >submit</v-btn
      >
    </div>
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
                  v-model="orderData.item_id"
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
                rules="numeric|alpha_num|min_value:0"
                name="Non-FoC Quantity"
              >
                <v-text-field
                  type="number"
                  outlined
                  v-model="orderData.non_foc_quantity"
                  label="Non-FoC Quantity*"
                  :error-messages="errors"
                  @change="calculate"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="numeric|alpha_num|min_value:0"
                name="FoC Quantity"
              >
                <v-text-field
                  type="number"
                  outlined
                  v-model="orderData.foc_quantity"
                  label="FoC Quantity*"
                  :error-messages="errors"
                  @change="calculate"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required|numeric|min_value:1"
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
                  persistent-hint
                  hint="Total quantity is the sum of FoC and Non-FoC quantities."
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
              <v-text-field
                outlined
                v-model="orderData.remarks"
                label="Remarks"
              ></v-text-field>
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
                  :disabled="!valid"
                  :loading="loadingDialogOrder"
                  @click="addItem"
                  >{{ dialogOrderBtn }}</v-btn
                >
              </div>
            </v-form>
          </ValidationObserver>
        </v-card-text>
      </v-card>
    </v-dialog>
    <confirmation-dialog
      :confOptions="confirmOptions"
      @response="confirmRemove"
    ></confirmation-dialog>
  </div>
</template>

<script>
import store from "../../../store";
import { mapActions, mapGetters, mapState } from "vuex";
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
      dialogOrderBtn: "Add",
      loadingDialogOrder: false,

      loadingSubmit: false,
      loadingSaveLater: false,

      confirmOptions: {},
      toRemove: {},
    };
  },
  watch: {
    orderProp: {
      handler(newVal, oldVal) {
        this.orderObj = Object.assign({}, newVal);
        this.orderDetails = newVal.order_details ? newVal.order_details : [];
        this.orderData.location_id = this.orderObj.location_id;
        if (this.orderObj.location_id == null) {
          this.setLocations();
        }
      },
      deep: true,
      immediate: true,
    },
    orderData: {
      handler(newVal, oldVal) {
        this.calculate();
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    ...mapGetters(["all_item_list", "all_location_list"]),
    ...mapActions(["fetchAllItems", "fetchAllLocations"]),
  },
  methods: {
    confirmRemove() {
      let data = {
        order_detail_id: this.toRemove.id,
      };
      this.confirmOptions.loading = true;
      axios
        .post("/order/detail/remove", data)
        .then((response) => {
          this.$emit("saved", true);
          this.confirmOptions.loading = false;
          this.confirmOptions.status = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    removeItem(item) {
      this.toRemove = Object.assign({}, item);
      this.confirmOptions = {
        title: `Remove ${item.sku}`,
        status: true,
        loading: false,
        msg: `Are you sure you want to remove ${item.sku} from the list?`,
      };
    },
    async updateOrder(status = "draft", emmit = true, redirect = false) {
      if (status == "draft") {
        this.loadingSaveLater = true;
      } else {
        this.loadingSubmit = true;
      }
      let data = {
        order_number: this.$route.params.ordernum,
        status: status,
        location_id: this.orderData.location_id,
      };
      await axios
        .post("/order/update", data)
        .then((response) => {
          this.loadingSaveLater = false;
          this.loadingSubmit = false;
          if (redirect == true) {
            this.$router.push({
              name: "StaffOrders",
              params: {
                status: status,
              },
            });
          }
          if (emmit == true) {
            this.$emit("saved", true);
          }
        })
        .catch((err) => {
          console.log(err);
          this.loadingSaveLater = false;
          this.loadingSubmit = false;
        });
    },
    clearOrderData() {
      this.orderData = {
        ...this.orderData,
        order_number: null,
        order_id: null,
        sku: null,
        item_id: null,
        item_name: null,
        non_foc_quantity: 0,
        foc_quantity: 0,
        total_quantity: 0,
        oum: null,
        price: null,
        line_price: null,
      };
    },
    calculate() {
      // calculate quantity
      this.orderData.total_quantity = Math.abs(
        parseInt(this.orderData.non_foc_quantity) +
          parseInt(this.orderData.foc_quantity)
      );

      // calculate price
      if (this.orderData.price && this.orderData.total_quantity) {
        this.totalPrice =
          parseFloat(this.orderData.price) *
          parseInt(this.orderData.non_foc_quantity);
        this.orderData.line_price = this.totalPrice ? this.totalPrice : null;
      }
    },
    changeItem() {
      // set item data
      let selectedItem = this.itemList.filter(
        (i) => i.id == this.orderData.item_id
      );
      this.orderData.item_id = selectedItem[0].id;
      this.orderData.item_name = selectedItem[0].name;
      this.orderData.sku = selectedItem[0].sku;
      this.orderData.price = selectedItem[0].price;
    },
    async setItems() {
      this.loadingItem = true;
      if (this.all_item_list.length == 0) {
        await store.dispatch("fetchAllItems").then(() => {
          this.itemList = this.all_item_list;
          this.loadingItem = false;
        });
      } else {
        this.loadingItem = false;
      }
    },
    openAddItem(item = null, action) {
      if (action == "add") {
        this.clearOrderData();
        this.dialogOrderBtn = "Add";
        this.dialogOrder = true;
      } else {
        this.dialogOrderBtn = "Update";
        this.dialogOrder = true;
        this.loadingDialogOrder = true;
        this.setItems().then(() => {
          this.orderData = item;
          this.orderData.item_id = item.item_id;
          this.loadingDialogOrder = false;
        });
      }
    },
    addItem() {
      if (
        this.orderProp.location_id == null &&
        this.orderData.location_id == null
      ) {
        this.saveItem();
      } else {
        if (this.orderProp.location_id == this.orderData.location_id) {
          this.saveItem();
        } else {
          this.loadingDialogOrder = true;
          this.updateOrder(this.orderProp.status, false).then(() => {
            this.saveItem();
          });
        }
      }
    },
    async saveItem() {
      this.loadingDialogOrder = true;
      this.orderData.order_number = this.$route.params.ordernum;
      this.orderData.order_id = this.orderObj.id;
      await axios
        .post("/staff/order/add-item", this.orderData)
        .then((response) => {
          this.dialogOrder = false;
          this.loadingDialogOrder = false;
          this.clearOrderData();
          this.$refs.order_observer.reset();
          this.$emit("saved", true);
        })
        .catch((err) => {
          console.log(err);
          this.loadingDialogOrder = false;
        });
    },
    async setLocations() {
      if (this.all_location_list.length == 0) {
        this.loadingLocation = true;
        await store.dispatch("fetchAllLocations").then(() => {
          this.locationList = this.all_location_list;
          this.loadingLocation = false;
        });
      } else {
        this.locationList = this.all_location_list;
      }
    },
  },
  created() {
    this.loadingPage = false;
  },
};
</script>
