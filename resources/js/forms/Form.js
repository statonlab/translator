import Errors from './Errors'

export default class Form {
  constructor(data) {
    this.errors       = new Errors()
    this.originalData = data

    for (let field in data) {
      if (data.hasOwnProperty(field)) {
        this[field] = data[field]
      }
    }
  }

  keydown(event) {
    let name = event.target.name
    if (typeof name !== 'undefined' && typeof this[name] !== 'undefined') {
      this.errors.clear(name)
    }
  }

  /**
   * Get the data for the form.
   *
   * @param {{Object}} override
   */
  data(override) {
    let data = {}

    for (let field in this.originalData) {
      if (this.originalData.hasOwnProperty(field)) {
        data[field] = this[field]
      }
    }

    if (override) {
      for (let field in override) {
        if (this.originalData.hasOwnProperty(field)) {
          data[field] = override[field]
        }
      }
    }

    return data
  }

  reset() {
    for (let field in this.originalData) {
      if (this.originalData.hasOwnProperty(field)) {
        this[field] = ''
      }
    }
  }

  post(url, override) {
    return this.submit('post', url, override)
  }

  put(url, override) {
    return this.submit('put', url, override)
  }

  get(url, override) {
    return this.submit('get', url, override)
  }

  delete(url, override) {
    return this.submit('delete', url, override)
  }

  patch(url, override) {
    return this.submit('patch', url, override)
  }

  submit(requestType, url, override) {
    if (url.length === 0) {
      throw new Error('Please provide a valid URL')
    }
    return new Promise((resolve, reject) => {
      axios[requestType](url, this.data(override)).then(response => {
        resolve(response)
      }).catch(errors => {
        this.onFail(errors)

        reject(errors)
      })
    })
  }

  onFail(errors) {
    if (errors.response && errors.response.status === 422) {
      this.errors.set(errors.response.data)
    }
  }

  setDefault(data) {
    for (let field in this.originalData) {
      if (!this.originalData.hasOwnProperty(field)) {
        return
      }

      if (data[field] !== 'undefined') {
        this[field] = data[field]
      }
    }
  }
}
