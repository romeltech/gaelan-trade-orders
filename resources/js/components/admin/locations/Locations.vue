<template>
  <div>
    <page-title :title="'Customers'"></page-title>
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
          <div class="d-flex mb-5" v-if="auth_user.role === 'admin'">
            <v-btn class="primary mr-2" @click="openlocationDialog('new', null)"
              >New Customer</v-btn
            >
            <v-btn @click="openImportPage" class="orange white--text"
              >Import Customers</v-btn
            >
          </div>
          <v-card>
            <v-card-title>
              <h4>Customer</h4>
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
                    <th class="text-left">Customer Name</th>
                    <th class="text-left">Area</th>
                    <th class="text-left">Code</th>
                    <th class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(location_list).length > 0">
                  <tr v-for="item in location_list" :key="item.id">
                    <td>{{ item.name }}</td>
                    <td>{{ item.area }}</td>
                    <td>{{ item.code }}</td>
                    <td class="text-right">
                      <v-btn
                        icon
                        x-small
                        :disabled="auth_user.role === 'manager'"
                        @click="openlocationDialog('edit', item)"
                        class="transparent mr-1"
                      >
                        <v-icon> mdi-pencil </v-icon>
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <div
              v-if="Object.keys(location_list).length == 0"
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
    <v-dialog v-model="locationDialog.status" persistent max-width="600px">
      <v-card
        :loading="loadingLocationDialog"
        :disabled="loadingLocationDialog"
      >
        <v-card-title>
          <div class="text-capitalize mb-3">
            {{ locationDialog.title }} Customer
          </div>
        </v-card-title>
        <v-card-text>
          <ValidationObserver ref="location_observer" v-slot="{ valid }">
            <v-form ref="form">
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Customer Name"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="locationDialogData.name"
                  label="Customer Name"
                  outlined
                  required
                  name="Customer Name"
                  :error-messages="errors"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="Area"
              >
                <v-text-field
                  dense
                  type="text"
                  v-model="locationDialogData.area"
                  label="Area"
                  outlined
                  required
                  name="Area"
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
              </ValidationProvider>
              <div class="d-flex">
                <v-spacer></v-spacer>
                <v-btn
                  color="primary darken-1"
                  class="mr-2"
                  text
                  @click="locationDialog.status = false"
                >
                  cancel
                </v-btn>
                <v-btn
                  :disabled="!valid"
                  :loading="loadingLocationDialog"
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
      auth_user: this.$store.state.authUser.userObject,
      // Pagination
      pageCount: 0,
      page: this.$route.params.page ? this.$route.params.page : 1,
      itemsPerPage: 10,

      pageLoading: true,
      location_list: [],

      sbOptions: {},
      locationDialog: {
        status: false,
        title: "",
      },
      loadingLocationDialog: false,
      locationDialogData: {},
    };
  },
  watch: {
    $route(to, from) {
      this.getPaginatedLocations(this.$route.params.page);
    },
  },
  methods: {
    async saveItem() {
      this.loadingLocationDialog = true;
      await axios
        .post("/d/location/save", this.locationDialogData)
        .then((response) => {
          console.log("response.data.message", response.data.message);
          this.getPaginatedLocations().then(() => {
            this.sbOptions = {
              status: true,
              type: "success",
              text: response.data.message,
            };
            this.locationDialog.status = false;
            this.$refs.location_observer.reset();
            this.loadingLocationDialog = false;
          });
        })
        .catch((err) => {
          console.log(err);
          this.sbOptions = {
            status: true,
            type: "error",
            text: "Error saving data",
          };
          this.loadingLocationDialog = false;
        });
    },
    openlocationDialog(title, obj = null) {
      this.locationDialogData = {};
      this.locationDialog = {
        status: true,
        title: title,
      };
      if (obj) {
        console.log("obj", obj);
        this.locationDialogData = Object.assign({}, obj);
      }
    },
    openImportPage() {
      this.$router.push({
        name: "ImportLocation",
      });
    },
    onPageChange() {
      this.$router.push("/d/customers/page/" + this.page).catch((err) => {});
    },
    async getPaginatedLocations(page) {
      const response = await axios.get("/d/customers/get/all?page=" + page);
      this.location_list = Object.assign([], response.data.data);
      this.page = response.data.current_page;
      this.pageCount = response.data.last_page;
      console.log("this.location_list", this.location_list);
    },
  },
  created() {
    this.getPaginatedLocations(this.page).then(() => {
      this.pageLoading = false;
    });
  },
};
</script>
