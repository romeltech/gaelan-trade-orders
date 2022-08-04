<template>
  <v-dialog v-model="importDialog" persistent max-width="600px">
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="primary" v-bind="attrs" v-on="on"> Import </v-btn>
    </template>
    <v-card :loading="loading" :disabled="loading">
      <v-card-title class="mb-2">Import</v-card-title>
      <v-card-text>
        <v-file-input
          id="inputFile"
          accept="text/csv"
          label="Select csv file"
          :disabled="loading"
          outlined
          dense
        ></v-file-input>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="grey"
          :disabled="loading"
          text
          @click="importDialog = false"
          >Cancel</v-btn
        >
        <v-btn color="primary" :loading="loading" @click="importCSV"
          >Import</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    route: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      importDialog: false,
      loading: false,
    };
  },
  methods: {
    parseComplete(results, file) {
      // Remove 1st row header
      //   delete results.data[0];

      // Filter Empty Rows
      let resultsArray = results.data.filter(function (el) {
        let firstKey = Object.keys(el)[0].toString(); // get the first property and check
        return el != null && el[firstKey] != "";
      });
      console.log("resultsArray", resultsArray);

      // Set Data
      let data = {
        import_data: JSON.stringify(resultsArray),
      };

      // Send Data
      axios
        .post(this.route, data)
        .then((response) => {
          let data = {
            alertData: {
              type: "success",
              message: response.data.message,
            },
            existed_data: response.data.existed_data,
          };
          this.$emit("responded", data);
          this.loading = false;
          this.importDialog = false;
        })
        .catch((error) => {
          this.loading = false;
          this.importDialog = false;
          if (error.response.status == 422) {
            let data = {
              alertData: {
                type: "error",
                message: error.response.data.message,
              },
              existed_data: error.response.data.existed_data,
            };
            this.$emit("responded", data);
          }
          if (error.response.status >= 500) {
            let data = {
              alertData: {
                type: "error",
                message:
                  "Import Error 500. Please double check your CSV file for valid values",
              },
              existed_data: error.response.data.existed_data,
            };
            this.$emit("responded", data);
          }
        });
    },

    importCSV() {
      this.loading = true;
      let ext = document
        .getElementById("inputFile")
        .value.split(".")
        .pop()
        .toLowerCase();
      if (ext !== "csv") {
        alert("Please upload a csv file");
        this.loading = false;
        return;
      }
      this.$papa.parse(document.getElementById("inputFile").files[0], {
        header: true,
        transformHeader: function (text) {
          return text.replace(/\s+/g,"_").toLowerCase().trim();
        },
        complete: this.parseComplete,
      });
    },
  },
};
</script>
