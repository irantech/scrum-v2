export default function ({ $axios, redirect , app }) {
  $axios.onRequest(config => {
    config.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    //config.headers.common['X-CSRF-TOKEN'] ="eyJpdiI6IlI1THVqT1dJM3pjNzhlbkhnalZ3bVE9PSIsInZhbHVlIjoiWXQ0cFZScTVVUUdsQktwNlU2dHRDV3IzVmFTcmZteGQzQTN1RXVLWUdiTUljRkNoZGpRS083U0t5ZWFpZmFaRyIsIm1hYyI6IjgyYWQ1MDhkMWExZDQzNWU5MDE3YjMxMzkwMTFmNDZmODNhMjQyY2RhODEwMzhhZTBlOTJmMmE5MTgzOTM2YmYifQ==";

    const accessToken = app.$cookies.get('token');
    if (accessToken) {
      config.headers.Authorization = "Bearer " + accessToken;
      config.headers.OriginPath   = app.context.route.fullPath
    }
    return config;
  })

  $axios.onResponse(response => {
    return response;
  })

  $axios.onError(error => {
    if (error.response === undefined) {
      alert('خطای اتصال به اینترنت ، لطفا دوباره تلاش کنید.')
      throw error
    }

    const code = parseInt(error.response && error.response.status)
    if (code === 400) {
      redirect('/400')
    }
    if(error.response.status === 500) {
      redirect('/sorry')
    }
    throw error
  })
}


