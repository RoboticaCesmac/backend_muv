import Cookies from 'js-cookie'

const TOKEN_KEY = 'auth_token'

export const authService = {
  setToken(token) {
    // O token expira em 1 dia
    Cookies.set(TOKEN_KEY, token, { expires: 1 })
  },

  getToken() {
    return Cookies.get(TOKEN_KEY)
  },

  removeToken() {
    Cookies.remove(TOKEN_KEY)
  },

  isAuthenticated() {
    return !!this.getToken()
  }
} 