<template>
  <div>
    <div class="example-full">
      <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
        <h3>Drop files to upload</h3>
      </div>
      <div class="upload" v-show="!isOption">
        <div class="">
          <table class="table-auto table-hover" style="width: 100%" v-if="showZone">
            <tbody>
            <tr>
              <td colspan="9">
                <div class="text-center p-5 relative" style="border: 1px dashed">
                  <h4>Drop files anywhere to upload<br/>or</h4>
                  <label :for="name" class="btn btn-lg btn-primary">Select Files</label>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="example-foorer hidden">
          <div class="btn-group">

            <file-upload
              class="btn btn-primary btn-active"
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
              @input="$refs.upload.active = true"
              @update:modelValue="inputUpdate"
              @click="$refs.upload.active = true"
              ref="upload">

            </file-upload>

          </div>

        </div>
      </div>

    </div>

    <div class="grid grid-cols-6 gap-6" v-if="!showZone">
      <div class="col-lg-2 my-2.5 relative"  v-for="(file, index) in files" :key="file.id">
        <img v-if="file.thumb" :src="file.thumb" style="height: 100%" :class="{'opacity-25' : !file.success}"/>
        <span v-if="file.success" class="absolute top-1/2 right-1/2">
          <solid-check-circle-icon style="width: 30px; height: 30px"></solid-check-circle-icon>
        </span>
        <span v-else style="top: 50% ; right:40%" class="absolute">
          {{file.progress}}%
          <progress class="progress progress-success" :value="file.progress" max="100"></progress>
        </span>

        <button @click.prevent="removeImage(file)" v-if="file.success" class="absolute top-0 right-0">
          <solid-x-icon style="width: 30px; height: 30px"></solid-x-icon>
        </button>
      </div>

      <div class="col-lg-2 my-2.5" v-for="(file , index) in uploaded_files" :key="index">
        <div class="form-control">
          <label class="cursor-pointer label">
            <input v-if="multiple" type="checkbox" :value="file.id" checked="checked"
                   @change="setMedia"
                   class="checkbox checkbox-accent"
                   v-model="selectedFiles">
            <input v-else @change="setMedia"
                   type="radio" checked="checked" class="radio radio-xs" v-model="selectedFiles" :value="file.id">
          </label>
        </div>
        <img :src="file.link" :alt="file.title" style="height: 50%">
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name : 'channel-upload-file' ,
  props : ['uploaded_files' , 'multiple' , 'showZone' , 'close' , 'selectedImages' , 'extraData'] ,
  data() {
    return {
      selectedFiles : [] ,
      files: [],
      accept: 'image/png,image/gif,image/jpeg,image/webp,video/mp4',
      extensions: 'gif,jpg,jpeg,png,webp,mp4',
      // extensions: ['gif', 'jpg', 'jpeg','png', 'webp'],
      // extensions: /\.(gif|jpe?g|png|webp)$/i,
      minSize: 1024,
      size: 1024 * 1024 * 10,
      directory: false,
      drop: true,
      dropDirectory: true,
      createDirectory: false,
      addIndex: false,
      thread: 3,
      name: 'file',
      postAction: 'http://localhost:8000/api/v1/user/upload',
      putAction: 'http://localhost:8000/api/v1/user/upload',
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
      this.$emit('showZone' , false)
      file.postAction = this.putAction
      return await component.uploadHtml5(file)
      // console.log(file.file)
      // this.$store.dispatch('channel/upload/uploadedFiles' , {
      //   file : file.file
      // })
    },
    removeImage(file){
      this.$store.dispatch('channel/upload/removeFiles' , {
        id : file.response.data[0].id
      }).then(response=>{
        this.$refs.upload.remove(file)
      })
    },
    setMedia() {
      this.$emit('selectedMedia' , this.selectedFiles)

      console.log(this.selectedFiles)
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
          this.$store.commit('channel/upload/ADD_FILE_TO_UPLOADED_FILES', newFile.response.data[0])
          let index = this.files.indexOf(newFile)
          this.files.splice(index , 1)
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
    }
  } ,
  watch:  {
    close () {
      console.log('close' ,this.close)
      this.close ? this.selectedFiles = [] : ''
    } ,
    selectedImages() {
      console.log('selected' ,this.selectedImages)
      this.selectedImages ? this.selectedFiles = this.selectedImages : ''
    }
  },
  created() {
    this.selectedImages ? this.selectedFiles = this.selectedImages : this.selectedFiles = []
  }
}
</script>
