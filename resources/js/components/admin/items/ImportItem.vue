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
              {{ alertMessage + "." }}
              {{ "Redirecting to " }}
              <a
                v-if="alertType == 'success'"
                href="#"
                class="success--text"
                @click="() => goToRoute('Items')"
                >Items</a
              >
              {{ " in... " + countdown }}
            </div>
          </v-alert>
        </div>
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

      countdown: 0,
    };
  },
  watch: {
    countdown: {
      handler(value) {
        if (value) {
          setTimeout(() => {
            this.countdown--;
            if (this.countdown == 0) {
              this.goToRoute("Items");
            }
          }, 1000);
        }
      },
    },
  },
  methods: {
    importResponse(res) {
      console.log("res", res);
      this.alertStatus = true;
      this.alertMessage = res.alertData.message;
      this.alertType = res.alertData.type;
      this.redirectCounter(5);
    },
    redirectCounter(s) {
      this.countdown = s;
    },
    goToRoute(route) {
      this.$router.push({ name: route });
    },
  },
};
</script>
