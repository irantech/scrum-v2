<template>
  <div>
    <div class="example-full">

      <div v-show="$refs[`upload-${setRef}`] && $refs[`upload-${setRef}`].dropActive" class="drop-active drop-box">
        <div class="drop-content">
          <div style="border: 1px dashed #1d2124; " class="p-3">
            <h3>Drop files to upload</h3>
          </div>
        </div>
      </div>

      <div v-if="files.length!==0" class="row">

        <div class="col-lg-3 py-2 relative"  v-for="(file, index) in files" :key="index">
          <img v-if="file.type === 'application/pdf'"  src="~/assets/images/pdf.jpg" :alt="file.name"/>
          <img v-if="file.thumb" :src="file.thumb"  :alt="file.name" style="width: 100%"/>
          <span>
          <Progress :percent="parseInt(file.progress)" :stroke-width="20" status="active" text-inside />
        </span>
          <Button icon="ios-trash-outline" type="error" @click.prevent="removeImage(file)"  style="width: 24px; height: 24px;; position: absolute;right: 8px;top: 5px;" ></Button>
        </div>
      </div>
      <div class="upload" v-show="!isOption">
        <div class="example-foorer hidden">
          <div class="border d-flex border-radius">
              <div class="btn-group flex-column">
                <file-upload
                  class="d-flex align-items-center"
                  :custom-action="uploadImage"
                  :extensions="extensions"
                  :accept="accept"
                  :input-id="`file${setRef}`"
                  :multiple="multiple"
                  :directory="directory"
                  :create-directory="createDirectory"
                  :size="size || 0"
                  :thread="thread < 1 ? 1 : (thread > 5 ? 5 : thread)"
                  :headers="headers"
                  :drop="drop"
                  :drop-directory="dropDirectory"
                  :add-index="addIndex"
                  v-model="files"
                  @input-filter="inputFilter"
                  @input-file="inputFile"
                  @update:modelValue="inputUpdate"
                  :ref="`upload-${setRef}`" :id="`upload-${setRef}`">
                  <Icon type="md-add-circle" shape="circle" style="font-size: 25px"></Icon>
                </file-upload>
              </div>
              <FormItem prop="body" class="mb-0 w-100 custom-input">
                <Input v-model="body" type="textarea" :autosize="{minRows: 1,maxRows: 20}"
                       placeholder="Enter something..." @on-keydown="inputHandler"  @input="setBodyField" style="border-width: 0 !important;"></Input>
              </FormItem>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name : 'scrum-input' ,
    props : ['multiple' , 'setRef'],
    data() {
      return {
        counter  : 0 ,
        updated_data : '',
        body : '',
        checklist_reverse_id : '' ,
        reverse_loading : false,
        files: [],
        accept: 'image/png,image/gif,image/jpeg,image/webp,video/mp4,application/pdf',
        extensions: 'gif,jpg,jpeg,png,webp,mp4,pdf',
        minSize: 1024,
        size: 1024 * 1024 * 10,
        directory: false,
        drop: true,
        dropDirectory: true,
        createDirectory: false,
        addIndex: false,
        thread: 3,
        name: 'file',
        postAction: `${this.$env.BASE_URL}uploadEditor`,
        putAction: `${this.$env.BASE_URL}uploadEditor` ,
        headers: {
          'Authorization': "Bearer " + this.$cookies.get('token'),
          'X-Requested-With' : 'XMLHttpRequest'
        },
        autoCompress: 1024 * 1024,
        uploadAuto: false,
        isOption: false,
      }
    },
    methods: {
      inputHandler(e) {
        if (e.keyCode === 13 && !e.shiftKey) {
          e.preventDefault();
          this.$emit('submitForm')
        }
      },
      setValue(payload) {
        this.checklist_reverse_id = payload.checklist_reverse_id
        this.$refs[`upload-${this.setRef}`].active = payload.active
      },
      setBody(value){
        this.body = value
      },
      setBodyField() {
        this.$emit('bodyData' , this.body)
      },
      async uploadImage(file , component){
        file.postAction = this.putAction
        let result =  await component.uploadHtml5(file)
        this.$emit('update' , result.response)
        return result
      },
      resetData() {
        this.body = ''
        this.files = []
      },
      removeImage(file){
        this.$refs[`upload-${this.setRef}`].remove(file)
      },
      inputFilter(newFile, oldFile, prevent) {
        if (newFile && !oldFile) {
          if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
            return prevent()
          }
          if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
            return prevent()
          }
          if (newFile.file && newFile.error === "" && newFile.type.substr(0, 6) === 'image/' && this.autoCompress > 0 && this.autoCompress < newFile.size) {
            newFile.error = 'compressing'
            const imageCompressor = new ImageCompressor(null, {
              convertSize: 1024 * 1024,
              maxWidth: 512,
              maxHeight: 512,
            })
            imageCompressor.compress(newFile.file)
              .then((file) => {
                this.$refs[`upload-${this.setRef}`].update(newFile, { error: '', file, size: file.size, type: file.type })
              })
              .catch((err) => {
                this.$refs[`upload-${this.setRef}`].update(newFile, { error: err.message || 'compress' })
              })
          }
        }


        if (newFile && newFile.error === "" && newFile.file && (!oldFile || newFile.file !== oldFile.file)) {
          newFile.blob = ''
          let URL = (window.URL || window.webkitURL)
          if (URL) {
            newFile.blob = URL.createObjectURL(newFile.file)
          }
          newFile.thumb = ''
          if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
            newFile.thumb = newFile.blob
          }
        }
        if (newFile && newFile.error === '' && newFile.type.substr(0, 6) === "image/" && newFile.blob && (!oldFile || newFile.blob !== oldFile.blob)) {
          newFile.error = 'image parsing'
          let img = new Image();
          img.onload = () => {
            this.$refs[`upload-${this.setRef}`].update(newFile, {error: '', height: img.height, width: img.width})
          }
          img.οnerrοr = (e) => {
            this.$refs[`upload-${this.setRef}`].update(newFile, { error: 'parsing image size'})
          }
          img.src = newFile.blob
        }
      },

      // add, update, remove File Event
      inputFile(newFile, oldFile) {
        this.$emit('setFileCount' , this.files.length)
        if (newFile && oldFile) {
          if (newFile.active && !oldFile.active) {
            if (newFile.size >= 0 && this.minSize > 0 && newFile.size < this.minSize) {
              this.$refs[`upload-${this.setRef}`].update(newFile, { error: 'size' })
            }
          }

          if (newFile.progress !== oldFile.progress) {
            // progress
          }

          if (newFile.error && !oldFile.error) {
            // error
          }

          if (newFile.success && !oldFile.success) {
          }
        }


        if (!newFile && oldFile) {
          // remove
          if (oldFile.success && oldFile.response.id) {

          }
        }


        // Automatically activate upload
        if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
          if (this.uploadAuto && !this.$refs[`upload-${this.setRef}`].active) {
            this.$refs[`upload-${this.setRef}`].active = true
          }
        }
        // window.scrollTo({ left: 0, top: document.body.scrollHeight , behavior: "smooth" });
      },
      inputUpdate(files) {
        alert('dddd')
      },
      alert(message) {
        alert(message)
      },
    }
  }
</script>


<style>
.drop-box{
  position: fixed;
  overflow: auto;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 999999999999999999999999999999999;
  outline: 0;
  background-color: rgba(55,55,55,.6);
}
.drop-content{
  margin: 0 auto;
  position: relative;
  outline: 0;
  top: 100px;
  box-sizing: border-box;
  color: #000000;
  background: #ffffff;
  padding: 10px;
  border-radius: 5px;
  width: 500px;
  text-align: center;
}
.image-box{
  position: absolute;
  background: #fff;
  right: 30px;
  left: 30px;
  border: 1px solid #dcdee2;
  padding: 10px
}
</style>
