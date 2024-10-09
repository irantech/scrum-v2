<template>
  <Card :bordered="true" class="mb-2">
    <nuxt-link :to="`/admin/contract/${contract.id}/view`">
        <p class="mb-1 contract-option d-flex justify-content-between">
          <span v-if="contract.customer">
            {{ contract.customer.name }}
          </span>
          <span class="contract-date">
            [{{ contract.contract_code }}]
          </span>
        </p>
        <div class="contract-title-head">
          {{ contract.title }}

        </div>
    </nuxt-link>
    <div v-if="contract.checklistContract.length !== 0" class="contract-option my-2 p-2" style="border: 2px dotted; font-size: 12px">
      <div  v-for="(checklistContract , index) in contract.checklistContract" :key="index" class="py-1">
        <nuxt-link :to="`/admin/contract-checklist/${checklistContract.id}`" v-if="checklistContract.checklist">
          چک لیست
          {{checklistContract.checklist.title}} :
          <span v-if="checklistContract.section">
           بخش
          {{checklistContract.section.title}}
            <span>
               -->
            </span>
          {{getStatus(checklistContract.status)}}
        </span>
          <span v-else>------------</span>
        </nuxt-link>
      </div>
    </div>

<!--    <div v-if="checklists.length > 0" class="contract-option my-2 p-2" style="border: 2px dotted; font-size: 12px">-->
<!--      <Tree  :data="checklists"></Tree>-->
<!--    </div>-->

    <p class="text-left contract-option">{{contract.sign_date}}</p>
  </Card>
</template>

<script>
  export default {
    name : 'contractSingle' ,
    props : ['contract'] ,
    data () {
      return {
        design_status_list : [
          {
            id : 1 ,
            title : 'تایید کارمند'
          } ,
          {
            id : 2 ,
            title : 'تایید مدیر'
          } ,
          {
            id  :3 ,
            title : 'تایید پشتیبانی'
          } ,
          {
            id  : 4 ,
            title : 'امضای مدیر'
          } ,
          {
            id : 5 ,
            title : 'امضای کارمند'
          } ,
          {
            id : 6 ,
            title : 'امضای اولیه طرح'
          },
          {
            id  : 0 ,
            title : 'برگشت'
          }
        ] ,
        office_status_list : [
          {
            id : 2 ,
            title : 'تایید مدیر'
          } ,
          {
            id  : 4 ,
            title : 'امضای مدیر'
          } ,
        ] ,
        typical_status_list : [
          {
            id : 1 ,
            title : 'تایید کارمند'
          } ,
          {
            id : 2 ,
            title : 'تایید مدیر'
          } ,
          {
            id  : 4 ,
            title : 'امضای مدیر'
          } ,
          {
            id : 5 ,
            title : 'امضای کارمند'
          } ,
          {
            id  : 0 ,
            title : 'برگشت'
          }
        ] ,
        sale_status_list :[
          {
            id : 2 ,
            title : 'تایید مدیر'
          } ,
          {
            id  : 4 ,
            title : 'امضای مدیر'
          } ,
          {
            id  : 0 ,
            title : 'برگشت'
          }
        ] ,
        active_section : ''
      }
    },
    // computed:{
    //   checklists() {
    //      let data = [] ;
    //      let counter = 0 ;
    //     this.contract.checklistContract.forEach(item => {
    //         this.active_section = item.section
    //        if(item.checklist)
    //        {
    //          data.push( {
    //            title : item.checklist.title ,
    //            expand: false,
    //            contextmenu: true,
    //            render: (h, { root, node, data }) => {
    //              return h('span', {
    //                style: {
    //                  display: 'inline-block',
    //                  width: '100%'
    //                }
    //              }, [
    //                h('a', [
    //                  h('Icon', {
    //                    props: {
    //                      type: 'ios-clipboard-outline'
    //                    },
    //                    style: {
    //                      marginLeft: '8px',
    //                    },
    //
    //                  }),
    //                  h('span', {
    //                    on : {
    //                      click: () => { this.$router.push({path : `/admin/contract-checklist/${item.id}`}) }
    //                    },
    //                  },data.title) ,
    //                ])
    //              ]);
    //            },
    //            children: [],
    //          })
    //          item.checklist.sections.forEach(sect =>{
    //            let selected_section = this.$store.getters['admin/section/getSection'](sect.id)
    //
    //            data[counter].children.push({
    //              title: selected_section.title,
    //              render: (h, { data }) => {
    //                return h('span', {
    //                  style: {
    //                    display: 'inline-block',
    //                    width: '100%'
    //                  }
    //                }, [
    //                  h('a', [
    //                    h('Icon', {
    //                      props: {
    //                        type: 'ios-albums-outline'
    //                      },
    //                      style: {
    //                        marginLeft: '8px'
    //                      },
    //
    //                    }),
    //                    h('span' ,data.title) ,
    //                  ])
    //                ]);
    //              },
    //              expand: false,
    //              contextmenu: true,
    //              children: this.getChildren(this.active_section , sect.id , item.status)
    //            })
    //          })
    //          counter ++;
    //
    //        }
    //
    //
    //     })
    //     return data
    //   }
    // } ,
    methods : {
      getStatus(status){
        switch (status) {
          case 1 :
            return 'تایید کارمند';
          case 2 :
            return  'تایید مدیر';
          case 3 :
            return 'تایید پشتیبانی';
          case 4 :
            return 'امضای مدیر';
          case 5 :
            return  'امضای کارمند';
          case 6 :
            return 'امضای اولیه طرح';
          default :
                return 'برگشت'
        }
      },
      getIcon(status  , checklist_status) {
        if(checklist_status === 0 )
          return 'md-add-circle'
        else if(status.id === 0)
          return 'md-close'
        else if(status.id < checklist_status)
          return 'md-checkmark'
        else if(status.id === checklist_status)
          return 'md-code-working'
        else
          return 'md-close'
      },
      getColor(status  , checklist_status) {
        if(checklist_status === 0  || status.id === 0)
          return 'red'
        else if(status.id < checklist_status)
          return 'green'
        else if(status.id === checklist_status)
          return 'yellow'
        else
          return 'red'
      },
      getChildren (section , selected_section , checklist_status) {
        let list = []
        if(section.id === selected_section) {
          let active_status_list = []
          switch (section.order) {
            case 1 :
              active_status_list = this.office_status_list
              break;
            case 2 :
              active_status_list = this.design_status_list
              break;
            case 6 :
                  active_status_list = this.sale_status_list
                  break;
            default :
                active_status_list = this.typical_status_list

          }
          active_status_list.forEach(status => {
            list.push({
              title   : status.title ,
              render: (h, { root, node, data }) => {
                return h('span', {
                  style: {
                    display: 'inline-block',
                    width: '100%'
                  }
                }, [
                  h('a', [
                    h('Icon', {
                      props: {
                        type: this.getIcon(status , checklist_status)
                      },
                      style: {
                        marginLeft: '8px' ,
                        color : this.getColor(status , checklist_status)
                      },

                    }),
                    h('span' ,data.title) ,
                  ])
                ]);
              },
            })

          })
        }
        return  list
      }
    }
  }
</script>
