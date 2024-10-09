<template>
  <Card title="گزارش">
    <List>
      <div class="border-bottom py-2">
        <div slot="description">
          <Collapse v-model="value1">
            <Panel v-for="(item , index) in checklistProcessList" :key="index" :name="`'${index}'`" >
                <span v-if="item.status === 1">
                  <i class="fa fa-check-circle text-warning"></i>
                  تایید بخش
                  <span>{{item.section.title}}</span>
                </span>
                <span v-if="item.status === 2">
                   <i class="fa fa-check-circle text-success"></i>
                  تایید مدیر بخش
                  {{item.section.title}}
                </span>
                <span v-if="item.status === 0">
                  <i class="fa fa-window-close text-danger"></i>
                   برگشت به بخش
                  {{item.section.title}}
                </span>
                <p slot="content" v-if="item.status === 0">
                  بخش
                  <span>"{{item.section.title}}"</span>
                  توسط
                  <span>
                    "{{item.user.name}}"
                  </span>
                  در تاریخ
                  <span>
                    {{item.date}}
                  </span>
                  برگشت خورد.
                  <br>
                   دلایل:
                  <span v-html="item.description"></span>

                </p>
                <p slot="content" v-else>
                  موارد چک لیست بخش
                  <span>"{{item.section.title}}"</span>
                  توسط
                  <span>
                    "{{item.user.name}}"
                  </span>
                  در تاریخ
                  <span>
                    {{item.date}}
                  </span>
                  <span v-if="item.status === 1">
                    در مدت زمان
                    {{item.duration}}
                  </span>
                  به تایید نهایی رسید.
                  <br>
                  توضیحات :
                  <span v-html="item.description"></span>
                </p>

            </Panel>
          </Collapse>
        </div>
      </div>
    </List>
  </Card>
</template>

<script>
  export  default {
    props : ['checklistProcessList'] ,
    name : 'contract-checklist-list' ,
    data() {
      return {
        value1: '0'
      }
    }
  }
</script>

