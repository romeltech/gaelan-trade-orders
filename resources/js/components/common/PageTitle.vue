<template>
  <v-app-bar color="white" dense class="elevation-0">
    <v-toolbar-title class="overline">{{ title }}</v-toolbar-title>
  </v-app-bar>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: "",
    },
  },
};
</script>
