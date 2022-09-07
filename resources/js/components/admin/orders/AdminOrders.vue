<template>
  <div>
    <page-title :title="'Orders'"></page-title>
    <v-container class="py-8">
      <v-row v-if="pageLoading == true">
        <v-col cols="12">
          <v-skeleton-loader
            class="mx-auto"
            max-width="100%"
            type="list-item-avatar-three-line, image, article"
          ></v-skeleton-loader>
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col cols="12" class="py-5">
          <v-card :loading="loadingOrdersTable" :disabled="loadingOrdersTable">
            <v-card-title>
              <h4 class="text-capitalize">{{ $route.params.status }} Orders</h4>
              <v-spacer></v-spacer>
              <!-- <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
              ></v-text-field> -->
            </v-card-title>
            <v-simple-table class="gm-admin-orders">
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Order Number</th>
                    <th class="text-left">Cash Sales</th>
                    <th class="text-left">Customer Name</th>
                    <th class="text-left">Customer Code</th>
                    <th class="text-left">Submitted by</th>
                    <th class="text-left">Submitted date</th>
                    <th class="text-left">Attachment</th>
                    <th class="text-left">Order Details</th>
                    <th class="text-left">Instructions</th>
                    <th class="text-right">ERP</th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(order_list).length > 0">
                  <tr v-for="item in order_list" :key="item.id">
                    <td style="min-width: 100px !important">
                      {{ item.order_number }}
                    </td>
                    <td>
                      <v-chip
                        small
                        :color="`${
                          item.is_cash_sale == true
                            ? 'success'
                            : 'grey lighten-3'
                        }`"
                        >{{ item.is_cash_sale == true ? "Yes" : "No" }}</v-chip
                      >
                    </td>
                    <td>{{ printCustomer(item) }}</td>
                    <td>{{ printCustomer(item, "code") }}</td>
                    <td>{{ item.user.profile.full_name }}</td>
                    <td>{{ formatDateHelper(item.created_at) }}</td>
                    <td>
                      <div v-if="item.files && item.files.length > 0">
                        <v-btn
                          rounded
                          small
                          color="primary"
                          v-if="item.files[0]"
                          :href="`${$baseUrl}/file/${item.files[0].path}`"
                          target="_blank"
                        >
                          OPEN
                          <v-icon class="ml-1" small color="white"
                            >mdi-open-in-new</v-icon
                          >
                        </v-btn>
                      </div>
                      <div v-else>-</div>
                    </td>
                    <td>
                      <v-btn
                        small
                        rounded
                        color="success"
                        @click="() => downloadCSV(item)"
                        >Download
                        <v-icon small class="ml-1" color="white"
                          >mdi-microsoft-excel</v-icon
                        ></v-btn
                      >
                    </td>
                    <td>
                      <v-btn
                        rounded
                        v-if="item.instructions != ''"
                        small
                        color="primary"
                        @click="() => readInstructions(item)"
                        >Read
                        <v-icon small class="ml-1" color="white"
                          >mdi-eye</v-icon
                        >
                      </v-btn>
                    </td>
                    <td class="text-right" style="min-width: 40px !important">
                      <div>
                        <v-progress-circular
                          v-if="loadingERP[item.id] == true"
                          :size="20"
                          :width="2"
                          color="primary"
                          class="ml-auto"
                          indeterminate
                        ></v-progress-circular>
                        <v-checkbox
                          v-else
                          v-model="item.erp"
                          class="ma-0 ml-auto"
                          hide-details
                          color="success"
                          style="width: 20px"
                          @click="() => updateStatus(item)"
                        ></v-checkbox>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <div
              v-if="Object.keys(order_list).length == 0"
              class="text-center caption text-capitalize py-3"
            >
              Result Not Found
            </div>
          </v-card>
          <v-pagination
            v-if="pageCount > 1"
            class="mt-3"
            v-model="page"
            :length="pageCount"
            @input="onPageChange"
            :total-visible="10"
          ></v-pagination>
        </v-col>
      </v-row>
    </v-container>

    <!-- Dialogs -->
    <v-dialog v-model="orderDialog.status" persistent max-width="600px">
      <v-card :loading="loadingOrderDialog" :disabled="loadingOrderDialog">
        <v-card-title>
          <div class="text-capitalize mb-3">{{ orderDialog.title }} Item</div>
        </v-card-title>
        <v-card-text>
          <ValidationObserver ref="item_observer" v-slot="{ valid }">
            <v-form ref="form">
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Item Name"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="orderDialogData.name"
                  label="Item Name"
                  outlined
                  required
                  name="Item Name"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="SKU"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="orderDialogData.sku"
                  label="SKU"
                  outlined
                  required
                  name="SKU"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Price"
              >
                <v-text-field
                  dense
                  type="number"
                  v-model="orderDialogData.price"
                  label="Price"
                  outlined
                  required
                  name="Price"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <div class="d-flex">
                <v-spacer></v-spacer>
                <v-btn
                  color="primary darken-1"
                  class="mr-2"
                  text
                  @click="orderDialog.status = false"
                >
                  cancel
                </v-btn>
                <v-btn
                  :disabled="!valid"
                  :loading="loadingOrderDialog"
                  color="primary"
                  @click="saveItem"
                >
                  Save
                </v-btn>
              </div>
            </v-form>
          </ValidationObserver>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogStatusInstructions" persistent width="600">
      <v-card>
        <v-card-title class="text-capitalize">Instructions</v-card-title>
        <v-card-text class="text-body-1 textColor--text">{{
          dialogInstructions
        }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="primary ml-2" @click="dialogStatusInstructions = false"
            >Okay</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <snack-bar :snackbar-options="sbOptions"></snack-bar>
  </div>
</template>

<script>
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
      dialogStatusInstructions: false,
      dialogInstructions: "",

      // Pagination
      pageCount: 0,
      page: 1,
      itemsPerPage: 10,

      pageLoading: true,
      order_list: [],

      sbOptions: {},
      orderDialog: {
        status: false,
        title: "",
      },
      loadingOrderDialog: false,
      orderDialogData: {},

      loadingERP: [],

      loadingOrdersTable: false,
    };
  },
  watch: {
    $route(to, from) {
      this.getPaginatedItems(this.$route.params.page);
    },
  },
  methods: {
    readInstructions(item) {
      this.dialogStatusInstructions = true;
      this.dialogInstructions = item.instructions;
    },
    printCustomer(item, field = "name") {
      let customer = "";
      if (item) {
        if (item.is_cash_sale == true) {
          customer = item.cash_sale_customer ? item.cash_sale_customer : "-";
        } else {
          customer =
            item.location_id && field === "name"
              ? item.location.name
              : item.location_id && field == "code"
              ? item.location.code
              : "-";
        }
      }
      return customer;
    },
    async updateStatus(item) {
      console.log("item", item);
      this.loadingERP[item.id] = true;
      let data = {
        order_id: item.id,
        erp: item.erp,
      };
      await axios
        .post("/order/update-erp", data)
        .then((response) => {
          this.getPaginatedItems(this.$route.params.page).then(() => {
            this.sbOptions = {
              status: true,
              type: "success",
              text: response.data.message,
            };
          });
        })
        .catch((err) => {
          console.log(err);
          this.sbOptions = {
            status: true,
            type: "error",
            text: "Error updating order",
          };
          this.loadingERP[item.id] = false;
        });
    },
    downloadCSV(item) {
      // setup json
      let rawJson = [];
      item.order_details.map((i) => {
        rawJson.push({
          customer_code: item.location ? item.location.code : "-",
          type: "Item",
          sku: i.sku,
          description: i.item_name,
          location_code: "",
          non_foc_quantity: i.non_foc_quantity,
          foc_quantity: i.foc_quantity,
          total_quantity: i.total_quantity,
          unit_of_measure: i.uom ? i.uom : "",
          unit_price: i.price,
          line_discount: "",
          line_price: i.line_price,
          remarks: i.remarks,
        });
      });

      // unparse json
      let csv = this.$papa.unparse(rawJson, {
        delimiter: ",",
      });

      // setup csv to download
      this.$papa.download(
        csv,
        item.order_number ? item.order_number : "order-details"
      );
    },
    async saveItem() {
      this.loadingOrderDialog = true;
      await axios
        .post("/d/order/save", this.orderDialogData)
        .then((response) => {
          //   console.log("response.data.message", response.data.message);
          this.getPaginatedItems().then(() => {
            this.sbOptions = {
              status: true,
              type: "success",
              text: response.data.message,
            };
            this.orderDialog.status = false;
            this.$refs.item_observer.reset();
            this.loadingOrderDialog = false;
          });
        })
        .catch((err) => {
          console.log(err);
          this.sbOptions = {
            status: true,
            type: "error",
            text: "Error saving data",
          };
          this.loadingOrderDialog = false;
        });
    },
    onPageChange() {
      this.$router
        .push("/d/orders/" + this.$route.params.status + "/page/" + this.page)
        .catch((err) => {});
    },
    async getPaginatedItems(page) {
      this.loadingOrdersTable = true;
      const response = await axios.get(
        "/d/orders/get/paginated/" + this.$route.params.status + "?page=" + page
      );
      this.order_list = Object.assign([], response.data.data);
      if (response.data) {
        this.loadingOrdersTable = false;
      }
      this.page = response.data.current_page;
      this.pageCount = response.data.last_page;
      this.order_list.map((ol) => {
        this.loadingERP[ol.id] = false;
      });
    },
  },
  created() {
    this.getPaginatedItems(this.$route.params.page).then(() => {
      this.pageLoading = false;
    });
  },
};
</script>

<style lang="scss">
.gm-admin-orders {
  tr,
  th,
  td {
    min-width: 80px;
  }
}
</style>
