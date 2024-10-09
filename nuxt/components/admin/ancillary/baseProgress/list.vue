<template>
  <div>
    <Card>
      <p slot="title">
        مراحل کلی این قرارداد
      </p>
      <p slot="extra" >
        <Button type="success" v-if="$store.getters['auth/can']('update-ancillary')" >
          <nuxt-link :to="`/admin/ancillary/${ancillary.id}/edit`">افزودن</nuxt-link>
        </Button>
      </p>
      <Alert show-icon v-if="ancillary.base_progress && ancillary.base_progress.length  === 0">
        <p>هنوز برای این قرارداد مرحله اصلی ثبت نشده است. لطفا به صفحه ویرایش رفته و مراحل را اضافه
          نمایید </p>
        <Button type="primary" v-if="$store.getters['auth/can']('update-ancillary')">
          <nuxt-link :to="`/admin/ancillary/${ancillary.id}/edit`">ویرایش</nuxt-link>
        </Button>

      </Alert>
      <Collapse v-else accordion v-model="value">
        <Panel v-for="(base , index) in ancillary.base_progress"
               :key="index" :name="base.id.toString()" :class="[cardHeaderClass(base.pivot.status)]">
          {{base.description}} - <span v-for="stat in allStatuses"
                                       v-if="base.pivot.status == stat.value"
                                       v-text="stat.title" ></span>
          <div slot="content">
            <Form class="mb-4">
              <FormItem>
                  <Select @on-change="changeBaseStatus(base.id)"  v-model="newStatus" :disabled="changeBaseStatusLoading || !$store.getters['auth/can']('ancillary-change-base-progress')">
                    <Option v-for="stat in allStatuses" :value="stat.value" :key="stat.value">{{ stat.title }}</Option>
                  </Select>
              </FormItem>
            </Form>

            <div class="d-flex flex-wrap">
            <div class="child-label" v-for="child in bp_child">
              <span class="font-weight-bold inline-block">{{child.title}}</span>
              <div class="btn-group btn-group-sm d-inline" v-if="child">

                <ButtonGroup class="d-flex mb-3"   >
                  <Button  v-for="(status,index) in allStatuses"
                           :key="index" class="px-1 btn-status"
                           :disabled="!$store.getters['auth/can']('ancillary-change-sub-progress')"
                           @click="changeSubStatus(status.value,child)"
                           :class="[`${status.value}` , {'active' : child.pivot.status == `${status.value}`}]">{{ status.title }}</Button>
                </ButtonGroup>
              </div>
            </div>
          </div>
          </div>
        </Panel>
      </Collapse>
    </Card>
  </div>
</template>
<script>
  export default  {
    name : 'ancillary-baseProgress' ,
    props : ['ancillary'] ,
    data () {
      return{
        allStatuses: [
          {value: 'hold', title: 'در دست بررسی'},
          {value: 'cancel', title: 'لغو'},
          {value: 'running', title: 'در حال اجرا'},
          {value: 'complete', title: 'تکمیل'},
        ],
        bp_child: [],
        value : '0' ,
        newStatus : '' ,
        changeBaseStatusLoading : false
      }
    },
    watch: {
      value (){
        this.getBaseProgressChild(this.value)
      }
    },
    methods : {
      getBaseProgressChild(baseId) {
        this.bp_child = [];
        this.bp_child = this.ancillary.sub_progress.filter(sub => sub.base_progress_id == parseInt(baseId[0]))
        let selectedBase = this.ancillary.base_progress.find(x => x.id == parseInt(baseId[0]))
        if(selectedBase)
         this.newStatus = selectedBase.pivot.status
      },
      cardHeaderClass(baseStatus) {
        if (baseStatus == 'complete') {
          return 'bg-success-iview';
        }
        if (baseStatus == 'running') {
          return 'bg-info-iview';
        }
        if (baseStatus == 'hold') {
          return 'bg-warning-iview';
        }
        if (baseStatus == 'cancel') {
          return 'bg-danger-iview';
        }

        return '';
      },
      changeBaseStatus(baseProgressId) {
        this.changeBaseStatusLoading = true
        this.$store.dispatch('admin/ancillary/changeAncillaryBaseStatus'  , {
          params : {status: this.newStatus, base_progress_id: baseProgressId, ancillary_id: this.ancillary.id}
        })
          .then(response => {
            this.$Message.success(response.data.message);
            this.value = '0'
            this.changeBaseStatusLoading  = false
          })
          .catch(error => {
            this.changeBaseStatusLoading  = false
            this.$Message.error(error.response.data.message);
          })

      },
      changeSubStatus(status, child) {
        this.$store.dispatch('admin/ancillary/changeAncillarySubStatus' , {
          params : {'status': status, 'sub_progress_id': child.id, 'ancillary_id': this.ancillary.id}
        })
          .then(response => {
            this.$Message.success(response.data.message);
          })
          .catch(error => {
            this.$Message.error(error.response.data.message);
          })
      },
    }
  }
</script>

<style scoped>
.btn-status {
  opacity: 0.5;
  padding: .125rem .25rem;
  font-size: 0.75rem;
  color: #000000;
}
.btn-status:hover {
  opacity: 0.7;
  color: inherit;
}

.btn-status.active {
  opacity: 1;
  color: #ffffff;
  font-weight: bolder;
  -webkit-box-shadow: 0 0 12px -4px #000000;
  -moz-box-shadow: 0 0 12px -4px #000000;
  box-shadow: 0 0 12px -4px #000000;
  border : 0 !important
}

.btn-status.hold {
  background: #f66d9b;
}

.btn-status.complete {
  background: #38c172;
}

.btn-status.running {
  background: #4dc0b5;
}

.btn-status.cancel {
  background: #e3342f;
}

.ivu-icon-ios-arrow-forward::before {
  content: "\f115" !important;
}

</style>
