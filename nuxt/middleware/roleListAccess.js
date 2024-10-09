export default function ({ redirect ,  app , context }) {
  if(!app.store.getters['auth/can']('show-role'))
    return redirect('/admin')

}
