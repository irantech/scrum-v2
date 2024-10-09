<template>
   <Collapse>

      <Panel :name="section.id.toString()" v-if="day != 0 || checklist_reverse_section.length != 0">
        <span class="col-2 pt-2">
          {{section.title}}
          <br>
          <span v-if="users && users.length > 0">({{getUser(section.id)}})</span>
        </span>

        <div class="col-10 badge-box d-flex flex-wrap">
          <div class="py-1 px-2 mx-2">
            <Tooltip content="کل زمان تاخیر" placement="top">
              <Badge status="cyan" :text="`${day}روز `"  />
              تاخیر
            </Tooltip>
          </div>
          <div class="py-1 px-2 mx-2" v-if="checklist_reverse_section.length > 0">
            <Tooltip content="تعداد برگشت ها" placement="top">
              <Badge status="cyan" :text="`${checklist_reverse_section.length}`"  />
              برگشت
            </Tooltip>
          </div>
          <div v-if="checklist_reverse_section.some(x=> { return x.section.id == section.id})" class="py-1 px-2 mx-2"
               v-for="type in reverse_type_list"  :key="type.name" >
            <Tooltip :content="type.title" placement="top">
              <Badge :status="type.type" :text="`${countType(type.name)}`"  />
              {{type.title}}
            </Tooltip>
          </div>
        </div>
        <template slot="content">
          <div class="row" >
            <adminChecklistReverseItem  v-for="(reverse_data , index) in checklist_reverse_section" :reverse="reverse_data" :key="index"></adminChecklistReverseItem>
          </div>
        </template>
      </Panel>
    </Collapse>
</template>
<script>

export default {
  name : 'checklist-reverse-section' ,
  props : ['section' , 'checklist_reverse' , 'todolist' , 'users'],
  data() {
    return {
      day : 0 ,
      reverse_type_list : [
        {
          name : 'accept_periodic_count' ,
          title : 'ادواری تایید شده',
          type : 'success'
        } ,
        {
          name : 'reject_periodic_count' ,
          title : 'ادواری تایید نشده',
          type : 'error'
        } ,
        {
          name : 'accept_error_count' ,
          title : 'خطا تایید شده' ,
          type : 'success'
        },
        {
          name : 'reject_error_count' ,
          title : 'خطا تایید نشده' ,
          type : 'error'
        },
        {
          name : 'accept_offer_count',
          title : 'پیشنهاد تایید شده' ,
          type : 'success'
        },
        {
          name : 'reject_offer_count',
          title : 'پیشنهاد تایید نشده' ,
          type : 'error'
        },
      ]
    }
  },
  created() {
      let time = 0
      this.checklistTodolist.forEach(todo => {
        if(todo.progress_extend_time) {
          let split_time = todo.progress_extend_time.split(" ");
          this.day  = this.day + parseInt(split_time[0])

        }
      })
      return this.day + ' روز '
    },
  methods :{
    getUser(section_id) {
      if(this.users && this.users.length > 0 ) {
        if(section_id == '7') {
          section_id = '2'
        }
        let user = this.users.find(x => x.role.section.id == section_id)
        if(user) {
          return user.name
        }else{
          return  '' ;
        }
      }else{
        return  '' ;
      }

    },
    countType(type) {
      let counter = 0;
      this.checklist_reverse_section.forEach(reverse => {
        if(type == 'accept_error_count' && reverse.error_reverse_data)
        {
          counter += reverse.error_reverse_data.accept_count
        }else if(type == 'reject_error_count' && reverse.error_reverse_data) {
          counter += reverse.error_reverse_data.reject_count
        }
        else if(type == 'accept_offer_count' && reverse.offer_reverse_data) {
          counter += reverse.offer_reverse_data.accept_count
        }
        else if(type == 'reject_offer_count' && reverse.offer_reverse_data) {
          counter += reverse.offer_reverse_data.reject_count
        }
        else if(type == 'accept_periodic_count' && reverse.periodic_reverse_data) {
          counter += reverse.periodic_reverse_data.accept_count
        }
        else if(type == 'reject_periodic_count' && reverse.periodic_reverse_data) {
          counter += reverse.periodic_reverse_data.reject_count
        }
      })
      return counter

    },
  } ,
  computed : {
    checklist_reverse_section() {
      return  this.checklist_reverse.filter( reverse => {
          return reverse.section.id == this.section.id
      })
    },
    checklistTodolist() {
      return  this.todolist.filter( todo => {
        return todo.section.id == this.section.id
      })
    },
  },
}
</script>
<style scoped>

.ivu-collapse > .ivu-collapse-item > .ivu-collapse-header{
  height: 100px !important;
}
</style>
