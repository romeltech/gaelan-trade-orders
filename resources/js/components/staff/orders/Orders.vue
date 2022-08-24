<template>
  <div>
    <page-title :title="$route.params.status"></page-title>
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
          <v-btn
            x-large
            class="primary mb-5"
            :loading="loadingCreateOrder"
            @click="createOrder"
            >Create Order</v-btn
          >
          <v-card :loading="loadingOrderList" :disabled="loadingOrderList">
            <v-card-title>
              <h4 class="text-capitalize">
                {{ $route.params.status + " Orders" }}
              </h4>
              <v-spacer></v-spacer>
            </v-card-title>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Order Number</th>
                    <th class="text-left">Cash Sales</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Submitted by</th>
                    <th class="text-left">Updated date</th>
                    <th class="text-left">Order Details</th>
                    <th
                      class="text-right"
                      v-if="$route.params.status != 'completed'"
                    >
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(order_list).length > 0">
                  <tr v-for="item in order_list" :key="item.id">
                    <td>{{ item.order_number }}</td>
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
                    <td
                      class="text-right"
                      v-if="$route.params.status != 'completed'"
                    >
                      <v-btn
                        fab
                        x-small
                        depressed
                        @click="openOrder(item)"
                        class="transparent mr-1"
                      >
                        <v-icon small> mdi-pencil </v-icon>
                      </v-btn>
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
      loadingOrderList: false,

      sbOptions: {},
      orderDialog: {
        status: false,
        title: "",
      },
      loadingOrderDialog: false,
      orderDialogData: {},
      loadingCreateOrder: false,
    };
  },
  watch: {
    $route(to, from) {
      this.getPaginatedItems(this.$route.params.page);
    },
  },
  methods: {
    printCustomer(item) {
      let customer = "";
      if (item) {
        if (item.is_cash_sale == true) {
          customer = item.cash_sale_customer ? item.cash_sale_customer : "-";
        } else {
          customer = item.location_id ? item.location.code : "-";
        }
      }
      return customer;
    },
    async createOrder() {
      this.loadingCreateOrder = true;
      await axios
        .post("/staff/order/create")
        .then((response) => {
          this.loadingCreateOrder = false;
          this.$router.push({
            name: "EditOrder",
            params: {
              ordernum: response.data.order_number,
            },
          });
        })
        .catch((err) => {
          console.log(err);
          this.loadingCreateOrder = false;
        });
    },

    downloadCSV(item) {
      // setup json
      let rawJson = [];
      item.order_details.map((i) => {
        rawJson.push({
          type: "Item",
          sku: i.sku,
          customer_code: item.location ? item.location.code : "-",
          non_foc_quantity: i.non_foc_quantity,
          foc_quantity: i.foc_quantity,
          total_quantity: i.total_quantity,
          unit_of_measure: i.uom ? i.uom : "-",
          unit_price: i.price,
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
    openOrder(obj) {
      this.$router.push({
        name: "EditOrder",
        params: {
          ordernum: obj.order_number,
        },
      });
    },
    openImportPage() {
      this.$router.push({
        name: "ImportItem",
      });
    },
    onPageChange() {
      this.$router
        .push(
          "/staff/orders/" + this.$route.params.status + "/page/" + this.page
        )
        .catch((err) => {});
    },
    async getPaginatedItems(page) {
      this.loadingOrderList = true;
      const response = await axios.get(
        "/orders/get/paginated/" + this.$route.params.status + "?page=" + page
      );
      if (response.data) {
        this.loadingOrderList = false;
      }
      this.order_list = Object.assign([], response.data.data);
      this.page = response.data.current_page;
      this.pageCount = response.data.last_page;
      //   console.log("this.order_list", this.order_list);
    },
  },
  created() {
    this.getPaginatedItems(this.$route.params.page).then(() => {
      this.pageLoading = false;
    });
    console.log(this.$route.params.status);
  },
};
</script>
