<template>
  <div>
    <page-title :title="'Order'"></page-title>
    <v-container class="py-8" style="padding-bottom: 100px !important">
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
          <v-card :loading="loadingOrder" :disabled="loadingOrder">
            <v-card-title>
              <div class="textcolor--text d-flex align-center">
                <div class="mr-1">Order Number:</div>
                <div class="font-weight-bold">
                  {{ orderObj.order_number && orderObj.order_number }}
                </div>
              </div>
            </v-card-title>
            <v-card-text class="py-3">
              <order-form
                :order-prop="orderObj"
                @saved="savedResponse"
              ></order-form>
            </v-card-text>
          </v-card>
        </div>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import OrderForm from "./OrderForm";
export default {
  components: {
    OrderForm,
  },
  data() {
    return {
      loadingPage: false,
      loadingOrder: false,
      orderObj: {},
    };
  },
  methods: {
    async getOrder() {
      this.loadingOrder = true;
      await axios
        .get("/staff/order/get/" + this.$route.params.ordernum)
        .then((response) => {
          this.loadingOrder = false;
          this.orderObj = Object.assign({}, response.data);
          console.log("getOrder", this.orderObj);
        })
        .catch((err) => {
          console.log(err);
          this.loadingOrder = false;
          this.loadingPage = false;
        });
    },
    savedResponse(value) {
      if (value == true) {
        this.getOrder();
      }
    },
  },
  created() {
    this.getOrder().then(() => {
      this.loadingPage = false;
    });
  },
};
</script>
