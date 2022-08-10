<template>
  <v-dialog v-model="dataObj.status" persistent width="450">
    <v-card>
      <v-card-title class="text-capitalize">{{ dataObj.title }}</v-card-title>
      <v-card-text>{{ dataObj.msg }}</v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" text @click="dataObj.status = false">Cancel</v-btn>
        <v-btn class="error ml-2" :loading="dataObj.loading" @click="confirm">{{
          dataObj.btnTitle
        }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    confOptions: {
      type: Object,
      default: null,
    },
  },
  watch: {
    confOptions: {
      handler(val) {
        this.dataObj = {
          ...this.dataObj,
          status: val.status,
          title: val.title ? val.title : "Confirmation",
          msg: val.msg ? val.msg : "Please confirm",
          btnTitle: val.btnTitle ? val.btnTitle : "Confirm",
          loading: val.loading ? val.loading : false,
        };
      },
      deep: true,
    },
  },
  data() {
    return {
      dataObj: {
        status: false,
        loading: false,
      },
    };
  },
  methods: {
    confirm() {
      this.dataObj.status = false;
      this.$emit("response", true);
    },
  },
};
</script>
