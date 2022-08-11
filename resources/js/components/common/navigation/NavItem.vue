<template>
  <div>
    <v-list-group
      active-class="ga-active-list-bg white--text"
      dark
      v-if="item.subs"
      value="true"
    >
      <template v-slot:activator>
        <v-list-item-action>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title v-text="item.title"></v-list-item-title>
        </v-list-item-content>
      </template>
      <div v-for="sub in item.subs" :key="sub.title">
        <v-list-item
          :to="sub.location"
          class="pl-10"
          active-class="orange-gradient-bg"
        >
          <v-list-item-action class="mr-3">
            <v-icon small color="white">{{ sub.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title
              class="white--text"
              style="z-index: 1"
              v-text="sub.title"
            >
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </div>
    </v-list-group>
    <v-list-item
      v-else
      link
      :to="item.location"
      active-class="orange-gradient-bg"
    >
      <v-list-item-action>
        <v-icon>{{ item.icon }}</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>{{ item.title }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </div>
</template>

<script>
export default {
  props: {
    nav: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      item: this.nav,
    };
  },
  created() {
    // console.log(this.item);
  },
};
</script>

<style>
</style>
