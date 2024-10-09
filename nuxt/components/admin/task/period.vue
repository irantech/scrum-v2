<template>
  <div>
    <div class="row px-2">
      <adminTaskItem v-for="(task , index) in task_list" :key="index" :task="task"/>
    </div>
  </div>
</template>
<script>
export default {
  name : 'task-period' ,
  props : ['task_list' ,'sectionList' , 'contractList' , 'taskLabelList' , 'task_loading' , 'searchForm'] ,
  data () {
    return {
      createModal : false,
      searchLoading : false ,
      formCreate: {
        title: '',
        description : '' ,
        theme_link  : '' ,
        site_link   : '' ,
        contract_id: '',
        label_list: '',
        section_id: '',
      },
      ruleCreate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ],
        section_id: [
          { required: true, message: 'انتخاب بخش مورد نظر الزامی است', trigger: 'change' , type : 'integer'}
        ],
      },
      createLoading : false
    }
  },
  methods : {
    createNewTask(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.createLoading = true
          this.$store.dispatch('admin/task/createNewTask' , { form : this.formCreate })
            .then(response => {
                this.$Message.success(response.data.message);
                this.createModal = false
                this.createLoading = false
                this.handleReset(name)
              }
            )
            .catch(error => {
              console.log(error)
              this.createLoading = false
              this.$Message.error(error.response);
            })
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    },
    handleReset (name) {
      this.$refs[name].resetFields();
    } ,
    search() {
      this.$emit('setSearchData' , this.searchForm)
    },
  },
  computed : {
    today_date() {
      return  this.$moment().utc().format('jYYYY-jMM-jDD')
    }
  },
}
</script>

<style scoped>
.ivu-card-head {
  display: none;
}
</style>
