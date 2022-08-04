<template>
  <div>
    <page-title :title="'Import Items'"></page-title>
    <v-container class="py-8">
      <v-row>
        <div class="col-12 col-md-7 mx-auto">
          <v-card>
            <v-card-title>Import Items</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pb-10">
              <div class="d-flex align-center">
                <ImportData
                  :route="'/d/import/items'"
                  @responded="importResponse"
                />
                <v-btn
                  class="ml-2"
                  text
                  color="primary"
                  :href="`${$baseUrl}/csv/import-item-template.csv`"
                  download
                  >Download CSV Template
                  <v-icon small class="ml-2">mdi-download</v-icon></v-btn
                >
              </div>
            </v-card-text>
          </v-card>
          <v-alert
            v-model="alertStatus"
            class="pa-5 my-5 elevation-2"
            border="top"
            colored-border
            :color="`${alertType == 'success' ? 'success' : 'red'}`"
            :type="alertType"
          >
            <div>
              {{ alertMessage }}
              <a
                v-if="alertType == 'success'"
                href="#"
                class="ml-1 success--text"
                @click="goToRoute"
                >Suppliers</a
              >
            </div>
          </v-alert>

          <v-card v-if="existedSuppliersArray.length > 0" class="pb-3">
            <v-card-title class="overline">Existed Supplier(s)</v-card-title>
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Suppler</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in existedSuppliersArray"
                    :key="index"
                  >
                    <td>{{ item }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card>
        </div>
        <!-- <div class="col-12 col-md-5">
          <v-card :loading="loadingCompanies">
            <v-card-title>Company Codes</v-card-title>
            <v-divider></v-divider>
            <v-data-table
              dense
              :headers="headers"
              :items="companies"
              item-key="name"
              class="elevation-0"
              :search="search"
              :items-per-page="5"
            >
              <template v-slot:top>
                <v-text-field
                  dense
                  v-model="search"
                  label="Search Company"
                  class="mx-4 pt-5"
                ></v-text-field>
              </template>
            </v-data-table>
          </v-card>
        </div> -->
      </v-row>
    </v-container>
  </div>
</template>

<script>
import ImportData from "../../import/ImportData.vue";
export default {
  components: {
    ImportData,
  },
  data() {
    return {
      loadingCompanies: false,
      search: "",
      companies: [],
      headers: [
        {
          text: "Supplier name",
          align: "start",
          sortable: false,
          value: "name",
        },
      ],
      alertStatus: false,
      alertMessage: "",
      alertType: "info",
      existedSuppliersArray: [],
    };
  },
  methods: {
    importResponse(res) {
      console.log(res);
      //   this.alertStatus = true;
      //   this.alertMessage = res.alertData.message;
      //   this.alertType = res.alertData.type;
      //   this.existedSuppliersArray = res.existed_data ? res.existed_data : [];
    },
    goToRoute() {
      this.$router.push({ name: "Supplers" });
    },
  },
};
</script>
