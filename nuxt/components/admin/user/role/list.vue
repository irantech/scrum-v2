<template>
  <div>
    <div class="col-12 col-md-9 m-auto">
      <Card>
        <p slot="title">
          لیست نقش ها
        </p>
        <p slot="extra" v-if="$store.getters['auth/can']('create-role')">
          <Button type="success" @click="openCreateModal">
            افزودن نقش جدید
          </Button>
        </p>
        <List :loading="roleLoading">
          <ListItem v-for="(role , index) in roleList" :key="index">
            <ListItemMeta :title="role.title" :description="role.section ? role.section.title : ''"/>
            <template slot="action">
              <li class="text-success" v-if="!role.trashed && $store.getters['auth/can']('update-role')">
                <a @click="openUpdateModal(role.id)">
                  ویرایش
                </a>
              </li>
              <li class="text-info" v-if="role.trashed && $store.getters['auth/can']('restore-role')">
                <a @click="restoreModal = true ; active_role = role.id">
                  بازگردانی
                </a>
              </li>
              <li class="text-danger" v-if="!role.trashed && $store.getters['auth/can']('delete-role')">
                <a @click="deleteModel = true ; active_role = role.id">
                  حذف
                </a>
              </li>
            </template>
          </ListItem>
        </List>
      </Card>
    </div>

    <Modal v-model="createModal" title="نقش جدید" width="500" footer-hide>
      <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top"
        @submit.native.prevent="handleSubmit('formValidate')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formValidate.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="نوع">
            <Select v-model="formValidate.type" placeholder="انتخاب کنید">
              <Option value="employee">کارمند</Option>
              <Option value="manager">مدیر</Option>
              <Option value="admin">ادمین</Option>
              <Option value="customer">مشتری</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="بخش های مربوطه" prop="section">
            <Select v-model="formValidate.section" placeholder="انتخاب کنید">
              <Option v-for="(section , index) in sectionList"  :key="index" :value="section.id">{{ section.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="createLoading" @click="handleSubmit('formValidate')">ثبت اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>

    <Modal v-model="deleteModel" width="360">
      <p slot="header" class="text-center text-warning">
        <Icon type="ios-information-circle"></Icon>
        <span>حذف نقش انتخابی</span>
      </p>
      <div class="text-center">
        <p>آیا از حذف این نقش اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="error" size="large" long :loading="deleteLoading" @click="removeRole()">حذف</Button>
      </div>
    </Modal>
    <Modal v-model="restoreModal" width="360">
      <p slot="header" class="text-center text-info">
        <Icon type="ios-information-circle"></Icon>
        <span>بازگرداندن نقش انتخابی</span>
      </p>
      <div style="text-align:center">
        <p>آیا از بازگرداندن این نقش اطمینان دارید؟</p>
      </div>
      <div slot="footer">
        <Button type="info" size="large" long :loading="restoreLoading" @click="restoreRole()">بازگرداندن</Button>
      </div>
    </Modal>
    <Modal v-model="updateModal" :title="` ویرایش نقش ` " width="500" footer-hide>
      <Form ref="formEdit" :model="formEdit" :rules="ruleEdit" label-position="top"
        @submit.native.prevent="updateRole('formEdit')">
        <div class="col-12">
          <FormItem label="عنوان" prop="title">
            <Input v-model="formEdit.title" type="text" placeholder="عنوان را وارد کنید..."></Input>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="نوع">
            <Select v-model="formEdit.type" placeholder="انتخاب کنید">
              <Option value="employee" >کارمند</Option>
              <Option value="manager">مدیر</Option>
              <Option value="admin">ادمین</Option>
              <Option value="customer">مشتری</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="بخش های مربوطه" prop="section">
            <Select v-model="formEdit.section" placeholder="انتخاب کنید">
              <Option v-for="(section , index) in sectionList"  :key="index"
                      :value="section.id">{{ section.title }}</Option>
            </Select>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem label="دسترسی ها" prop="permission" >
            <CheckboxGroup v-model="formEdit.permission" class="row">
              <Checkbox class="col-6" v-for="(permission , index) in permissionList"
                        :key="index" :label="permission.id">{{permission.label}}</Checkbox>
            </CheckboxGroup>
          </FormItem>
        </div>
        <div class="col-12">
          <FormItem>
            <Button type="primary" :loading="updateLoading" @click="updateRole('formEdit')">ویرایش اطلاعات</Button>
          </FormItem>
        </div>
      </Form>
    </Modal>

  </div>
</template>

<script>
  export default {
    name : 'user-role-list',
    props : ['roleList' , 'permissionList' , 'roleLoading' , 'sectionList'] ,
    data() {
      return {
        createModal : false,
        createLoading : false ,
        formValidate : {
          title : '' ,
          section : '',
          type : 'employee'
        },
        ruleValidate : {
          title: [
            { required: true, message: 'فیلد عنوان الزامی است.', trigger: 'change' }
          ],
        },

        updateModal : false ,
        updateLoading : false,
        active_role : '',
        deleteModel : false ,
        deleteLoading : false ,
        restoreModal : false,
        restoreLoading : false,

        formEdit: {
          title: '',
          permission : [] ,
          section : '' ,
          type: 'employee'
        },
        ruleEdit: {
          title: [
            { required: true, message: 'یک عنوان وارد کنید', trigger: 'change' }
          ]
        },


      }
    },
    methods : {
      removeRole() {
        this.deleteLoading = true
        this.$store.dispatch('admin/user/role/removeUserRole', {id: this.active_role})
          .then(response => {
            this.deleteLoading = false
            this.$Message.warning(response.data.message);
            this.deleteModel = false
          })
          .catch(error => {
            this.deleteLoading = false
            this.$Message.error(error.message);
          })
      },
      restoreRole() {
        this.restoreLoading = true
        this.$store.dispatch('admin/user/role/restoreUserRole', {id: this.active_role})
          .then(response => {
            this.restoreLoading = false
            this.$Message.warning(response.data.message);
            this.restoreModal = false
          })
          .catch(error => {
            this.restoreLoading = false
            this.$Message.error(error.message);
          })
      },
      openUpdateModal(role_id) {
        this.active_role = role_id
        this.$store.dispatch('admin/user/role/getUserRole' , {
          id : this.active_role
        }).then(res => {
          this.new_role = res.data.data
          this.updateModal = true
          this.formEdit.title = this.new_role.title
          this.formEdit.type = this.new_role.type
          this.formEdit.section = this.new_role.section ? this.new_role.section.id : ''
          this.formEdit.permission = this.new_role.permissions.map(x => x.id)
        })

      },


      updateRole(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.updateLoading = true
            this.$store.dispatch('admin/user/role/updateUserRole', {
              id: this.active_role,
              form: this.formEdit
            })
              .then(response => {
                this.updateModal = false
                this.updateLoading = false
                this.$Message.success(response.data.message);
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

      openCreateModal() {
        this.createModal = true
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.createLoading = true
            this.$store.dispatch('admin/user/role/createNewUserRole' , { form : this.formValidate })
              .then(response => {
                  this.$Message.success(response.data.message)
                  this.createModal = false
                  this.createLoading = false
                  this.handleReset(name)
                }
              )
              .catch(error => {
                console.log(error)
                this.$Message.error(error.message);
                this.createLoading = false
              })
          } else {
            this.$Message.error('ابتدا خطاهای خود را بررسی کنید.');
          }
        })
      },
      handleReset (name) {
        this.$refs[name].resetFields();
      }
    }
  }
</script>
