<template>
    <div class="box-scrum col-6" >
      <div class="parent-scrum">
        <div class="scrum-items">
            <div class="parent-data w-100">
              <div class="scrum-users w-100">
                <span class="mb-2 d-flex flex-end">{{reverse.date}}</span>
                <div class="parent-return-user">
                  <div class="user-one">
                    <div class="coming-back">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M480 48c0-8.8 7.2-16 16-16s16 7.2 16 16V224c0 44.2-35.8 80-80 80H54.6L155.3 404.7c6.2 6.2 6.2 16.4 0 22.6s-16.4 6.2-22.6 0l-128-128c-6.2-6.2-6.2-16.4 0-22.6l128-128c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6L54.6 272H432c26.5 0 48-21.5 48-48V48z"/></svg>
                    </div>
                  </div>
                  <div class="user-one">
                    <img :src="`https://api.ladyscarf.ir/uploads/image/${reverse.user_section.avatar}`" alt="img-user">
                    <h3>{{reverse.user_section.name}}</h3>
                    <span class="side">({{reverse.section.title}})</span>
                  </div>
                </div>
                <div class="scrum-error mt-2">
                  <div class="error">
                    <Icon type="ios-close-circle-outline" />
                    <span class="ml-3"> تایید شده ها : {{reverse.error_reverse_data.accept_count}} عدد</span>
                    <span>رد شده ها :{{reverse.error_reverse_data.reject_count}} عدد</span>
                  </div>
                  <div class="proposal">
                    <Icon type="ios-checkmark-circle-outline" />
                    <span class="ml-3">تایید شده ها :{{reverse.offer_reverse_data.accept_count}} عدد</span>
                    <span>رد شده ها :{{reverse.offer_reverse_data.reject_count}} عدد</span>
                  </div>
                  <div class="peridic">
                    <Icon type="ios-analytics" />
                    <span class="ml-3">تایید شده ها:{{reverse.periodic_reverse_data.accept_count}} عدد</span>
                    <span>رد شده ها:{{reverse.periodic_reverse_data.reject_count}} عدد</span>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name : 'checklistReverseItem' ,
  props : ['reverse'] ,
  data () {
    return {
    }
  },
}
</script>

<style>
.parent-scrum{
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  /*! grid-column-gap: 20px; */
  /*! grid-row-gap: 20px; */
  background: #fcfcfc;
  border-radius: 12px;
  border: 1px solid #ccc;
}
.scrum-items{
  /*! background: #fff; */
  /*! border-radius: 12px; */
  /*! border: 1px solid #ccc; */
  display: flex;
  /*! flex-direction: column; */
  padding: 20px 15px;
  gap: 10px;
  position: relative;
  border-bottom: 2px dashed #ccc;
}
.scrum-items:last-child{
  border-bottom: none;
}



