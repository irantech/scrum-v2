export default function ({ redirect ,  app , context }) {
  if(!app.store.getters['auth/can']('show-customer-contracts'))
    return redirect('/admin')

}
