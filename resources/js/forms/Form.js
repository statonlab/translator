import Errors from './Errors'

export default class Form {
  constructor(data) {
    this.errors             = new Errors()
    this.originalData       = data
    this._multipart         = false
    this.fileFields         = []
    this.onProgressCallback = (e) => {
    }

    for (let field in data) {
      if (data.hasOwnProperty(field)) {
        this[field] = data[field]
      }
    }
  }

  /**
   * Set the form to response to multipart data.
   *
   * @param multipart
   */
  multipart(multipart) {
    this._multipart = typeof multipart !== 'undefined' ? multipart : true
  }

  /**
   * Clear error of modified element.
   *
   * @param event
   */
  keydown(event) {
    let name = event.target.name
    if (typeof name !== 'undefined' && typeof this[name] !== 'undefined') {
      this.errors.clear(name)
    }
  }

  onProgressChange(callback) {
    if (typeof callback !== 'function') {
      throw new Errors('The function onProgressChange only accepts a function as a parameters')
    }

    this.onProgressCallback = callback
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

    if (this._multipart) {
      let form = new FormData()

      for (let field in data) {
        if (data.hasOwnProperty(field)) {
          if (this.isFileField(field)) {
            if (FileList && data[field] instanceof FileList) {
              form.append(field, data[field][0])
            } else {
              form.append(field, data[field])
            }
          } else {
            form.append(field, data[field])
          }
        }
      }

      return form
    }

    return data
  }

  /**
   * check if the field is a file field.
   *
   * @private
   * @param field
   * @return {boolean}
   */
  isFileField(field) {
    return this.fileFields.indexOf(field) > -1
  }

  /**
   * Resent form data.
   */
  reset() {
    for (let field in this.originalData) {
      if (this.originalData.hasOwnProperty(field)) {
        this[field] = ''
      }
    }
  }

  /**
   * Send a POST request.
   *
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  post(url, override) {
    return this.submit('post', url, override)
  }

  /**
   * Send a PUT request.
   *
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  put(url, override) {
    return this.submit('put', url, override)
  }

  /**
   * Send a GET request.
   *
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  get(url, override) {
    return this.submit('get', url, override)
  }

  /**
   * Send a DELETE request.
   *
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  delete(url, override) {
    return this.submit('delete', url, override)
  }

  /**
   * Send a PATCH request.
   *
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  patch(url, override) {
    return this.submit('patch', url, override)
  }

  /**
   * Get headers object.
   *
   * @private
   */
  headers() {
    let data = {}

    if (this._multipart) {
      data['Content-Type'] = 'multipart/form-data'
    }

    return data
  }

  /**
   * Let the form know that we have a file field.
   *
   * @param field
   */
  setAsFile(field) {
    if (!this.isFileField(field)) {
      this.multipart(true)
      this.fileFields.push(field)
    }
  }

  /**
   * @private
   * @return {Object}
   */
  config() {
    return {
      onUploadProgress: this.onProgressCallback
    }
  }

  /**
   * @private
   * @param requestType
   * @param url
   * @param override
   * @return {Promise<any>}
   */
  submit(requestType, url, override) {
    if (url.length === 0) {
      throw new Error('Please provide a valid URL')
    }

    if (this._multipart && requestType !== 'post') {
      throw new Error(`Form is set to be multipart but request method is ${requestType}. Method should be changed to post.`)
    }

    const headers = this.headers()
    const config  = this.config()

    return new Promise((resolve, reject) => {
      axios[requestType](url, this.data(override), {...config, headers}).then(response => {
        resolve(response)
      }).catch(errors => {
        this.onFail(errors)

        reject(errors)
      })
    })
  }

  /**
   * Handle errors.
   * @param errors
   * @private
   */
  onFail(errors) {
    if (errors.response && errors.response.status === 422) {
      this.errors.set(errors.response.data)
    }
  }

  /**
   * Set default data.
   *
   * @param data
   */
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
