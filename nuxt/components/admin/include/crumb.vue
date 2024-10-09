<template>
  <div class="content-header">
      <Row>
        <Col span="24">
          <h1 class="page-title"> {{ name }} </h1>
        </Col>

      </Row>
  </div>
</template>

<script>
  export default  {
    name : 'crumb' ,
    props : ['name']
  }
</script>

<style>
.content-header {
  padding: 15px .5rem;
}
.page-title {
  font-size: 16px;
  font-weight: 700;
}
</style>
