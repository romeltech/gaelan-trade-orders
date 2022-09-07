<template>
  <div>
    <v-navigation-drawer
      class="elevation-4"
      v-model="drawer"
      floating
      app
      width="250px"
      :src="`${$baseUrl + '/images/gag-2.png'}`"
      color="primary"
      dark
    >
      <div width="100%" class="text-center pa-3 pb-3">
        <!-- <v-avatar size="150">
          <v-img :src="profileImagePath"></v-img>
        </v-avatar> -->
        <v-avatar size="70" color="white" class="text-uppercase text-h5 my-5">
          {{ printInitials(auth_user.profile.full_name) }}
        </v-avatar>
        <div class="text-h6 text-uppercase white--text">
          {{ auth_user.profile.full_name }}
        </div>
        <small class="white--text text-capitalize"
          >{{ auth_user.role }} Account</small
        >
      </div>
      <v-divider></v-divider>
      <v-list dense nav>
        <!-- Navigation Items -->
        <!-- Common Nav -->
        <!-- <nav-item
          v-for="item in commonNav"
          :key="item.title"
          :nav="item"
        ></nav-item> -->
        <!-- Admin -->
        <div v-if="auth_user.role == 'admin' || auth_user.role == 'manager'">
          <nav-item
            v-for="item in adminNav"
            :key="item.title"
            :nav="item"
          ></nav-item>
        </div>
        <!-- Staff -->
        <div v-if="auth_user.role == 'staff'">
          <nav-item
            v-for="item in staffNav"
            :key="item.title"
            :nav="item"
          ></nav-item>
        </div>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar app color="white" dense class="elevation-gag">
      <v-app-bar-nav-icon
        @click.stop="drawer = !drawer"
        color="primary"
      ></v-app-bar-nav-icon>
      <v-toolbar-title class="pl-1 mr-12 align-center d-flex">
        <v-img max-width="25" :src="`${$baseUrl + '/images/fav.png'}`"> </v-img>
        <span class="ml-2 title primary--text text-capitalize"
          >{{ auth_user.role }} Panel</span
        >
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu
        v-model="menu"
        :close-on-content-click="true"
        :nudge-width="150"
        transition="slide-y-transition"
        offset-y
        :nudge-bottom="3"
      >
        <template v-slot:activator="{ on }">
          <v-btn text icon v-on="on">
            <!-- <v-avatar size="30">
              <img :src="profileImagePath" />
            </v-avatar> -->
            <v-avatar
              size="30"
              color="#eee"
              class="text-uppercase text-subtitle"
            >
              {{ printInitials(auth_user.profile.full_name) }}
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list>
            <v-list-item>
              <!-- <v-list-item-avatar color="#eee">
                <img :src="profileImagePath" />
              </v-list-item-avatar> -->
              <v-list-item-avatar color="#eee">
                {{ printInitials(auth_user.profile.full_name) }}
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title>{{
                  auth_user.profile.full_name
                }}</v-list-item-title>
                <v-list-item-subtitle>{{
                  auth_user.email
                }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list class="py-3">
            <v-list-item-group color="primary">
              <v-list-item :to="'/user/account-settings'">
                <v-list-item-content>
                  <v-list-item-title>Account Settings</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
            <!-- <v-list-item :to="'/user/account-settings'">
              <v-list-item-avatar small>
                <v-icon small>mdi-account</v-icon>
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title>Account Settings</v-list-item-title>
              </v-list-item-content>
            </v-list-item> -->
          </v-list>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn depressed v-on:click="logout" width="100%">Logout</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
    </v-app-bar>
  </div>
</template>

<script>
import NavItem from "./NavItem";
export default {
  components: {
    NavItem,
  },
  data() {
    return {
      auth_user: this.$store.state.authUser.userObject,
      drawer: true,
      menu: false,
      //   commonNav: [
      //     {
      //       title: "Dashboard",
      //       icon: "mdi-home-outline",
      //       location: "/d/dashboard",
      //     },
      //     // {
      //     //   title: "Profile",
      //     //   icon: "mdi-account",
      //     //   location: "/d/profile",
      //     // },
      //   ],
      staffNav: [
        // {
        //   title: "New Order",
        //   icon: "mdi-playlist-edit",
        //   location: "/staff/order/new",
        // },
        {
          title: "Orders",
          icon: "mdi-playlist-edit",
          location: "/staff/orders/draft",
          value: true,
          subs: [
            {
              title: "Draft",
              icon: "mdi-clipboard-edit-outline",
              location: "/staff/orders/draft",
            },
            {
              title: "Submitted",
              icon: "mdi-email-fast",
              location: "/staff/orders/submitted",
            },
            {
              title: "Completed",
              icon: "mdi-check-all",
              location: "/staff/orders/completed",
            },
          ],
        },
      ],
      adminNav: [
        {
          title: "Orders",
          icon: "mdi-format-list-bulleted",
          location: "/d/orders",
          value: true,
          subs: [
            {
              title: "Submitted",
              icon: "mdi-email-fast",
              location: "/d/orders/submitted",
            },
            {
              title: "Completed",
              icon: "mdi-check-all",
              location: "/d/orders/completed",
            },
          ],
        },
        {
          title: "Settings",
          icon: "mdi-cog-outline",
          location: "/d/settings",
          subs: [
            {
              title: "Items",
              icon: "mdi-archive",
              location: "/d/items",
            },
            {
              title: "Customers",
              icon: "mdi-store",
              location: "/d/customers",
            },
            {
              title: "Users",
              icon: "mdi-account-group",
              location: "/d/users",
            },
          ],
        },
      ],
    };
  },
  methods: {
    logout: function (event) {
      event.preventDefault();
      document.getElementById("logout-form").submit();
    },
  },
};
</script>
