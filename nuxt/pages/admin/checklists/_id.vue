<template>
  <div>
    <adminIncludeCrumb name="عناوین چک لیست"/>
    <adminChecklistTitleList :checklist="checklist" :loading="loading" :userList="userList" />
  </div>
</template>

<script>
  import {mapState} from "vuex";

  export default {
    name : 'title-checklist',
    layout : 'admin',
    data() {
      return {
         loading :false
      }
    },
    created() {
      this.loading = true
      this.$store.dispatch('admin/user/getUserList')
      this.$store.dispatch('admin/section/LoadAdminSections')
      this.$store.dispatch('admin/checklist/getChecklist' , {
        id : this.$route.params.id
      }).then(res => this.loading = false)
    },
    computed : {
      ...mapState('admin/user' , ['userList']),
      ...mapState('admin/checklist' , ['checklist'])
    }
  }
</script>
