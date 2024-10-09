import Vue from 'vue';
import wysiwyg from "vue-wysiwyg";
import "vue-wysiwyg/dist/vueWysiwyg.css";
Vue.use(wysiwyg, {
  // if the image option is not set, images are inserted as base64
  image: {
    uploadURL: "https://api.ladyscarf.ir/api/uploadEditor",
    dropzoneOptions: {}
  },

  // limit content height if you wish. If not set, editor size will grow with content.
  maxHeight: "500px",


}); // config is optional. more below
