<template>
    <div>
      <Collapse v-model="value1" class="mx-5">
        <Panel  v-for="(activity , index) in activities" :key="index" :name="index.toString()">
          {{activity.log_name}}
          <p slot="content">
            شما در تاریخ
            {{ activity.created_at}}
            {{activity.description}}
          </p>
        </Panel>
      </Collapse>
    </div>
</template>

<script>
  export default {
    name : 'activity-profile',
    props : ['activities' ,'loading'] ,
    data(){
      return {
        value1 : "0"
      }
    }
  }
</script>
