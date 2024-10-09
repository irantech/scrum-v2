<template>
  <div>
    <div class="example-full">
      <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active drop-box">
        <div class="drop-content">
          <div style="border: 1px dashed #1d2124; " class="p-3">
            <h3>Drop files to upload</h3>
          </div>
        </div>
      </div>
      <div v-if="files.length!==0" class="row">
        <div class="col-lg-3 py-2 relative"  v-for="(file, index) in files" :key="file.id">
          <img v-if="file.thumb" :src="file.thumb" style="width: 100%"/>
          <span>
          <Progress :percent="parseInt(file.progress)" :stroke-width="20" status="active" text-inside />
        </span>
          <Button icon="ios-trash-outline" type="error" @click.prevent="removeImage(file)"  style="width: 24px; height: 24px;; position: absolute;right: 8px;top: 5px;" ></Button>
        </div>
      </div>
      <div class="upload" v-show="!isOption">
        <div class="example-foorer hidden">
          <Form v-model="form">
            <div class="row">
              <div class="col-2">
                <Select placeholder="type" v-model="form.status">
                  <Option v-for="(status , index) in  status_list" :key="index" :value="status.id">
                    {{status.title}}
                  </Option>
                </Select>
              </div>
              <div class="col-10">
                <div class="border d-flex border-radius">
                  <div class="btn-group">
                    <file-upload
                      class="d-flex align-items-center"
                      :custom-action="uploadImage"
                      :extensions="extensions"
                      :accept="accept"
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
                      ref="upload">
                      <Icon type="md-add-circle" shape="circle" style="font-size: 25px"></Icon>
                    </file-upload>
                  </div>
                  <FormItem prop="body" class="mb-0 w-100 custom-input">
                    <Input v-model="form.body" type="textarea" :autosize="{minRows: 1,maxRows: 20}"
                           placeholder="Enter something..." style="border-width: 0 !important;"></Input>
                  </FormItem>
                  <div class="btn-group">
                    <Button style="height: 32px !important;" type="success"  @click="createChecklistReverse" :loading="reverse_loading">save</Button>
                  </div>
                </div>
              </div>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name : 'channel-upload-file' ,
  props : ['multiple' , 'checklist_process'] ,
  data() {
    return {
      counter  : 0 ,
      updated_data : '',
      form: {
        body : '',
        status : ''
      },
      checklist_reverse_id : '' ,
      status_list : [
        {
          id    : 'offer' ,
          title : 'پیشنهاد'
        } ,
        {
          id    : 'error' ,
          title : 'ایراد'
        }
      ],
      reverse_loading : false,
      files: [],
      accept: 'image/png,image/gif,image/jpeg,image/webp,video/mp4,pdf',
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
      postAction: '',
      putAction: '',
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
    async uploadImage(file , component){
      file.postAction = this.putAction
      let result =  await component.uploadHtml5(file)
      this.updateChecklistReverse(result.response)
      return result
    },
    updateChecklistReverse(file_path) {
      this.$axios.put(`subTask/${this.checklist_reverse_id}` , {
        file : file_path
      }).then(res => {
        this.counter = this.counter + 1
        this.updated_data = res.data.data
        if(this.counter === this.files.length){
          this.$emit('uploadedFiles' , this.updated_data)
          this.resetData();
        }
      })
    },
    resetData() {
      this.reverse_loading = false
      this.form.body = ''
      this.form.status = ''
      this.files = []
      this.counter = 0
    },
    removeImage(file){
      this.$refs.upload.remove(file)
    },
    inputFilter(newFile, oldFile, prevent) {
      if (newFile && !oldFile) {
        // Before adding a file
        // 添加文件前

        // Filter system files or hide files
        // 过滤系统文件 和隐藏文件
        if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
          return prevent()
        }

        // Filter php html js file
        // 过滤 php html js 文件
        if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
          return prevent()
        }

        // Automatic compression
        // 自动压缩
        if (newFile.file && newFile.error === "" && newFile.type.substr(0, 6) === 'image/' && this.autoCompress > 0 && this.autoCompress < newFile.size) {
          newFile.error = 'compressing'
          const imageCompressor = new ImageCompressor(null, {
            convertSize: 1024 * 1024,
            maxWidth: 512,
            maxHeight: 512,
          })
          imageCompressor.compress(newFile.file)
            .then((file) => {
              this.$refs.upload.update(newFile, { error: '', file, size: file.size, type: file.type })
            })
            .catch((err) => {
              this.$refs.upload.update(newFile, { error: err.message || 'compress' })
            })
        }
      }


      if (newFile && newFile.error === "" && newFile.file && (!oldFile || newFile.file !== oldFile.file)) {
        // Create a blob field
        // 创建 blob 字段
        newFile.blob = ''
        let URL = (window.URL || window.webkitURL)
        if (URL) {
          newFile.blob = URL.createObjectURL(newFile.file)
        }

        // Thumbnails
        // 缩略图
        newFile.thumb = ''
        if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
          newFile.thumb = newFile.blob
        }
      }

      // image size
      // image 尺寸
      if (newFile && newFile.error === '' && newFile.type.substr(0, 6) === "image/" && newFile.blob && (!oldFile || newFile.blob !== oldFile.blob)) {
        newFile.error = 'image parsing'
        let img = new Image();
        img.onload = () => {
          this.$refs.upload.update(newFile, {error: '', height: img.height, width: img.width})
        }
        img.οnerrοr = (e) => {
          this.$refs.upload.update(newFile, { error: 'parsing image size'})
        }
        img.src = newFile.blob
      }
    },

    // add, update, remove File Event
    inputFile(newFile, oldFile) {

      if (newFile && oldFile) {
        // update

        if (newFile.active && !oldFile.active) {
          // beforeSend

          // min size
          if (newFile.size >= 0 && this.minSize > 0 && newFile.size < this.minSize) {
            this.$refs.upload.update(newFile, { error: 'size' })
          }
        }

        if (newFile.progress !== oldFile.progress) {
          // progress
        }

        if (newFile.error && !oldFile.error) {
          // error
        }

        if (newFile.success && !oldFile.success) {
          // this.$store.commit('channel/upload/ADD_FILE_TO_UPLOADED_FILES', newFile.response.data[0])
          // let index = this.files.indexOf(newFile)
          // this.files.splice(index , 1)
        }
      }


      if (!newFile && oldFile) {
        // remove
        if (oldFile.success && oldFile.response.id) {

          // $.ajax({
          //   type: 'DELETE',
          //   url: '/upload/delete?id=' + oldFile.response.id,
          // })
        }
      }


      // Automatically activate upload
      if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
        if (this.uploadAuto && !this.$refs.upload.active) {
          this.$refs.upload.active = true
        }
      }
      window.scrollTo({ left: 0, top: document.body.scrollHeight , behavior: "smooth" });
    },
    inputUpdate(files) {
      alert('dddd')
      console.log(files)
      //this.$store.commit('updateFiles', files)
    },
    alert(message) {
      alert(message)
    },
    onEditFileShow(file) {
      this.editFile = { ...file, show: true }
      this.$refs.upload.update(file, { error: 'edit' })
    },
    onEditorFile() {
      if (!this.$refs.upload.features.html5) {
        this.alert('Your browser does not support')
        this.editFile.show = false
        return
      }

      let data = {
        name: this.editFile.name,
        error: '',
      }
      if (this.editFile.cropper) {
        let binStr = atob(this.editFile.cropper.getCroppedCanvas().toDataURL(this.editFile.type).split(',')[1])
        let arr = new Uint8Array(binStr.length)
        for (let i = 0; i < binStr.length; i++) {
          arr[i] = binStr.charCodeAt(i)
        }
        data.file = new File([arr], data.name, { type: this.editFile.type })
        data.size = data.file.size
      }
      this.$refs.upload.update(this.editFile.id, data)
      this.editFile.error = ''
      this.editFile.show = false
    },
    // add folder
    onAddFolder() {
      if (!this.$refs.upload.features.directory) {
        this.alert('Your browser does not support')
        return
      }
      let input = document.createElement('input')
      input.style = "background: rgba(255, 255, 255, 0);overflow: hidden;position: fixed;width: 1px;height: 1px;z-index: -1;opacity: 0;"
      input.type = 'file'
      input.setAttribute('allowdirs', true)
      input.setAttribute('directory', true)
      input.setAttribute('webkitdirectory', true)
      input.multiple = true
      document.querySelector("body").appendChild(input)
      input.click()
      input.onchange = (e) => {
        this.$refs.upload.addInputFile(input).then(function() {
          document.querySelector("body").removeChild(input)
        })
      }
    },
    onAddData() {
      this.addData.show = false
      if (!this.$refs.upload.features.html5) {
        this.alert('Your browser does not support')
        return
      }
      let file = new window.File([this.addData.content], this.addData.name, {
        type: this.addData.type,
      })
      this.$refs.upload.add(file)
    } ,
    createChecklistReverse() {
      this.reverse_loading = true
      this.$axios.post( `subTask/create` , {
        body : this.form.body ,
        status  : this.form.status ,
        checklist_process  : this.checklist_process.id ,
      }).then(res=>{
        if(this.files.length === 0 ){
          this.$emit('uploadedFiles' , res.data.data)
          this.resetData();
        }
        else {
          this.checklist_reverse_id = res.data.data.id
          this.$refs.upload.active = true
        }
      })
    },
  } ,
  created() {
    this.putAction = `${this.$env.BASE_URL}uploadEditor`
    this.postAction =`${this.$env.BASE_URL}uploadEditor`
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
