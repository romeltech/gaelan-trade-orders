<template>
  <div>
    <page-title :title="'Users'"></page-title>
    <v-container class="py-8">
      <v-row>
        <v-col v-if="pageLoading == true" cols="12">
          <v-skeleton-loader
            class="mx-auto"
            max-width="100%"
            type="list-item-avatar-three-line, image, article"
          ></v-skeleton-loader>
        </v-col>
        <v-col v-else cols="12" class="py-5">
          <v-btn
            to="user/new"
            class="primary mb-5"
            v-if="auth_user.role === 'admin'"
            >New User</v-btn
          >
          <v-card>
            <v-card-title>
              <h4>Users</h4>
              <v-spacer></v-spacer>
              <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
              ></v-text-field>
            </v-card-title>
            <v-data-table
              :headers="headers"
              :items="user_lists"
              :search="search"
            >
              <template v-slot:item.status="{ item }">
                <v-chip
                  :class="`${
                    item.status == 'active'
                      ? 'success'
                      : item.status == 'disabled'
                      ? 'error'
                      : 'grey'
                  }`"
                  >{{
                    item.status == "active"
                      ? "Active"
                      : item.status == "disabled"
                      ? "Disabled"
                      : "Trashed"
                  }}</v-chip
                >
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn
                  icon
                  x-small
                  :disabled="auth_user.role === 'manager'"
                  @click="editUser(item)"
                  class="transparent mr-1"
                >
                  <v-icon small> mdi-pencil </v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  data() {
    return {
      auth_user: this.$store.state.authUser.userObject,
      pageLoading: true,
      search: "",
      headers: [
        { text: "Full Name", value: "profile.full_name" },
        { text: "Email", value: "email" },
        { text: "Phone", value: "account_phone" },
        { text: "Role", value: "role" },
        { text: "Status", value: "status" },
        { text: "Actions", align: "end", value: "actions", sortable: false },
      ],
      user_lists: [],
    };
  },
  methods: {
    async getAllUsers() {
      const response = await axios.get("/d/user/get/all");
      console.log(response.data);
      this.user_lists = Object.assign([], response.data);
    },
    editUser(user) {
      this.$router.push({
        name: "EditUser",
        params: { id: user.id },
      });
    },
  },
  created() {
    this.getAllUsers().then(() => {
      this.pageLoading = false;
    });
  },
};
</script>
