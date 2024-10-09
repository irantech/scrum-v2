export default function ({ redirect ,  app , context }) {
  if(!app.store.getters['auth/can']('show-permission'))
    return redirect('/admin')
}
