<template>
  <div>
    <page-title :title="'Import Customers'"></page-title>
    <v-container class="py-8">
      <v-row>
        <div class="col-12">
          <v-card>
            <v-card-title>Import Customers</v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pb-10">
              <div class="d-flex align-center">
                <ImportData
                  :route="'/d/import/customers'"
                  @responded="importResponse"
                />
                <v-btn
                  class="ml-2"
                  text
                  color="primary"
                  :href="`${$baseUrl}/csv/import-customer-template.csv`"
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
              <span>
                {{ alertMessage }}
              </span>
              <span v-if="alertType == 'success'">
                {{ "Redirecting to " }}
                <a
                  href="#"
                  class="success--text"
                  @click="() => goToRoute('Locations')"
                  >Customers</a
                >
                {{ " in... " + countdown }}
              </span>
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
              this.goToRoute("Locations");
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
      if (res.alertData.type == "success") {
        this.redirectCounter(5);
      } else {
        this.alertMessage =
          "Import Error. Please download and make sure to upload the correct format.";
      }
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