.scrum-title h2{
  font-size: 22px;
  margin-bottom: 0;
  color: #222;
}
.main-title span{
  font-size: 16px;
  color: #777;
}
.badge-title{
  /*! background: #cce8f457; */
  /*! border: 1px solid #057dca; */
  border-radius: 8px;
  color: #057dca;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 8px;
}
.coming-back svg{
  fill: #777;
  width: 20px !important;
  height: 20px !important;
  margin-left: 10px;
}
.scrum-users{
  display: flex;
  flex-direction: column;
  width: 100%;

}
.user-one{
  display: flex;
  align-items: center;
  gap: 5px;
}
.user-one img{
  width: 40px;
  height: 40px;
  border-radius: 50%;
}
.user-one h3{
  font-size: 18px;
  color: #333;
  margin-bottom: 0;
}
.side{
  font-size: 14px;
  color: #555;
}
.parent-data{
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.parent-coming-back-user{
  display: flex;
  align-items: center;
}
.scrum-users svg{
  width: 100px;
  height: 14px;
  fill: #999;
}
.scrum-error svg{
  width: 18px;
  /*! height: 20px; */
}
.error{
  /*! background: #ebc8c2; */
  color: #e32828;
  /*! border: 1px solid #b13e37; */
  /*! border-radius: 12px; */
  /*! padding: 5px 12px; */
  display: flex;
  align-items: center;
  font-size: 14px;
}

.proposal{
  /*! background: #def2d6; */
  /*! border-radius: 12px; */
  color: #008000;
  /*! border: 1px solid #586c50; */
  /*! padding: 5px 12px; */
  display: flex;
  align-items: center;
  font-size: 14px;
}
.peridic{
  /*! background: #def2d6; */
  /*! border-radius: 12px; */
  color: #8E20A1FF;
  /*! border: 1px solid #586c50; */
  /*! padding: 5px 12px; */
  display: flex;
  align-items: center;
  font-size: 14px;
}


.unconfirmed-error{
  /*! background: #f8f3d6; */
  /*! border-radius: 12px; */
  color: #9c6f2c;
  /*! border: 1px solid #9c6f2c; */
  /*! padding: 5px 12px; */
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
}
.unconfirmed-error svg{
  fill: #9c6f2c;
}
.scrum-error{
  display: flex;
  flex-direction : column;
  width: 100%;
}
.scrum{
  margin-top: 30px;
  margin-bottom: 30px;
}
.level{
  position: absolute;
  top: 20px;
  left: 20px;
  background: #fff;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 1px solid #333;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.parent-svg{
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  background: #ffbdbd33;
  border-radius: 10px;
}
.parent-svg svg{
  width: 50px;
  fill: #ff000078;
}
.parent-svg h3{
  font-size: 20px;
  color: #ff000078;
  margin-bottom: 0;
}
.parent-return-user{
  width: 100%;
  display: flex;
  align-items: center;
  gap: 1px;
}
.scrum-error strong{
  font-size: 16px;
}
.parent-svg-user-coming-back{
  /*! display: flex; */
  /*! flex-direction: column; */
  /*! align-items: center; */
  /*! justify-content: center; */
  /*! gap: 5px; */
  position: relative;
}
.parent-svg-user-coming-back span{
  font-size: 12px;
  color: #777;
  position: absolute;
  top: -10px;
  right: 50%;
  transform: translateX(50%);
}
.box-scrum{
  margin: 20px 0;
}

@media (max-width: 991px) {
  .coming-back{
    display: none;
  }
  .scrum-items{
    gap: 10px;
    padding: 10px;
  }
  .scrum-title h2{
    font-size: 18px;
  }
  .user-one img{
    display: none;
  }
  .parent-return-user{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: 1fr;
    grid-column-gap: 0;
    grid-row-gap: 0;
  }
  .user-one h3{
    font-size: 16px;
  }
  .side{
    font-size: 12px;
  }
  .parent-svg-user-coming-back{
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .scrum-error{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: 1fr;
    grid-column-gap: 0;
    grid-row-gap: 8px;
  }
  .scrum-users{
    gap: 20px;
  }
  .badge-title{
    padding: 0 5px;
  }
  .parent-svg-user-coming-back span{
    top: -16px;
  }
  .parent-svg h3{
    margin-bottom: 0;
    font-size: 20px;
  }
}

@media (max-width: 576px) {
  .scrum-items{
    flex-wrap: wrap;
    padding: 7px;
    gap: 15px;
  }
  .scrum-title h2{
    font-size: 16px;
  }
  .col-parent{
    padding-left: .25rem !important;
  }
  .badge-title{
    background: unset;
    border: unset;
    border-radius: unset;
  }
  .parent-return-user{
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    grid-template-rows: 1fr;
    grid-column-gap: 0;
    grid-row-gap: 30px;
  }
  .side {
    font-size: 14px;
  }
  .parent-svg-user-coming-back span{
    font-size: 13px;
    top: -18px;
  }
  .scrum-users svg{
    width: 70px;
  }
  .user-one h3{
    font-size: 18px;
  }
  .user-one{
    gap: 4px;
    justify-content: center;
  }
  .scrum-error{
    grid-template-columns: repeat(1, 1fr);
  }
  .error i svg{
    width: 10px !important;
    height: unset !important;
  }
  .parent-svg h3{
    margin-bottom: 0;
    font-size: 18px;
  }
  .parent-svg{
    padding: 5px;
  }
  .parent-svg svg{
    width: 40px;
  }
  .scrum-error strong{
    font-size: 14px;
  }
  .scrum-title{
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }
  .badge-title{
    font-size: 12px;
  }
  .user-one img{
    display: block;
  }
  .parent-svg-user-coming-back{
    transform: rotate(-90deg);
  }
  .scrum-users{
    gap: 20px;
  }
}
</style>
