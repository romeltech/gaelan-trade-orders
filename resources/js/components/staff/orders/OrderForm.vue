<template>
  <div>
    <div class="d-flex align-flex-start flex-wrap">
      <div style="width: 120px; margin-right: 5px">
        <div class="text-subtitle-1 textcolor--text mb-2">Cash Sales</div>
        <v-switch
          class="ma-0 pt-4"
          inset
          style="max-width: 120px"
          v-model="switchCashSales"
          :color="`${switchCashSales == true ? 'success' : 'grey'}`"
          :label="`${switchCashSales == true ? 'Yes' : 'No'}`"
        ></v-switch>
      </div>
      <div class="gm-customer-name">
        <div class="text-subtitle-1 textcolor--text mb-2">Customer</div>
        <v-text-field
          v-if="switchCashSales == true"
          outlined
          v-model="orderData.cash_sale_customer"
          label="Input"
        ></v-text-field>
        <v-autocomplete
          v-else
          v-model="orderData.location_id"
          :items="locationList"
          label="Select"
          item-text="name"
          item-value="id"
          outlined
          clearable
          @click="setLocations"
          @blur="setLocations"
          :loading="loadingLocation"
        >
        </v-autocomplete>
      </div>
    </div>
    <v-divider class="mb-5"></v-divider>
    <div class="text-subtitle-1 textcolor--text mb-2">Order Details</div>
    <div class="d-flex align-center flex-wrap">
      <v-btn @click="() => openAddItem(null, 'add')" class="secondary mr-3 mb-3"
        >Add Item</v-btn
      >
    </div>
    <v-simple-table
      v-if="orderObj.order_details && orderObj.order_details.length > 0"
      class="elevation-0 gm-item-table mb-5"
      style="border: 1px solid #ddd"
    >
      <!-- dense -->
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">SKU</th>
            <!-- <th class="text-left">Item Name</th> -->
            <th class="text-left">Non-FoC Quantity</th>
            <th class="text-left">FoC Quantity</th>
            <th class="text-left">Total Quantity</th>
            <th class="text-left">
              Unit of Measure <br />
              <div style="font-size: 10px; line-height: 10px">Code</div>
            </th>
            <th class="text-left">
              Unit Price <br />
              <div style="font-size: 10px; line-height: 10px">Excl. VAT</div>
            </th>
            <th class="text-left">
              Line Amount <br />
              <div style="font-size: 10px; line-height: 10px">Excl. VAT</div>
            </th>
            <th class="text-left">Remarks</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody v-if="orderObj.order_details.length > 0">
          <tr v-for="(item, index) in orderDetails" :key="item.id">
            <td>{{ item.sku }}</td>
            <td>{{ item.non_foc_quantity }}</td>
            <td>{{ item.foc_quantity }}</td>
            <td>{{ item.total_quantity }}</td>
            <td>{{ item.uom }}</td>
            <td>{{ item.price }}</td>
            <td>{{ item.line_price }}</td>
            <td>{{ item.remarks }}</td>
            <td class="text-right" style="min-width: 60px; width: 60px">
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
      class="mx-auto text-center py-5 mb-5"
      width="100%"
      color="grey lighten-5"
    >
      No orders to display
    </v-sheet>

    <div class="mb-5">
      <div class="text-subtitle-1 textcolor--text mb-2">Instructions</div>
      <v-textarea
        outlined
        hide-details
        v-model="orderData.instructions"
        rows="3"
        label="Optional"
      ></v-textarea>
    </div>

    <div class="text-subtitle-1 textcolor--text mb-2">Attachment</div>
    <div class="d-flex align-end flex-wrap" style="width: 100%">
      <div>
        <div class="d-flex align-center">
          <v-btn
            :disabled="!has_order"
            @click="() => addAttachment()"
            class="open-uploader primary mr-3"
            ><v-icon small class="mr-1">mdi-paperclip</v-icon> Add</v-btn
          >
          <div v-if="selectedFile == null">
            <v-btn
              v-if="orderData.files && orderData.files.length == 1"
              class="mr-3"
              color="error"
              @click="removeAttachment"
            >
              <v-icon small class="mr-1">mdi-close</v-icon> remove
            </v-btn>
            <v-btn
              v-if="
                orderData.files &&
                orderData.files.length == 0 &&
                orderObj.files.length > 0
              "
              color="primary"
              icon
              @click="undoRemoveAttachment"
            >
              <v-icon small>mdi-undo</v-icon>
            </v-btn>
          </div>
        </div>
        <div v-if="selectedFile == null" class="mb-3">
          <div v-if="!orderData.files" class="mt-3">No Attachment</div>
          <div v-else class="d-flex align-center mt-3">
            <a
              v-if="orderData.files && orderData.files[0]"
              :href="`${$baseUrl}/file/${orderData.files[0].path}`"
              target="_blank"
              style="font-size: 16px"
            >
              {{ orderData.files[0].path }}
            </a>
            <a
              v-if="orderData.files && orderData.files[0]"
              :href="`${$baseUrl}/file/${orderData.files[0].path}`"
              target="_blank"
              class="text-decoration-none"
            >
              <v-icon color="primary" class="ml-1">mdi-open-in-new</v-icon>
            </a>
          </div>
        </div>
        <vue-dropzone
          class="file-upload"
          ref="myVueDropzone"
          id="dropzone"
          :options="dropzoneOptions"
          :duplicateCheck="true"
          :include-styling="false"
          :useCustomSlot="preview"
          v-on:vdropzone-file-added="addedFunction"
          v-on:vdropzone-files-added="addedFunction"
          v-on:vdropzone-sending="sendingFunction"
          v-on:vdropzone-drop="dropFunction"
          v-on:vdropzone-success-multiple="uploadSuccessFuntion"
          v-on:vdropzone-removed-file="removedFunction"
          v-on:vdropzone-processingFunction-multiple="processingFunction"
          v-on:vdropzone-error-multiple="uploadErrorFunction"
          v-on:vdropzone-duplicate-file="duplicateFileFunction"
        >
        </vue-dropzone>
      </div>
      <v-spacer></v-spacer>
      <div class="mb-3 mt-10 mt-md-auto">
        <v-btn
          class="mr-2 grey lighten-4 primary--text"
          depressed
          :loading="loadingSaveLater"
          @click="() => updateOrder('draft', false, true)"
        >
          save as draft
        </v-btn>
        <v-btn
          v-if="orderObj.status == 'draft'"
          :disabled="!has_order"
          class="primary"
          :loading="loadingSubmit"
          @click="() => updateOrder('submitted', false, true)"
          >submit</v-btn
        >
      </div>
    </div>
    <!-- Dialogs -->
    <v-dialog v-model="dialogOrder" persistent max-width="600px">
      <v-card :loading="loadingOrder" :disabled="loadingOrder">
        <v-card-title>
          <div class="text-capitalize mb-3">{{ dialogOrderBtn }} Item</div>
        </v-card-title>
        <v-card-text>
          <ValidationObserver ref="order_observer" v-slot="{ valid }">
            <v-form ref="form">
              <v-autocomplete
                ref="search_item_list"
                v-model="selectedFromSearch"
                :items="searchItemList"
                :loading="loadingSearch"
                :search-input.sync="toSearch"
                clearable
                hide-details
                hide-no-data
                item-text="search_name"
                item-value="id"
                label="Search SKU or Item Name"
                class="mb-7"
                outlined
                @change="changeSelected"
                return-object
                append-icon="mdi-maginify"
              >
                <template v-slot:selection="{ attr, on, item, selected }">
                  <v-chip
                    v-bind="attr"
                    :input-value="selected"
                    color="primary"
                    class="white--text"
                    v-on="on"
                  >
                    <span>{{ item.name }}</span>
                  </v-chip>
                </template>
              </v-autocomplete>
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

              <div class="row">
                <div class="col-12 col-md-6 pb-0 pt-2">
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="required"
                    name="Item SKU"
                  >
                    <v-text-field
                      readonly
                      outlined
                      v-model="orderData.sku"
                      label="Item SKU*"
                      :error-messages="errors"
                      required
                    ></v-text-field>
                  </ValidationProvider>
                </div>
                <div class="col-12 col-md-6 pb-0 pt-2">
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="required"
                    name="Unit of Measure Code"
                  >
                    <v-text-field
                      readonly
                      outlined
                      v-model="orderData.uom"
                      label="Unit of Measure Code*"
                      :error-messages="errors"
                      required
                    ></v-text-field>
                  </ValidationProvider>
                </div>
                <div class="col-12 col-md-6 pt-0 pb-2">
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
                </div>
                <div class="col-12 col-md-6 pt-0 pb-2">
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="numeric|alpha_num|min_value:0"
                    name="FoC Quantity"
                  >
                    <v-text-field
                      type="number"
                      outlined
                      v-model="orderData.foc_quantity"
                      label="FoC Quantity"
                      :error-messages="errors"
                      @change="calculate"
                    ></v-text-field>
                  </ValidationProvider>
                </div>
              </div>
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
import vueDropzone from "vue2-dropzone";
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
    vueDropzone,
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      searchItemList: [],
      loadingSearch: false,
      toSearch: "",
      selectedFromSearch: null,

      selectedFile: null,
      switchCashSales: false,
      itemList: [],
      loadingItem: false,
      locationList: [],
      loadingLocation: false,
      loadingPage: false,
      loadingOrder: false,
      orderDetails: [],
      orderData: {
        files: [],
        location_code: null,
        order_id: null,
        order_number: null,
        sku: null,
        item_name: null,
        non_foc_quantity: 0,
        foc_quantity: 0,
        total_quantity: 0,
        oum: null,
        price: null,
        line_price: null,
        remarks: "",
        instructions: "",
        remove_file: false,
        order_detail_id: null,
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

      // dropzone
      preview: true,
      dropzoneOptions: {
        url: "/order/update",
        thumbnailWidth: 150,
        thumbnailHeight: 150,
        uploadMultiple: true,
        autoProcessQueue: false,
        maxFiles: 1,
        parallelUploads: 1,
        maxFilesize: 5,
        timeout: 180000,
        acceptedFiles: ".jpeg,.jpg,.png,.jfif,.pdf",
        previewTemplate: this.dropzoneTemplate(),
        clickable: ".open-uploader",
        headers: {
          "x-csrf-token": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      },
    };
  },
  watch: {
    toSearch(val) {
      if (val && val.length > 3) {
        this.loadingSearch = true;
        let data = {
          keyword: val,
        };
        // this.$refs.search_item_list.isMenuActive = false;
        axios
          .post("/item/search", data)
          .then((res) => {
            this.loadingSearch = false;
            this.searchItemList = Object.assign([], res.data);
            this.searchItemList.map((si) => {
              si.search_name = si.name + " (" + si.sku + ")";
            });
            // this.$refs.search_item_list.isMenuActive = true;
            // console.log(
            //   "isMenuActive",
            //   this.$refs.search_item_list.isMenuActive
            // );
            // console.log("search results", this.searchItemList);
          })
          .catch((err) => {
            this.loadingSearch = false;
            console.error(err);
          });
      }
    },
    orderProp: {
      handler(newVal, oldVal) {
        this.orderObj = Object.assign({}, newVal);
        // this.orderData = Object.assign({}, newVal);
        this.orderDetails = newVal.order_details ? newVal.order_details : [];
        this.switchCashSales = newVal.is_cash_sale;

        this.orderData.instructions = this.orderObj.instructions;
        this.orderData.cash_sale_customer = newVal.cash_sale_customer;
        this.orderData.files = newVal.files;
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
    has_order() {
      return this.orderObj.order_details &&
        this.orderObj.order_details.length < 1
        ? false
        : true;
    },
    ...mapGetters(["all_item_list", "all_location_list"]),
    ...mapActions(["fetchAllItems", "fetchAllLocations"]),
  },
  methods: {
    undoRemoveAttachment() {
      this.orderData.files = this.orderObj.files;
      console.log("undoRemoveAttachment", this.orderData);
    },
    removeAttachment() {
      console.log("before", this.orderData.files);
      this.orderData.files = [];
      this.selectedFile = null;
      console.log("after", this.orderData.files);
    },
    changeSelected() {
      if (this.selectedFromSearch) {
        this.orderData.item_id = this.selectedFromSearch.id;
        this.orderData.item_name = this.selectedFromSearch.name;
        this.orderData.uom = this.selectedFromSearch.uom;
        this.orderData.sku = this.selectedFromSearch.sku;
        this.orderData.price = this.selectedFromSearch.price;
        console.log("orderData", this.orderData);
      }
    },
    uploadFunction() {
      this.loading = true;
      if (this.$refs.myVueDropzone.getQueuedFiles().length === 0) {
        this.updateOrder();
      } else {
        this.$refs.myVueDropzone.processQueue();
      }
    },
    processingFunction() {
      this.sbOptions = {
        snackbar: false,
        type: "",
        text: "",
      };
      this.loading = true;
    },
    duplicateFileFunction(e) {
      console.log(
        "Duplicated file will not be inserted in upload queue: " + e.name
      );
    },
    dropFunction(e) {
      e.preventDefault();
      // console.log(e);
    },
    addedFunction(file) {
      this.selectedFile = true;
      // console.log(file);
    },
    removeAllFilesFunction() {
      this.$refs.myVueDropzone.removeAllFiles();
      this.preview = true;
      this.selectedFile = null;
    },
    removedFunction(file, xhr, formData) {
      // console.log(formData);
      this.selectedFile = null;
    },
    sendingFunction(file, xhr, formData) {
      formData.append("order", JSON.stringify(this.orderData));
      //   console.log("formData", formData);
    },
    uploadSuccessFuntion(files, response) {
      this.sbOptions = {
        status: true,
        type: "success",
        text: response.message,
      };
      this.loadingSaveLater = false;
      this.loadingSubmit = false;
      this.removeAllFilesFunction();
      this.clearOrderData();
      this.$emit("saved", true);
      this.$router.push({
        name: "StaffOrders",
        params: {
          status: "draft",
        },
      });
    },
    uploadErrorFunction(files, message, xhr) {
      this.loadingSaveLater = false;
      this.loadingSubmit = false;
      this.sbOptions = {
        status: true,
        type: "error",
        text: "Error uploading file(s)",
      };
    },
    dropzoneTemplate() {
      return `<div class="dz-preview mt-3 mb-3 dz-file-preview d-flex align-center">
                <div class="dz-details d-flex align-center justify-start mr-3" style="width: 100%">
                  <div class="px-1 d-flex align-center" style="width: 100%">
                    <div class="dz-filename mr-2" data-dz-name></div>
                    <div class="dz-size mr-1" data-dz-size></div>
                    <div class="error--text" data-dz-errormessage></div>
                  </div>
                  <div class="dz-progress d-flex align-center justify-center caption">
                    <span class="dz-upload" data-dz-uploadprogress></span>
                  </div>
                </div>
                <v-spacer></v-spacer>
                <button
                  data-dz-remove
                  type="button"
                  class="dz-remove-text mr-auto v-btn v-btn--flat theme--light error--text"
                >
                  <span class="v-btn__content">
                   remove
                  </span>
                </button>
              </div>`;
    },
    addAttachment() {
      console.log("attachment");
    },
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
      this.orderData = {
        ...this.orderData,
        order_number: this.$route.params.ordernum,
        status: status,
        location_id: this.orderData.location_id,
        is_cash_sale: this.switchCashSales,
        cash_sale_customer: this.orderData.cash_sale_customer,
        instructions: this.orderData.instructions,
      };
      if (this.$refs.myVueDropzone.getQueuedFiles().length > 0) {
        console.log("has File");
        this.$refs.myVueDropzone.processQueue();
      } else {
        console.log("no File");
        if (this.orderData.files.length == 0) {
          this.orderData = {
            ...this.orderData,
            remove_file: true,
          };
        }
        await axios
          .post("/order/update", this.orderData)
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
      }
    },
    clearOrderData() {
      this.orderData = {
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
        remarks: "",
        instructions: "",
        remove_file: false,
        files: this.orderObj.files,
        order_detail_id: null,
      };
      setTimeout(() => {
        this.selectedFromSearch = null;
      }, 500);
    },
    calculate() {
      // calculate quantity
      this.orderData.foc_quantity = this.orderData.foc_quantity
        ? this.orderData.foc_quantity
        : 0;

      this.orderData.non_foc_quantity = this.orderData.non_foc_quantity
        ? this.orderData.non_foc_quantity
        : 1;

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
    // async setItems() {
    //   this.loadingItem = true;
    //   console.log("this.all_item_list.length", this.all_item_list.length);
    //   if (this.all_item_list.length == 0) {
    //     await store.dispatch("fetchAllItems").then(() => {
    //       this.itemList = this.all_item_list;
    //       this.loadingItem = false;
    //     });
    //   } else {
    //     this.itemList = this.all_item_list;
    //     this.loadingItem = false;
    //   }
    // },
    openAddItem(item = null, action) {
      if (action == "add") {
        this.dialogOrderBtn = "Add";
        this.dialogOrder = true;
        this.orderData = {
          ...this.orderData,
          sku: null,
          item_id: null,
          item_name: null,
          uom: null,
          non_foc_quantity: null,
          foc_quantity: null,
          price: null,
          remarks: null,
          order_detail_id: null,
        };
      } else if (action == "edit") {
        this.dialogOrderBtn = "Update";
        this.dialogOrder = true;
        this.orderData = {
          ...this.orderData,
          sku: item.sku,
          item_id: item.item_id,
          item_name: item.item_name,
          uom: item.uom,
          non_foc_quantity: item.non_foc_quantity,
          foc_quantity: item.foc_quantity,
          price: item.price,
          remarks: item.remarks,
          order_detail_id: item.id,
        };
      }
    },
    addItem() {
      this.loadingDialogOrder = true;
      this.updateOrder(this.orderProp.status, false).then(() => {
        this.saveItem();
      });
    },
    async saveItem() {
      this.loadingDialogOrder = true;
      this.orderData.order_number = this.$route.params.ordernum;
      this.orderData.order_id = this.orderObj.id;
      await axios
        .post("/staff/order/save/detail", this.orderData)
        .then((response) => {
          this.dialogOrder = false;
          this.loadingDialogOrder = false;
          this.$refs.order_observer.reset();
          this.clearOrderData();
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


<style lang="scss">
.gm-customer-name {
  width: calc(100% - 120px - 5px);
}
@media all and (max-width: 480px) {
  .gm-customer-name {
    width: 100%;
  }
}

.gm-item-table {
  tr:nth-of-type(even) {
    background-color: rgba(0, 0, 0, 0.025);
  }
  td,
  th {
    // font-size: 12px !important;
    min-width: 60px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
  }
}
.loading-sheet {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  left: 0;
  bottom: auto;
  right: auto;
}
.file-upload {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
  -webkit-box-pack: start;
  -ms-flex-pack: start;
  justify-content: flex-start;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  .dz-message {
    display: none !important;
  }
  .dz-preview {
    .dz-details {
      color: #233464;
      .dz-filename {
        font-weight: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
      }
      .dz-size {
        strong {
          font-weight: normal !important;
        }
      }
      img {
        width: 30px;
        height: auto;
      }
    }
    .dz-remove-text {
      font-size: 12px;
    }
    // &.dz-error.dz-complete {
    //     background-color: #ffebee !important;
    // }
  }
}
.drop-wrapper {
  background-color: #fdfdfd;
  .drop-msg {
    position: absolute;
    top: auto;
    left: 0;
    right: 0;
    bottom: 20px;
    width: 100%;
    text-align: center;
  }
  .textfield {
    width: 100%;
    border-top: 1px solid #f1f1f1;
    background-color: #ffffff;
  }
}
</style>
