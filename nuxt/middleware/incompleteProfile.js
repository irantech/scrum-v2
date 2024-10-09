export default function ({ redirect , app}) {
  if(!app.store.state.auth.user.signature || !app.store.state.auth.user.avatar)
    return redirect('/admin/profile')
}
