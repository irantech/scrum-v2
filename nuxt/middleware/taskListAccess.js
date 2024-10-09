export default function ({ redirect ,  app , context }) {
  if(!app.store.getters['auth/can']('manage-tasks'))
    return redirect('/admin')

}
