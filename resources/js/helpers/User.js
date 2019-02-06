class User {
  constructor(user) {
    this.user = user
  }

  role() {
    return this.user.role ? this.user.role.name : ''
  }

  isAdmin() {
    return this.role().toLowerCase() === 'admin'
  }
}

export default new User(window.app.user)
