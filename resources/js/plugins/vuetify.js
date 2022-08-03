import Vue from "vue";
import Vuetify from "vuetify";
// import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify);

const opts = {
    theme: {
        themes: {
          light: {
            // linear-gradient(to top, #e85224, #fabe22)
            primary: "#233464",
            orange: '#fabe22',
            textcolor: '#233464',
            lightOrange: 'e85224'
            // black: '#333',
            // Vuetify Action Colors
            // secondary: '#f5f5f5',
            // accent: '#82B1FF',
            // error: '#FF5252',
            // info: '#2196F3',
            // success: '#4CAF50',
            // warning: '#FFC107',
          }
        }
      },
    icons: {
        iconfont: "mdi"
    }
};

export default new Vuetify(opts);
