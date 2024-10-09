<template>
  <Tabs type="card" :animated="false" @on-click="filterReverseData">
    <TabPane label="همه" name="all">
      <reply-reverse v-for="(reverse , index) in process.reverse_data" :process_id="process.id"
                     :activeSection="activeSection"
                     :key="index" :reverse="reverse" :user="process.user" :level="1"/>
    </TabPane>
    <TabPane label="پاسخ دهید" name="notAnswered">
      <reply-reverse v-for="(reverse , index) in filter_reversed_data" :process_id="process.id"
                     :activeSection="activeSection"
                     :key="index" :reverse="reverse" :user="process.user" :level="1"/>
    </TabPane>
    <TabPane label="دیده شده ها" name="seen">
      <reply-reverse v-for="(reverse , index) in filter_reversed_data" :process_id="process.id"
                     :activeSection="activeSection"
                     :key="index" :reverse="reverse" :user="process.user" :level="1"/>
    </TabPane>
    <TabPane label="آرشیو"  name="archive">
      <reply-reverse v-for="(reverse , index) in filter_reversed_data" :process_id="process.id"
                     :activeSection="activeSection"
                     :key="index" :reverse="reverse" :user="process.user" :level="1"/>
    </TabPane>
  </Tabs>
</template>

<script>
import ReplyReverse from "./reply";
export default {
  name : 'reverse-reply' ,
  components : {ReplyReverse},
  props : ['process' , 'activeSection' , 'reverse' , ] ,
  data() {
    return{
      filter_reversed_data : [],
    }
  },
  methods :{
    filterReverseData(name) {
      if(name == 'seen') {
        this.filter_reversed_data = this.process.reverse_data.filter(x => {
          return x.seen == 1
        })
      }else if(name == 'notAnswered') {
        this.filter_reversed_data = this.process.reverse_data.filter(x => {
          return x.seen == 0 && x.replies.length == 0
        })
      }else if(name == 'archive') {
        this.filter_reversed_data = this.process.reverse_data.filter(x => {
          return x.replies.length > 0
        })
      }
    },
  }
}
</script>


