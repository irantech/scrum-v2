import Vue from 'vue';
import Vuex from 'vuex';
import BaseProgress from './Modules/admin/BaseProgress';
import Section from "./Modules/admin/Section";
import Software from './Modules/admin/Software';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        baseProgress  : BaseProgress ,
        section       : Section  ,
        software      : Software

    }
})
