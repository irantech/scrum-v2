export default function ({ redirect ,  app }) {
  if(!app.store.state.auth.user) {
    return redirect('/login')
  }
}
