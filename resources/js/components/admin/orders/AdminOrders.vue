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
              <h4>Orders</h4>
              <v-spacer></v-spacer>
              <!-- <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
              ></v-text-field> -->
            </v-card-title>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Order Number</th>
                    <th class="text-left">Location Code</th>
                    <th class="text-left">Submitted by</th>
                    <th class="text-left">Submitted date</th>
                    <th class="text-left">Order Details</th>
                    <th class="text-right">ERP</th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(order_list).length > 0">
                  <tr v-for="item in order_list" :key="item.id">
                    <td>{{ item.order_number }}</td>
                    <td>{{ item.location_id ? item.location.code : "-" }}</td>
                    <td>{{ item.user.profile.full_name }}</td>
                    <td>{{ formatDateHelper(item.created_at) }}</td>
                    <td>
                      <v-btn
                        small
                        color="success"
                        @click="() => downloadCSV(item)"
                        >Download
                        <v-icon small class="ml-1" color="white"
                          >mdi-microsoft-excel</v-icon
                        ></v-btn
                      >
                    </td>
                    <td class="text-right">
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
          //   console.log("response.data.message", response.data.message);
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
          type: "Item",
          sku: i.sku,
          location_code: i.location ? i.location.code : "-",
          non_foc_quantity: i.non_foc_quantity,
          foc_quantity: i.foc_quantity,
          total_quantity: i.total_quantity,
          unit_of_measure: i.uom ? i.uom : "-",
          unit_price: i.price,
          line_price: i.line_price,
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
      this.$router.push("/d/orders/page/" + this.page).catch((err) => {});
    },
    async getPaginatedItems(page) {
      this.loadingOrdersTable = true;
      const response = await axios.get("/orders/get/paginated?page=" + page);
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