<template>
  <div>
    <page-title :title="'New Order'"></page-title>
    <v-container class="py-8">
      <v-row v-if="loadingPage == true">
        <v-col cols="12">
          <v-skeleton-loader
            class="mx-auto"
            max-width="100%"
            type="list-item-avatar-three-line, image, article"
          ></v-skeleton-loader>
        </v-col>
      </v-row>
      <v-row v-else>
        <div class="col-12">
          <v-card :loading="loadingCreateOrder" :disabled="loadingCreateOrder">
            <v-card-title>
              <h4 class="textcolor--text">Create Order</h4>
            </v-card-title>
            <v-card-text class="py-5">
              <v-btn x-large class="primary" @click="createOrder"
                >Create Order</v-btn
              >
            </v-card-text>
          </v-card>
        </div>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loadingCreateOrder: false,
      loadingPage: true,
    };
  },
  methods: {
    async createOrder() {
      this.loadingCreateOrder = true;
      await axios
        .post("/staff/order/create")
        .then((response) => {
          this.loadingCreateOrder = false;
          console.log(response);
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
  },
  created() {
    this.loadingPage = false;
  },
};
</script>

<style>
</style>
