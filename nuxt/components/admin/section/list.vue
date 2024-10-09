<template>
  <div class="col-12 col-md-9 m-auto">
    <Card>
      <p slot="title">
        لیست بخش ها
      </p>
      <List :loading="sectionLoading">
        <adminSectionItem v-for="(section , index) in sectionList" :key="index" :section="section"
                          :sectionList="sectionList"/>
      </List>
    </Card>
  </div>
</template>

<script>
  export default {
    name : 'section-list',
    props : ['sectionList' , 'sectionLoading']
  }
</script>
