<template>
  <div class="col-4 mb-2">

     <Card :class="{'border border-warning' : !task.delivery_time , 'border border-info' : today_date == task.delivery_time_base && task.status != 'complete' , 'border border-dark' : today_date > task.delivery_time_base && task.status != 'complete' }">
       <div>

            <div class="d-flex justify-content-between my-1">
              <div>
                <Tag v-for="(label , index) in task.label_list" :key="index"   :color="`${label.color}`" >{{label.title}}</Tag>
              </div>
              <Dropdown v-if="task.status != 'complete'">
                <a href="javascript:void(0)">
                  <Icon type="ios-more-outline" />
                </a>
                <template #list v-if="$store.getters['auth/can']('create-tasks') ">
                  <DropdownMenu>
                    <DropdownItem>
                      <a @click="openUpdateModal()">ویرایش</a>
                    </DropdownItem>
                  </DropdownMenu>

                </template>
              </Dropdown>

            </div>
            <h3 class="font-weight-bold" @click="showSingleTask()"> {{ task.title }}</h3>

            <div class="d-flex justify-content-between my-1">
              <p class="text-left" style="font-size: 12px !important;">{{ task.created_at }}</p>

              <div>
                <span v-if="task.site_link" >
                    <Tooltip content="لینک سایت">
                      <a :href="`${task.site_link}`">
                         <Icon type="ios-link" />
                      </a>
                    </Tooltip>
                  </span>
                <span v-if="task.theme_link">
                  <Tooltip  content="لینک طرح">
                      <a :href="`${task.theme_link}`">
                          <Icon type="md-link" />
                      </a>
                  </Tooltip>
                </span>
                <span v-if="task.contract" >
                  <Tooltip content="لینک قرارداد">
                      <nuxt-link :to="`/admin/contract/${task.contract.id}/view`">
                          <Icon type="md-link" />
                      </nuxt-link>
                  </Tooltip>
                </span>
              </div>
            </div>

            <p v-if="task.delivery_time_base" class="text-left text-success" style="font-size: 12px"><span> تایم تحویل: </span>{{ task.delivery_time }}</p>
            <div class="d-flex my-1">
           <div>
             <Tooltip v-if="task.user_owner"  :content="task.user_owner.userName">
                 <Avatar :src="`${$env.UPLOAD_URL}/${task.user_owner.avatar}`"  />
             </Tooltip>
             <Tooltip v-for="(user , index) in task.user_list" :key="`${index}k`" v-if="task.user_owner && user.userName != task.user_owner.userName" :content="user.userName">
                   <Avatar  :src="`${$env.UPLOAD_URL}/${user.avatar}`"  />
             </Tooltip>
           </div>

         </div>

          </div>
     </Card>
     <Modal v-model="updateModal" title="ویرایش تسک" width="800" footer-hide>
      <Form :model="formUpdate" label-position="top" :rules="ruleUpdate" class="row" ref="formUpdate">
        <FormItem label="عنوان" prop="title" class="col-12">
          <Input v-model="formUpdate.title"></Input>
        </FormItem>
        <FormItem label="لینک طرح" class="col-6">
          <Input v-model="formUpdate.theme_link"></Input>
        </FormItem>
        <FormItem label="لینک سایت" class="col-6">
          <Input v-model="formUpdate.site_link"></Input>
        </FormItem>
        <FormItem label="قرارداد مشخص" class="col-6">
          <Select v-model="formUpdate.contract_id" filterable label="مشتری">
            <Option v-for="item in contractList" :value="item.id" :key="item.id">{{ item.customer ? item.customer.name : '' }}({{item.title}})</Option>
          </Select>
        </FormItem>
        <FormItem label="ارجاع به بخش" prop="section_id" class="col-6">
          <Select v-model="formUpdate.section_id" filterable label="بخش">
            <Option v-for="item in limitedSectionList" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
        <FormItem label="لیست لیبل ها" class="col-12">
          <Select v-model="formUpdate.label_list" multiple label="لیبل">
            <Option v-for="item in taskLabelList" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
        <FormItem label="توضیحات"  class="col-12">
          <Input v-model="formUpdate.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="Enter something..."></Input>
        </FormItem>
        <FormItem class="col-12">
          <Button type="primary" :loading="updateLoading" @click="updateTask('formUpdate')">ارسال اطلاعات</Button>
        </FormItem>
      </Form>
    </Modal>


  </div>
</template>
<script>

import {mapState} from "vuex";

export default {
  name : 'task-item' ,
  props : ['task'  ,'sectionList' , 'contractList' , 'taskLabelList' , 'limitedSectionList' ] ,
  data () {
    return {
      updateModal : false,

      formUpdate: {
        title: '',
        description : '' ,
        theme_link  : '' ,
        site_link   : '' ,
        contract_id: '',
        label_list: '',
        section_id: '',
      },
      ruleUpdate: {
        title: [
          { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
        ],
        section_id: [
          { required: true, message: 'انتخاب بخش مورد نظر الزامی است', trigger: 'change' , type : 'integer'}
        ],
      },
      updateLoading : false ,
      deleteLoading : false ,

    }
  },
  computed : {

    today_date() {
      return this.$moment().utc().format('YYYY-MM-DD')
    } ,

  },
  methods : {
    openUpdateModal() {
      this.updateModal = true
      this.formUpdate.title = this.task.title
      this.formUpdate.theme_link = this.task.theme_link
      this.formUpdate.site_link = this.task.site_link
      this.formUpdate.contract_id = this.task.contract ? this.task.contract.id : ''
      this.formUpdate.label_list = this.task.label_list.length > 0 ? this.task.label_list.map(a => a.id) : []
      this.formUpdate.description = this.task.description
    },
    updateTask(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.updateLoading = true
          this.$store.dispatch('admin/task/UpdateTask', {
            id: this.task.id,
            form: this.formUpdate
          })
            .then(response => {
              this.updateModal = false
              this.updateLoading = false
              this.$Message.success(response.message);
            })
            .catch(error => {
              this.$Message.error(error.response.data.message);
              this.updateLoading = false
            })
            .finally(()=> this.updateLoading  = false)
        } else {
          this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
        }
      })
    } ,
    showSingleTask() {
      this.$emit('showModal' , this.task.id);
    }
  } ,
}
</script>

<style scoped>
.ivu-card-head {
  display: none;
}
.bg-live{
 background: #f1ff8b;
}
.bg-archive{
  background: #000000e5;
}
</style>
