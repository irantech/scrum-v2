<template>
    <adminCustomerList />
</template>

<script>

  export default  {
    name : 'costumer' ,
    layout : 'admin' ,
    middleware : 'customerListAccess',
  }
</script>
