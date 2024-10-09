<template>
  <div>
    <table class="table table-bordered table-striped">
      <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">نام</th>
        <th scope="col">شماره تلفن</th>
        <th scope="col"> وضعیت</th>
        <th scope="col">از دست رفته</th>
        <th scope="col">انتخاب
          <input @click="checkAll"  :checked="checkAllInput()" type="checkbox" />
        </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, index) in agencies" :key="index">
        <th scope="row">{{ (page - 1 )*perPage + 1 + index }}</th>
        <td>{{ item.manager_name }}</td>
        <td>{{ item.mob_manager[0] }}</td>
        <td>{{ status[item.status] }}</td>
        <td>{{ lost[item.lost] }}</td>
        <td>

          <input
            :data-value="item.mob_manager"
            data-check="phoneNumbers"
            type="checkbox"
            :checked="!isSelected(item.mob_manager[0])"
            @change="togglePhoneNumber(item.mob_manager[0])"
          />
        </td>
      </tr>
      </tbody>
    </table>
    <Page v-if="totalItems" :total="totalItems" class="text-center mt-3"
          :page-size="perPage"
          @on-change="getNewDate" :disabled="dataLoading"  @on-page-size-change="getPerPage" show-sizer  />
  </div>
</template>

<script>
export default {
  name: 'agency-agencies',
  headerCheckbox: true,
  props: ['agencies' , 'totalItems' , 'perPage' , 'dataLoading' , 'page' ,'selectedPhoneNumbers'],

  data() {
    return {
      status: {
        null: "",
        "": "",
        "1": "عدم پاسخگویی",
        "2": "صحبت اولیه",
        "3": "در حال بررسی",
        "4": "قرار حضوری",
        "5": "مذاکره مثبت",
        "6": "مذاکره منفی",
        "8": "معلق",
        "9": "پیگیری نشود",
        "10": "مذاکره منفی",
      },
      lost: {
        "" : '',
        null: '',
        "lost": 'از دست رفته'
      }
    }
  },
  methods : {
    getNewDate(page){
      this.$emit('setPage' , page)
    },
    getPerPage(perPage){
      this.$emit('setPerPage' , perPage)
    },
    togglePhoneNumber(test) {
      const index = this.selectedPhoneNumbers.indexOf(test);
      if (index !== -1) {
        this.selectedPhoneNumbers.splice(index, 1);
      } else {
        this.selectedPhoneNumbers.push(test)
      }
      this.$emit('selectedPhoneNumbers' , this.selectedPhoneNumbers)
      console.log(this.selectedPhoneNumbers);
    },
    checkAll() {
      if (this.selectedPhoneNumbers[0] == 'unselect') {
        this.selectedPhoneNumbers = ["select"]
      } else {
        this.selectedPhoneNumbers = ["unselect"]
      }
      this.$emit('selectedPhoneNumbers' , this.selectedPhoneNumbers)
    },
    checkAllInput() {
      if (this.selectedPhoneNumbers[0] == 'unselect') {
        return true;
      } else {
        return false;
      }
      this.$emit('selectedPhoneNumbers' , this.selectedPhoneNumbers)
    },
    isSelected(value) {
      if (this.selectedPhoneNumbers[0] == 'unselect') {
        return this.selectedPhoneNumbers.includes(value);
      } else {
        return !this.selectedPhoneNumbers.includes(value);
      }
    }

  },
}
</script>
