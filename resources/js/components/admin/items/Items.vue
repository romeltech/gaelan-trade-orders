<template>
  <div>
    <page-title :title="'Items'"></page-title>
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
          <div class="d-flex mb-5">
            <v-btn class="primary mr-2" @click="openItemDialog('new', null)"
              >New Item</v-btn
            >
            <v-btn @click="openImportPage" class="orange white--text"
              >Import Items</v-btn
            >
          </div>
          <v-card>
            <v-card-title>
              <h4>Items</h4>
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
                    <th class="text-left">Item Name</th>
                    <th class="text-left">SKU</th>
                    <th class="text-left">Price</th>
                    <th class="text-left">Unit of Measure</th>
                    <th class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(item_list).length > 0">
                  <tr v-for="item in item_list" :key="item.id">
                    <td>{{ item.name && item.name }}</td>
                    <td>{{ item.sku }}</td>
                    <td>{{ item.price }}</td>
                    <td>{{ item.uom }}</td>
                    <td class="text-right">
                      <v-btn
                        fab
                        x-small
                        depressed
                        @click="openItemDialog('edit', item)"
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
              v-if="Object.keys(item_list).length == 0"
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
    <v-dialog v-model="itemDialog.status" persistent max-width="600px">
      <v-card :loading="loadingItemDialog" :disabled="loadingItemDialog">
        <v-card-title>
          <div class="text-capitalize mb-3">{{ itemDialog.title }} Item</div>
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
                  v-model="itemDialogData.name"
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
                  v-model="itemDialogData.sku"
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
                  v-model="itemDialogData.price"
                  label="Price"
                  outlined
                  required
                  name="Price"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Unit of Measure"
              >
                <v-text-field
                  dense
                  v-model="itemDialogData.uom"
                  label="Unit of Measure"
                  outlined
                  required
                  name="uom"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <div class="d-flex">
                <v-spacer></v-spacer>
                <v-btn
                  color="primary darken-1"
                  class="mr-2"
                  text
                  @click="itemDialog.status = false"
                >
                  cancel
                </v-btn>
                <v-btn
                  :disabled="!valid"
                  :loading="loadingItemDialog"
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
      item_list: [],

      sbOptions: {},
      itemDialog: {
        status: false,
        title: "",
      },
      loadingItemDialog: false,
      itemDialogData: {},
    };
  },
  watch: {
    $route(to, from) {
      this.getPaginatedItems(this.$route.params.page);
    },
  },
  computed: {},
  methods: {
    async saveItem() {
      this.loadingItemDialog = true;
      await axios
        .post("/d/item/save", this.itemDialogData)
        .then((response) => {
          console.log("response.data.message", response.data.message);
          this.getPaginatedItems().then(() => {
            this.sbOptions = {
              status: true,
              type: "success",
              text: response.data.message,
            };
            this.itemDialog.status = false;
            this.$refs.item_observer.reset();
            this.loadingItemDialog = false;
          });
        })
        .catch((err) => {
          console.log(err);
          this.sbOptions = {
            status: true,
            type: "error",
            text: "Error saving data",
          };
          this.loadingItemDialog = false;
        });
    },
    openItemDialog(title, obj = null) {
      this.itemDialogData = {};
      this.itemDialog = {
        status: true,
        title: title,
      };
      if (obj) {
        console.log("obj", obj);
        this.itemDialogData = Object.assign({}, obj);
      }
    },
    openImportPage() {
      this.$router.push({
        name: "ImportItem",
      });
    },
    onPageChange() {
      this.$router.push("/d/items/page/" + this.page).catch((err) => {});
    },
    async getPaginatedItems(page) {
      const response = await axios.get("/d/items/get/all?page=" + page);
      this.item_list = Object.assign([], response.data.data);
      this.page = response.data.current_page;
      this.pageCount = response.data.last_page;
      console.log("this.item_list", this.item_list);
    },
  },
  created() {
    this.getPaginatedItems(this.$route.params.page).then(() => {
      this.pageLoading = false;
    });
  },
};
</script>
