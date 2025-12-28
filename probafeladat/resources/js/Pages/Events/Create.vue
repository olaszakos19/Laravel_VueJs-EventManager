<template>
  <div class="container">
    <h1>Űrlap</h1>

    <form @submit.prevent="onSubmit" novalidate>
      
      <label>
        Név
        <input type="text" v-model.trim="form.name" @blur="touched.name = true" />
      </label>
      <p v-if="showError('name')" class="error">{{ errors.name }}</p>

   
      <label>
        Dátum
        <input type="date" v-model="form.date" @blur="touched.date = true" />
      </label>
      <p v-if="showError('date')" class="error">{{ errors.date }}</p>

   
      <label>
        Leírás
        <textarea v-model="form.description" :maxlength="5000" @blur="touched.description = true"></textarea>
      </label>
      <div class="meta">
        <small>{{ form.description.length }} / 5000 karakter</small>
      </div>
      <p v-if="showError('description')" class="error">{{ errors.description }}</p>

    
      <label>
        Limit
        <input type="number" v-model.number="form.limit" min="1" @blur="touched.limit = true" />
      </label>
      <p v-if="showError('limit')" class="error">{{ errors.limit }}</p>

    
      <label>
        Kép (opcionális)
        <input type="file" @change="onFileChange" accept="image/*" />
      </label>
      <p v-if="showError('image')" class="error">{{ errors.image }}</p>



      <div class="actions">
        <button type="submit">Küldés</button>
        <button type="button" @click="resetForm">Visszaállít</button>
      </div>

      <div v-if="submitted" class="success">Űrlap sikeresen elküldve!</div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
const form = reactive({
  name: '',
  date: '',
  description: '',
  limit: 1,
  image: null
})

const touched = reactive({ name: false, date: false, description: false, limit: false, image: false })
const submitted = ref(false)
const imagePreview = ref(null)

function isFutureDate(value) {
  if (!value) return false

  const today = new Date()
  today.setHours(0,0,0,0)
  const parts = value.split('-').map(Number)
  const inputDate = new Date(parts[0], parts[1] - 1, parts[2])
  return inputDate.getTime() > today.getTime()
}

const errors = computed(() => {
  const e = {}

  // Név
  if (!form.name || form.name.trim().length === 0) e.name = 'A név megadása kötelező.'
  else if (form.name.trim().length < 5) e.name = 'A névnek legalább 5 karakternek kell lennie.'

  // Dátum
  if (!form.date) e.date = 'A dátum megadása kötelező.'
  else if (!isFutureDate(form.date)) e.date = 'A dátumnak a jövőben kell lennie.'

  // Leírás
  if (form.description && form.description.length > 5000) e.description = 'A leírás legfeljebb 5000 karakter lehet.'

  // Limit
  if (form.limit === null || form.limit === undefined || form.limit === '') e.limit = 'A limit megadása kötelező.'
  else if (Number(form.limit) < 1) e.limit = 'A limitnek legalább 1-nek kell lennie.'

  // Kép
  if (form.image) {
    const file = form.image
    if (!file.type.startsWith('image/')) e.image = 'Csak képfájl feltöltése engedélyezett.'
  }

  return e
})

function showError(field) {
  // show error when touched or after submit
  return (touched[field] || submitted.value) && errors.value[field]
}

function onFileChange(e) {
  touched.image = true
  const file = e.target.files && e.target.files[0]
  if (!file) {
    form.image = null
    imagePreview.value = null
    return
  }
  form.image = file
  if (file && file.type && file.type.startsWith('image/')) {
    imagePreview.value = URL.createObjectURL(file)
  } else {
    imagePreview.value = null
  }
}


function validate() {
  // force compute errors by touching all fields
  Object.keys(touched).forEach(k => (touched[k] = true))
  const hasErrors = Object.keys(errors.value).length > 0
  return !hasErrors
}

function onSubmit() {
    submitted.value = true
    if (!validate()) return

    const payload = new FormData()
    payload.append('name', form.name.trim())
    payload.append('starts_at', form.date)  
    payload.append('description', form.description)
    payload.append('limit', String(form.limit))
    if (form.image) payload.append('image', form.image)

   
    router.post('/events', payload, {
        onSuccess: () => {
            alert('Űrlap sikeresen elküldve!')
            resetForm()
        },
        onError: (errors) => {
            console.log('Backend validation errors:', errors)
        }
    })
}

function resetForm() {
  form.name = ''
  form.date = ''
  form.description = ''
  form.limit = 1
  form.image = null
  imagePreview.value = null
  Object.keys(touched).forEach(k => (touched[k] = false))
  submitted.value = false
  const input = document.querySelector('input[type=file]')
  if (input) input.value = ''
}
</script>

<style scoped>
.container { max-width:700px; margin:24px auto; padding:16px; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
label { display:block; margin:12px 0; }
input[type="text"], input[type="date"], input[type="number"], textarea { display:block; width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; }
textarea { min-height:120px; }
.error { color: #b00020; font-size:0.9rem; margin:6px 0 0; }
.meta { display:flex; justify-content:flex-end; font-size:0.8rem; color:#666; }
.actions { margin-top:16px; display:flex; gap:8px; }
button { padding:8px 12px; border-radius:6px; border:1px solid #333; background:#111; color:#fff; cursor:pointer; }
button[type="button"] { background:#666; }
.preview { margin-top:12px; }
.preview img { max-width:200px; max-height:200px; display:block; border-radius:6px; margin-top:8px; }
.success { margin-top:12px; color: #0b7a3a; }
</style>
