<template>
    <Menu width="auto"  theme="dark" active-name="1-2" :open-names="['1']" style="height: 100% !important;">
      <Scroll>
      <MenuItem name="1" class="d-flex" to="/admin">
          داشبورد
      </MenuItem>
      <MenuItem name="2" class="d-flex" :to="{ path: '/admin/contract', query: { contractCode : '' , customerName : '' , startYear : '' , contractTitle : '' , endYear : '' }}">
        لیست قراردادها
      </MenuItem>
      <MenuItem name="3" class="d-flex" to="/admin/customer"
                v-if="$store.getters['auth/can']('show-customer-contracts')">
        لیست مشتریان
      </MenuItem>
      <MenuItem name="4" class="d-flex" to="/admin/base-progress">
        مدیریت مراحل اصلی
      </MenuItem>
      <MenuItem name="5" class="d-flex" :to="{path : '/admin/report-customers' , query : { checklistTitle : '' , contractTitle : '' , contractCode : '' , status : '' , section : '' }}">
         گزارش قراردادها
      </MenuItem>
      <MenuItem name="51" class="d-flex" :to="{path : '/admin/number-list-sms/search' , query : { checklistTitle : '' , contractTitle : '' , contractCode : '' , status : '' , section : '' }}">
        اس ام اس
      </MenuItem>
      <Submenu name="6" v-if="$store.getters['auth/can']('show-checklist')">
        <template slot="title">
          <Icon type="ios-analytics" />
          مدیریت چک لیست ها
        </template>
        <MenuItem name="6-1" to="/admin/checklists"  v-if="$store.getters['auth/can']('show-checklist')">لیست چک لیست ها</MenuItem>
        <MenuItem name="6-2" to="/admin/checklists/language" v-if="$store.getters['auth/can']('show-role')">لیست زبان ها</MenuItem>
      </Submenu>
      <Submenu name="7"
               v-if="$store.getters['auth/can']('show-user') ||
                     $store.getters['auth/can']('show-role') ||
                     $store.getters['auth/can']('show-permission')||
                     $store.getters['auth/can']('show-all-user-todolist')||
                     $store.getters['auth/can']('show-office-user-todolist')||
                     $store.getters['auth/can']('show-programmer-user-todolist')||
                     $store.getters['auth/can']('show-graphic-user-todolist')||
                     $store.getters['auth/can']('show-support-user-todolist')||
                     $store.getters['auth/can']('show-sale-user-todolist')||
                     $store.getters['auth/can']('show-designer-user-todolist')">
        <template slot="title">
          <Icon type="ios-analytics" />
          مدیریت کاربران
        </template>
        <MenuItem name="7-1" to="/admin/users"  v-if="$store.getters['auth/can']('show-user')">لیست کاربران</MenuItem>
        <MenuItem name="7-2" to="/admin/users/role" v-if="$store.getters['auth/can']('show-role')">لیست نقش ها</MenuItem>
        <MenuItem name="7-3" to="/admin/users/permission" v-if="$store.getters['auth/can']('show-permission')">لیست سطح دسترسی ها</MenuItem>
        <MenuItem name="7-4" to="/admin/users/todoList"
                  v-if="$store.getters['auth/can']('show-all-user-todolist')||
                        $store.getters['auth/can']('show-office-user-todolist')||
                        $store.getters['auth/can']('show-programmer-user-todolist')||
                        $store.getters['auth/can']('show-graphic-user-todolist')||
                        $store.getters['auth/can']('show-support-user-todolist')||
                        $store.getters['auth/can']('show-sale-user-todolist')||
                        $store.getters['auth/can']('show-designer-user-todolist')">لیست کارهای کاربران</MenuItem>
        <MenuItem name="7-5" to="/admin/checklist-reverse">گزارشات ایراد و پیشنهاد</MenuItem>
      </Submenu>
      <Submenu name="8"
                 v-if="$store.getters['auth/can']('admin-handle-sms-templates') ||
                     $store.getters['auth/can']('show-sms-logs')">
          <template slot="title">
            <Icon type="ios-analytics" />
            مدیریت اس ام اس ها
          </template>
          <MenuItem name="8-1" to="/admin/manage-sms"  v-if="$store.getters['auth/can']('admin-handle-sms-templates')">لیست اس ام اس ها</MenuItem>
          <MenuItem name="8-2" :to="{path : '/admin/smsLogs'}" v-if="$store.getters['auth/can']('show-sms-logs')">گزارش اس ام اس ها</MenuItem>
        </Submenu>

      <MenuItem name="9" v-if="$store.getters['auth/can']('admin-edit-sections')"
                class="d-flex" to="/admin/sections">
        مدیریت بخش ها
      </MenuItem>
      <MenuItem name="10" to="/admin/profile/todos">
        <Icon type="ios-notifications" />
        <Badge :count="user.tasks" class="notify-badge">
          لیست کارهای شما
        </Badge>
      </MenuItem>
        <MenuItem v-if="$store.getters['auth/can']('show-training-session')" name="11" class="d-flex" :to="{path : '/admin/trainingSession'}">
          <Badge :count="user.trainingSessionCount" class="notify-badge">
          گزارش جلسات آموزش
          </Badge>
        </MenuItem>
        <MenuItem v-if="$store.getters['auth/can']('manage-task-times')" name="12" :to="{path : '/admin/taskTime'}" >
          لیست زمان بندی ها
        </MenuItem>
        <Submenu name="13">
          <template slot="title">
            <Icon type="ios-analytics" />
            لیست درخواست ها
          </template>
          <MenuItem name="13-1" to="/admin/request/changeTodoTime" >درخواست تغییر زمان انجام کارها</MenuItem>
        </Submenu>
        <MenuItem v-if="$store.getters['auth/can']('manager-show-requests')" name="14" class="d-flex" :to="{ path: '/admin/allRequests'}">
          <Badge :count="user.request_count" class="notify-badge">
          درخواست های ارسال شده
          </Badge>
        </MenuItem>
        <MenuItem v-if="$store.getters['auth/can']('admin-show-requests')" name="15" class="d-flex" :to="{ path: '/admin/staffRequests'}">
          <Badge  class="notify-badge">
            لیست همه درخواست ها
          </Badge>
        </MenuItem>
        <Submenu name="16"
                 v-if="$store.getters['auth/can']('show-taskLabel') || $store.getters['auth/can']('manage-tasks')">
          <template slot="title">
            <Icon type="ios-analytics" />
            مدیریت پروژه ها
          </template>
          <MenuItem name="16-1" to="/admin/task/label"  v-if="$store.getters['auth/can']('show-taskLabel')">لیست لیبل ها</MenuItem>
          <MenuItem name="16-2" to="/admin/task" v-if="$store.getters['auth/can']('manage-tasks')">لیست تسک ها</MenuItem>
        </Submenu>
      </Scroll>
    </Menu>

</template>
<script>
import {mapState} from "vuex";

export default {
    name : 'side-menu',
    computed:{
      ...mapState('auth' , ['user'])
    }
}
</script>
