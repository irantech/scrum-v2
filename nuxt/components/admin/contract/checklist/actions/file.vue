<template>
  <div class="my-2 reverse-img position-relative">
    <a v-if="extension === 'pdf'" :href="file" target="_blank">
      <img src="~/assets/images/pdf.jpg" alt="pdf-file">
    </a>
    <img v-else :src="file" alt="file-image" @click="openImageModal(file)" height="130" class="cursor-pointer" style="max-width: 700px">
    <Icon @click="removeImage(file)" class="delete-img text-danger" type="md-close-circle" />
    <Modal v-model="imageModal" footer-hide class="reverse-img-modal" width="700">
      <div class="d-flex justify-content-center align-items-center">
        <img :src="file" alt="image-file" style="max-width: 700px">
      </div>
    </Modal>


  </div>
</template>

<script>
  export default {
    name : 'action-reverse-file' ,
    props : ['file' , 'reverse' , 'process_id'],
    data() {
      return{
        imageModal : false
      }
    },
    computed : {
      extension () {
        let ext =  this.file.split('.')
        return ext[ ext.length-1 ];
      }
    } ,
    methods : {
      openImageModal(){
        this.imageModal = true
      },
      removeImage() {
        this.$Modal.confirm({
          title: 'از حذف این فایل اطمینان دارید؟',
          okText: 'بله',
          cancelText: 'خیر',
          loading: true,
          onOk: () => {
            this.$axios.put(`reply/file/subTask/${this.reverse.id}`, {
              file: this.file
            }).then(res =>{
              this.$store.commit('admin/checklistContract/DELETE_CHECKLIST_PROCESS_REVERSE_ATTACH' , { newReverse : res.data.data , processId : this.process_id})
            }).catch(error =>{
              this.$Message.error(error.response.data.message)
            }).finally(() => {
                this.$Modal.remove();
            })
          },
          onCancel: () => {
            this.$Message.info('Clicked cancel');
          }
        });
      },
    }

  }
</script>
