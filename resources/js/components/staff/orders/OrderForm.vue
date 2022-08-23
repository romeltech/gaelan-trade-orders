<template>
  <div>
    <div class="mb-2">
      <div class="text-subtitle-1 textcolor--text mb-2">Cash Sales</div>
      <v-switch
        class="ma-0"
        inset
        style="max-width: 120px"
        v-model="switchCashSales"
        :color="`${switchCashSales == true ? 'success' : 'grey'}`"
        :label="`${switchCashSales == true ? 'Yes' : 'No'}`"
      ></v-switch>
    </div>

    <div class="text-subtitle-1 textcolor--text mb-2">Customer</div>
    <v-text-field
      v-if="switchCashSales == true"
      outlined
      v-model="orderData.cash_sale_customer"
      label="Input Customer"
    ></v-text-field>
    <v-autocomplete
      v-else
      v-model="orderData.location_id"
      :items="locationList"
      label="Select Customer"
      item-text="name"
      item-value="id"
      outlined
      clearable
      @click="setLocations"
      @blur="setLocations"
      :loading="loadingLocation"
    >
    </v-autocomplete>

    <div class="">
      <div class="text-subtitle-1 textcolor--text mb-2">Order Details</div>
      <div class="d-flex align-center flex-wrap">
        <v-btn
          @click="() => openAddItem(null, 'add')"
          class="secondary mr-3 mb-3"
          >Add Item</v-btn
        >
        <v-btn
          @click="() => addAttachment()"
          class="open-uploader secondary mr-3 mb-3"
          ><v-icon small class="mr-1">mdi-paperclip</v-icon> Attachment</v-btn
        >
        <!-- <div class="d-flex align-center">
          <a href="#" target="_blank" class="mr-1 text-decoration-none"
            >Preview Attachment</a
          >
          <v-icon small color="primary">mdi-open-in-new</v-icon>
        </div> -->
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
    </div>
    <v-simple-table
      v-if="orderObj.order_details && orderObj.order_details.length > 0"
      class="elevation-0 gm-item-table"
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
            <td class="text-right" style="width: 60px">
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
          <div class="text-capitalize mb-3">{{ dialogOrderBtn }} Item</div>
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
                  label="FoC Quantity"
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
      switchCashSales: false,
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
        non_foc_quantity: 0,
        foc_quantity: 0,
        total_quantity: 0,
        oum: null,
        price: null,
        line_price: null,
        remarks: "",
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
        url: "/r/save/feedback",
        thumbnailWidth: 150,
        thumbnailHeight: 150,
        uploadMultiple: true,
        autoProcessQueue: false,
        maxFiles: 5,
        parallelUploads: 5,
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
    orderProp: {
      handler(newVal, oldVal) {
        this.orderObj = Object.assign({}, newVal);
        this.orderDetails = newVal.order_details ? newVal.order_details : [];
        this.orderData.location_id = this.orderObj.location_id;
        this.switchCashSales = newVal.is_cash_sale;
        this.orderData.cash_sale_customer = newVal.cash_sale_customer;
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
    uploadFunction() {
      this.loading = true;
      if (this.$refs.myVueDropzone.getQueuedFiles().length === 0) {
        this.submit();
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
      // console.log(file);
    },
    removeAllFilesFunction() {
      this.$refs.myVueDropzone.removeAllFiles();
      this.preview = true;
    },
    removedFunction(file, xhr, formData) {
      // console.log(formData);
    },
    sendingFunction(file, xhr, formData) {
      this.feedback.type = this.typeValue;
      this.feedback.item_numbers = JSON.stringify(this.itemNumbersArray);
      formData.append("feedback", JSON.stringify(this.feedback));
      //   console.log("formData", formData);
    },
    uploadSuccessFuntion(files, response) {
      this.sbOptions = {
        status: true,
        type: "success",
        text: response.message,
      };
      this.resetForm();
      this.loading = false;
      this.removeAllFilesFunction();
    },
    uploadErrorFunction(files, message, xhr) {
      this.loading = false;
      this.sbOptions = {
        status: true,
        type: "error",
        text: "Error uploading file(s)",
      };
    },
    dropzoneTemplate() {
      return `<div class="dz-preview dz-file-preview d-flex align-center">
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
      let data = {
        order_number: this.$route.params.ordernum,
        status: status,
        location_id: this.orderData.location_id,
        is_cash_sale: this.switchCashSales,
        cash_sale_customer: this.orderData.cash_sale_customer,
      };
      console.log("updateOrder", data);
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
      this.loadingDialogOrder = true;
      this.updateOrder(this.orderProp.status, false).then(() => {
        this.saveItem();
      });
      //   if (
      //     this.orderProp.location_id == null &&
      //     this.orderData.location_id == null
      //   ) {
      //     console.log("location_id == null");
      //     this.saveItem();
      //   } else {
      //     if (this.orderProp.location_id == this.orderData.location_id) {
      //       this.saveItem();
      //       console.log("orderProp == orderData");
      //     } else {
      //       console.log("orderProp != orderData");
      //       this.loadingDialogOrder = true;
      //       this.updateOrder(this.orderProp.status, false).then(() => {
      //         this.saveItem();
      //       });
      //     }
      //   }
    },
    async saveItem() {
      this.loadingDialogOrder = true;
      this.orderData.order_number = this.$route.params.ordernum;
      this.orderData.order_id = this.orderObj.id;
      console.log("saveItem", this.orderData);
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
.gm-item-table {
  td,
  th {
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
    border: 1px dashed #333333;
    background-color: #eeeeee;
    width: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    // min-height: 200px;
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
