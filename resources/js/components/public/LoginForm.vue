<template>
  <v-card id="loginform" class="px-5">
    <v-card-title class="px-5 pt-8 pb-0">Login</v-card-title>
    <v-card-text class="py-5">
      <v-text-field
        outlined
        required
        type="text"
        name="username"
        label="Username"
        v-model="loginEmail"
        :rules="loginEmailrules"
        autofocus
      >
      </v-text-field>
      <v-text-field
        outlined
        required
        id="password"
        label="Password"
        type="password"
        name="password"
        v-model="loginPassword"
        :rules="loginPasswordrules"
      >
      </v-text-field>
      <v-btn
        :loading="loadingLogin"
        width="100%"
        height="55"
        large
        grey
        lighten-1
        :class="`mb-3 ${loginValid == true ? 'primary' : 'grey lighten-2'}`"
        :disabled="!loginValid"
        @click="submitLogin"
        >Login</v-btn
      >
      <div class="d-flex justify-space-between align-center my-3"></div>

      <v-alert v-if="hasError == true" type="error">
        {{ message }}
      </v-alert>
    </v-card-text>
  </v-card>
</template>

<script>
import gagUserClient from "../../services/gagUserClient";
export default {
  data() {
    return {
      loadingLogin: false,
      hasError: false,
      loginValid: true,
      loginEmail: "",
      loginEmailrules: [
        (value) => !!value || "Required",
        // value => /.+@.+\..+/.test(value) || "E-mail must be valid"
      ],
      loginPassword: "",
      loginPasswordrules: [
        (value) => !!value || "Required",
        (value) =>
          (value && value.length > 8) ||
          "Password must be atleast 8 characters",
      ],

      message: "",
    };
  },
  methods: {
    submitLogin() {
      this.loadingLogin = true;
      let data = {
        username: this.loginEmail,
        password: this.loginPassword,
      };
      gagUserClient.get("/sanctum/csrf-cookie").then((res) => {
        gagUserClient.post("api/sanctumlogin", data).then((response) => {
          this.loadingLogin = false;
          console.log("res", response.data);
          localStorage.setItem("gag_en", response.data.token.toString());
        });
      });
    },
  },
};
</script>
