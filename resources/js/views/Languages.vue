<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <form @submit="$event.preventDefault()">
                            <input type="search"
                                   v-model="search"
                                   class="form-control shadow-none form-control-sm mr-1 w-full w-max-18 w-min-15"
                                   placeholder="Search">
                        </form>

                        <div class="ml-auto">
                            <button class="d-flex btn btn-primary btn-sm font-weight-bold"
                                    @click.prevent="showCreateModal = true">
                                <ion-icon name="add"></ion-icon>
                                <span class="d-inline-block ml-1">New Language</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-middle mb-0">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Language</th>
                                <th>Language Code</th>
                                <th>Maintainers</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="language in languages">
                                <td class="flag-td">
                                    <letter-icon v-if="!language.image"
                                                 :value="language.language_code"
                                                 :title="language.language"
                                                 format="language"/>
                                </td>
                                <td>
                                    <editable
                                            :value="language.language"
                                            @save="patch(language, 'language', $event)"
                                    >
                                        {{ language.language }}
                                    </editable>
                                </td>
                                <td>
                                    <editable
                                            :value="language.language_code"
                                            @save="patch(language, 'language_code', $event)">
                                        {{ language.language_code }}
                                    </editable>
                                </td>
                                <td>
                                    <div v-if="language.users.length > 0" class="stacked-icons d-inline-block">
                                        <letter-icon v-for="user in language.users.slice(0, 3)"
                                                     :value="user.name"
                                                     :key="user.id"/>
                                        <letter-icon v-if="moreCount(language)> 0"
                                                     :value="moreCount(language) + ' +'"
                                                     :title="moreCount(language) + ' More User(s)'"/>
                                    </div>
                                    <span v-if="language.users.length === 0">None</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm ml-auto"
                                                type="button"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                            <ion-icon name="reorder" class="d-inline-flex"></ion-icon>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center"
                                               href="#"
                                               @click.prevent="manageAssignees(language)">
                                                <span class="icon d-inline-flex mr-2">
                                                    <ion-icon name="contacts"></ion-icon>
                                                </span>
                                                <span>Manage Assignees</span>
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item d-flex align-items-center text-danger"
                                               href="#"
                                               @click.prevent="destroy(language)">
                                                <span class="icon d-inline-flex mr-2">
                                                    <ion-icon name="trash" class="d-inline-flex"></ion-icon>
                                                </span>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <modal v-if="showCreateModal" @close="showCreateModal = false">
            <language-form
                    @close="showCreateModal = false"
                    @create="languageCreated()"/>
        </modal>

        <modal v-if="showAssigneesModal"
               @close="showAssigneesModal = false">
            <language-assignees-form
                    @toggle="toggleAssignment($event)"
                    @close="showAssigneesModal = false"
                    :language="selectedLanguage"/>
        </modal>
    </div>
</template>

<script>
  import Modal from '../components/Modal'
  import LanguageForm from '../forms/LanguageForm'
  import LetterIcon from '../components/LetterIcon'
  import Editable from '../components/Editable'
  import LanguageAssigneesForm from '../forms/LanguageAssigneesForm'

  export default {
    name      : 'Languages',
    components: {LanguageAssigneesForm, Editable, LetterIcon, LanguageForm, Modal},

    watch: {
      search() {
        this.loadLanguages()
      }
    },

    mounted() {
      this.loadLanguages()
    },

    data() {
      return {
        showCreateModal   : false,
        showAssigneesModal: false,
        selectedLanguage  : -1,
        languages         : [],
        search            : ''
      }
    },

    methods: {
      async loadLanguages() {
        try {
          const {data}   = await axios.get('/web/languages', {
            params: {
              search: this.search
            }
          })
          this.languages = data.data
        } catch (e) {
          console.error(e)
        }
      },

      moreCount(language) {
        return language.users.length - 3
      },

      manageAssignees(language) {
        this.selectedLanguage   = language
        this.showAssigneesModal = true
      },

      languageCreated() {
        this.loadLanguages()
        this.showCreateModal = false
      },

      async patch(language, field, event) {
        try {
          let data    = {}
          data[field] = event.value
          await axios.patch(`/web/language/${language.id}`, data)
          this.loadLanguages()
          event.done()
        } catch (e) {
          if (e.response && e.response.status === 422) {
            alert(e.response.message)
          } else {
            alert('Unable to update language')
          }
        }
      },

      async destroy(language) {
        if (!confirm(`Are you sure you want to delete ${language.language}`)) {
          return
        }

        try {
          await axios.delete(`/web/language/${language.id}`)
          this.loadLanguages()
        } catch (e) {
          if (e.response && e.response.status === 422) {
            alert(e.response.message)
          }

          console.error(e)
        }
      },

      toggleAssignment(event) {
        const {op, user} = event

        if (op === 'attach') {
          this.selectedLanguage.users.push(user)
        } else {
          this.selectedLanguage.users = this.selectedLanguage.users.filter(u => u.id !== user.id)
        }

        this.languages = this.languages.map(l => {
          if (l.id === this.selectedLanguage.id) {
            l = this.selectedLanguage
          }

          return l
        })
      }
    }
  }
</script>

<style scoped>
    .flag-td {
        width: 40px;
    }
</style>
