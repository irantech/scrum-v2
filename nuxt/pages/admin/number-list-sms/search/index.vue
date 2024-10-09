<template>
  <div>
    <div>

      <div class="box-style">
        <div class="box-style-padding">
          <h2 class="title">جستجوی شماره تلفن</h2>
          <div class="box_btn mt-auto">
            <button class="box_btn_buttons" ref="filterButtonIrantech" @click="setIrantech()">درخواست قیمت</button>
            <button class="box_btn_buttons" ref="filterButtonAutomation" @click="setAutomation()">اتوماسیون</button>
            <button class="box_btn_buttons" v-if="this.type === 'automation'" ref="filterButtonlost" @click="lostFilter('lost')">از دست رفته ها</button>
          </div>
          <div class="form-profile row">
            <div class="col-lg-4 col-md-6 col-12 py-2 style_form_filter">
              <Form ref="formInline">
                <FormItem prop="startDate">
                  <client-only>
                    <date-picker auto-submit format="jYYYY-jMM-jDD" popover="bottom-right" clearable v-model="form.startDate" placeholder="از تاریخ"/>
                  </client-only>
                </FormItem>
              </Form>
            </div>
            <div class="col-lg-4 col-md-6 col-12 py-2 style_form_filter">
              <Form ref="formInline">
                <FormItem prop="endDate">
                  <client-only>
                    <date-picker auto-submit format="jYYYY-jMM-jDD" popover="bottom-right" clearable v-model="form.endDate"  placeholder="تا تاریخ"/>
                  </client-only>
                </FormItem>
              </Form>
            </div>
            <div class="col-lg-4 col-md-6 col-12 py-2 style_form_filter_2">
              <select v-model="mobileArea" >
                <option value="">پیش شماره</option>
                <option v-for="option in mobileOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <label class="col-lg-4 col-md-6 col-12 py-2 label_style">
              <input type="text" placeholder="جست و جو" v-model="searchValue">
            </label>
            <div class="col-lg-4 col-md-6 col-12 py-2 style_form_filter_2">
              <select v-model="statusType" >
                <option value="all">وضعیت کلی</option>
                <option value="last">آخرین وضعیت</option>
                <option value="record">تاریخ ثبت قرارداد</option>
              </select>
            </div>
            <div v-if="this.type === 'irantech'" class="col-lg-4 col-md-6 col-12 py-2 style_form_filter_2">
              <Select v-model="selectedStatus" filterable multiple :max-tag-count="2" placeholder="وضعیت">
                <Option v-for="item in statusOptions" :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
            </div>
            <div v-if="this.type === 'automation'" class="col-lg-4 col-md-6 col-12 py-2 style_form_filter_2">
              <Select v-model="sitesTypeValue" filterable multiple :max-tag-count="2" placeholder="نوع خدمات">
                <Option v-for="item in sitesTypeValueArray" :value="item.value" :key="item.value">{{ item.label }}</Option>
              </Select>
            </div>
          </div>
          <div class="w-100 mt-4">
            <div class="box_btn d-flex justify-content-between align-items-center">
              <button class="box_btn_filter" @click="deleteFilters()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.1.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M82.15 39.08C88.75 34.61 96.71 32 105.3 32H534.3C557.3 32 576 50.67 576 73.7C576 83.3 572.7 92.6 566.6 100L408.1 294.6L630.8 469.1C641.2 477.3 643.1 492.4 634.9 502.8C626.7 513.2 611.6 515.1 601.2 506.9L9.196 42.89C-1.236 34.71-3.065 19.63 5.112 9.196C13.29-1.236 28.37-3.065 38.81 5.112L82.15 39.08zM134.4 80L370.3 264.1L521 80H134.4zM352 415.2V373.3L400 411.2V447.7C400 465.5 385.5 480 367.7 480C360.4 480 353.3 477.5 347.6 472.1L255.1 399.6C245.6 392 240 380.5 240 368.3V285.1L288 322.9V364.4L352 415.2z"/></svg></button>
              <download-excel class="box_btn_excel btn ml-3 mr-auto" :fetch="fetchData" :fields="json_fields" :before-generate="startDownload" :before-finish="finishDownload">
                دانلود اکسل
              </download-excel>
              <button class="box_btn_search" @click="handleSearch()">
                جستجو
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="this.dataLoading" class="bouncing-loader">
        <div class="spinner">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      </div>

      <div class="box-style">
        <div class="box-style-padding">
          <h2 class="title">پیامک</h2>
          <div class="form-profile form-profile-custom">
            <label class="label_style">
              <span>متن پیامک</span>
              <input v-model="smsText" type="text" placeholder="متن پیامک">
            </label>
            <div class="box_btn">
              <button disabled @click="sendSMS">ارسال پیامک</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- <adminSmsTemplateList :smsTemplates="smsTemplates" :loading="loading"/> -->
    <div>
      <div class="d-flex align-items-center justify-content-between">
        <adminIncludeCrumb name="اس ام اس و ..." />
        <div>
          تعداد نتایج: {{ customersCount }}
        </div>
      </div>
      <div v-if="this.customersCount === 0">
        <div class="fullCapacity_div">
          <img src="~/assets/images/no-data.jpg" alt="fullCapacity">
          <h2> اطلاعاتی یافت نشد.</h2>
        </div>
      </div>
      <div v-if="this.customersCount > 0">
        <adminAgencySearchList
          :agencies="customers"
          :perPage="perPage"
          :page="currentPage"
          :selectedPhoneNumbers="selectedPhoneNumbers"
          @setPage="getNewData"
          @selectedPhoneNumbers="togglePhoneNumber"
          :totalItems="customersCount"
          :dataLoading="dataLoading"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: 'number-list-sms',
  layout: 'admin',
  data() {
    return {
      json_fields: {
        'نام مدیر': 'manager',
        'نام آژانس': 'agency',
        'تلفن': 'phone',
      },
      statusOptions: [
        { label: 'عدم پاسخگویی', value: '1' },
        { label: 'صحبت اولیه', value: '2' },
        { label: 'در حال بررسی', value: '3' },
        { label: 'قرار حضوری', value: '4' },
        { label: 'مذاکره مثبت', value: '5' },
        { label: 'معلق', value: '8' },
        { label: 'پیگیری نشود', value: '9' },
        { label: 'مذاکره منفی', value: '10' },
      ],
      sitesTypeValueArray: [
        { label: 'طراحی سایت آژانس', value: 'طراحی سایت آژانس' },
        { label: 'اتوماسیون', value: 'اتوماسیون' },
        { label: 'باشگاه مشتریان', value: 'باشگاه مشتریان' },
        { label: 'Safar 360', value: 'Safar 360' },
        { label: 'سفربانک', value: 'سفربانک' },
        { label: 'طراحی سایت هتل', value: 'طراحی سایت هتل' },
        { label: 'رزرواسیون هتل', value: 'رزرواسیون هتل' },
        { label: 'طراحی سایت عمومی', value: 'طراحی سایت عمومی' },
        { label: 'فروش sms', value: 'فروش sms' },
        { label: 'سئو', value: 'سئو' },
        { label: 'آموزش', value: 'آموزش' },
        { label: 'اپلیکیشن', value: 'اپلیکیشن' },
        { label: 'رزرواسیون چارتری', value: 'رزرواسیون چارتری' },
        { label: 'API', value: 'API' },
        { label: 'رزرواسیون ویزا', value: 'رزرواسیون ویزا' },
        { label: 'رزرواسیون تور', value: 'رزرواسیون تور' },
        { label: 'khazarpitch.com', value: 'khazarpitch.com' },
        { label: 'رزرواسیون گردشگری', value: 'رزرواسیون گردشگری' }
      ],
      selectedStatus: [], // Initialize as an empty array to hold selected values
      form: {
        startDate : null ,
        endDate : null
      },
      loading : false,
      selectedPhoneNumbers: [
        "unselect"
      ],
      mobileOptions: [
        { label: '0990', value: '0990' },
        { label: '0991', value: '0991' },
        { label: '0992', value: '0992' },
        { label: '0993', value: '0993' },
        { label: '0994', value: '0994' },
        { label: '0910', value: '0910' },
        { label: '0911', value: '0911' },
        { label: '0912', value: '0912' },
        { label: '0913', value: '0913' },
        { label: '0914', value: '0914' },
        { label: '0915', value: '0915' },
        { label: '0916', value: '0916' },
        { label: '0917', value: '0917' },
        { label: '0918', value: '0918' },
        { label: '0919', value: '0919' },
        { label: '0911', value: '0911' },
        { label: '0912', value: '0912' },
        { label: '0913', value: '0913' },
        { label: '0914', value: '0914' },
        { label: '0915', value: '0915' },
        { label: '0916', value: '0916' },
        { label: '0917', value: '0917' },
        { label: '0918', value: '0918' },
        { label: '0919', value: '0919' },
        { label: '0930', value: '0930' },
        { label: '0933', value: '0933' },
        { label: '0935', value: '0935' },
        { label: '0936', value: '0936' },
        { label: '0937', value: '0937' },
        { label: '0938', value: '0938' },
        { label: '0939', value: '0939' },
        { label: '0901', value: '0901' },
        { label: '0902', value: '0902' },
        { label: '0903', value: '0903' },
        { label: '0904', value: '0904' },
        { label: '0905', value: '0905' },
        { label: '0920', value: '0920' },
        { label: '0921', value: '0921' },
        { label: '0922', value: '0922' },
      ],
      labelOptions: [
        { label: 'پیش شماره', value: 'mob_manager' },
        { label: 'وضعیت', value: 'status' }
      ],
      selectedLabel: '',
      smsText: '',
      searchValue: '',
      sitesTypeValue: '',
      statusType: 'all',
      dataLoading: false,
      perPage: 100,
      items: [],
      currentPage: 1,
      type: "all",
      lost: "",
      mobileArea: "",
      filters: {
        type: "all",
        label: "all",
        like: "all"
      },
      status: [],
      mob_manager: [] ,
      name: [] ,
      visible: false,
    }
  },
  created() {
    this.deleteFilters();
    this.handleSearch();
  },
  methods: {
    getNewData(page) {
      this.currentPage = page
      this.getCustomers()
    },
    togglePhoneNumber(array) {
      this.selectedPhoneNumbers = array
    },
    claerPhoneNumbers() {
      this.selectedPhoneNumbers = ['unselect'];
    },
    getCustomers() {
      this.dataLoading = true
      this.$store.dispatch('admin/agency/agencies/getCustomers', {
        page: this.currentPage,
        perPage: this.perPage,

        type: this.type,
        lost: [this.lost] ,

        statusType: this.statusType ,
        status: [this.selectedStatus.join(',')] ,

        startDate: this.form.startDate ,
        endDate: this.form.endDate ,

        mob_manager: this.mob_manager ,
        name: this.name ,
        sites: this.sitesTypeValue ,
      })
        .then(() => {
          this.dataLoading = false
        })
    },
    setIrantech() {
      const buttonAutomation = this.$refs.filterButtonAutomation;
      const buttonIrantech = this.$refs.filterButtonIrantech;
      if (this.type != 'irantech') {
        this.type = 'irantech'
        buttonIrantech.classList.toggle('active');
        buttonAutomation.classList.remove('active');
      } else {
        this.type = null;
        buttonAutomation.classList.remove('active');
        buttonIrantech.classList.remove('active');
      }
    },
    setAutomation() {
      const buttonAutomation = this.$refs.filterButtonAutomation;
      const buttonIrantech = this.$refs.filterButtonIrantech;
      if (this.type != 'automation') {
        this.type = 'automation';
        buttonAutomation.classList.toggle('active');
        buttonIrantech.classList.remove('active');
      } else {
        this.type = null;
        buttonAutomation.classList.remove('active');
        buttonIrantech.classList.remove('active');
      }
    },
    lostFilter() {
      if (this.lost != 'lost') {
        this.lost = 'lost';
      } else {
        this.lost = [];
      }
      const buttonElement = this.$refs.filterButtonlost;
      buttonElement.classList.toggle('active');
    },
    deleteFilters() {
      this.request_data = {};
      this.selectedStatus = [];
      this.form.startDate = null;
      this.form.endDate = null;
      this.type = "all";
      this.lost = [];
      this.mobileArea = "";
      this.searchValue = "";
      this.sitesTypeValue = "";
      this.claerPhoneNumbers();
      const buttons = document.querySelectorAll('.active');
      buttons.forEach((button) => {
        button.classList.remove('active');
      });
    },
    handleSearch() {
      const containsOnlyNumbers = /^\d+$/.test(this.searchValue);
      this.currentPage = 1;
      if (this.mobileArea) {
        this.mob_manager = [this.mobileArea];
      } else {
        this.mob_manager = [];
      }
      if (containsOnlyNumbers) {
        this.mob_manager.push(this.searchValue);
      } else {
        this.name= [this.searchValue];
      }
      this.getCustomers();
    },
    handleArrayData(array) {
      this.selectedPhoneNumbers = array; // Update the received array data in the parent component
    },
    async sendSMS() {
      try {
        const response = await this.$axios.post('https://api.ladyscarf.ir/api/curl/sms', {
           phoneNumbers: this.selectedPhoneNumbers,
            massage: this.smsText ,
            page: false,
            perPage: false,

            type: this.type,
            lost: [this.lost].join(",") ,

            statusType: this.statusType ,
            status: [this.selectedStatus.join(',')].join(",") ,

            startDate: this.form.startDate ,
            endDate: this.form.endDate ,

            mob_manager: this.mob_manager.join(",") ,
            name: this.name.join(",") ,
        });
        // console.log(response.data.info);
        this.$Message.success(response.data.info);
      } catch (error) {
        // Handle the error
        console.error(error);
        this.$Message.success(error);
      }
    },
    async fetchData() {
       const response = await this.$axios.post('https://api.ladyscarf.ir/api/curl/excel', {
           phoneNumbers: this.selectedPhoneNumbers,
           page: false,
           perPage: false,

           type: this.type,
           lost: [this.lost].join(",") ,

           statusType: this.statusType ,
           status: [this.selectedStatus.join(',')].join(",") ,

           startDate: this.form.startDate ,
           endDate: this.form.endDate ,

           mob_manager: this.mob_manager.join(",") ,
           name: this.name.join(",") ,
        });
      console.log(response.data.data)
      return response.data.data
    },
    startDownload() {
      this.dataLoading = true;
    },
    finishDownload() {
      this.dataLoading = false;
    }
  },
  computed: {
    ...mapState('admin/agency/agencies', ['customers']),
    ...mapState('admin/agency/agencies', ['customersCount']),
    todayDate() {
      return  this.$moment().utc().format('jYYYY-jMM-jDD')
    }
  }
}
</script>
