<template>
  <div class="app-container">
    <client-only>
    <div class="app-left" id="side-menu">
      <adminIncludeSideMenu/>
    </div>
    <div class="app-right">
      <div class="wrapper">
        <adminIncludeTopMenu @sideMenu="showSideMenu"/>
      </div>
      <div class="wrapper-container container-fluid">
        <Nuxt/>
      </div>
    </div>
    </client-only>
  </div>
</template>

<script>
export  default  {
  name : 'admin' ,
  middleware: ['auth'],

  methods : {
    showSideMenu(value) {
      if(value){
        document.getElementById("side-menu").style.right = "0px";
        document.getElementById("side-menu").style.bottom = "0px";
      }
      else{
        document.getElementById("side-menu").style.right = "-245px";
        document.getElementById("side-menu").style.bottom = "0px";
      }

    }
  }
}
</script>

<style>

.app-left {
  width: 240px;
  position: fixed;
  top: 60px;
  bottom: 0;
  right: 0;
  transition: right .5s ;
}

.app-right {
  margin-right: 240px;
}

.navigate {
  width: 240px;
  font-size: 14px;
  padding: 10px 0;
  position: fixed;
  top: 64px;
  bottom: 0;
  left: 0;
  overflow: auto;
}

.wrapper {
  background: #eee;
  padding: 1px;
}

.wrapper-header {
  position: fixed;
  width: 100%;
  top: 0;
  right: 0;
  z-index: 1000;
}

.wrapper-container {
  background: #fff;
  margin: 60px auto 15px;

}


@media screen and (max-width: 990px) {
  .app-right {
    margin-right: 0;
  }
  .app-left {
    right: -240px;
  }
}

</style>
