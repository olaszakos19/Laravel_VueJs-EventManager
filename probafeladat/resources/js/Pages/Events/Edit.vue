<template>
  <div class="max-w-xl mx-auto py-10 px-4">

    <h1 class="text-2xl font-bold mb-6">Esemény módosítása</h1>

    <form @submit.prevent="onSubmit" novalidate>
      <!-- Név -->
      <label class="block mb-2">Név</label>
      <input
        type="text"
        v-model="form.name"
        class="border p-2 w-full rounded mb-2"
      />

      <!-- Dátum -->
      <label class="block mb-2">Dátum</label>
      <input
        type="date"
        v-model="form.starts_at"
        class="border p-2 w-full rounded mb-2"
      />

      <!-- Leírás -->
      <label class="block mb-2">Leírás</label>
      <textarea
        v-model="form.description"
        class="border p-2 w-full rounded mb-2"
        maxlength="5000"
      ></textarea>

      <!-- Limit -->
      <label class="block mb-2">Limit</label>
      <input
        type="number"
        v-model.number="form.limit"
        min="1"
        class="border p-2 w-full rounded mb-2"
      />

      <!-- Kép -->
      <label class="block mb-2">Kép (opcionális)</label>
      <input type="file" @change="onFileChange" accept="image/*" class="mb-2" />

      <div v-if="imagePreview" class="mb-4">
        <p>Előnézet:</p>
        <img :src="imagePreview" class="max-w-full max-h-48 rounded" />
        <button type="button" @click="removeImage" class="mt-2 px-3 py-1 bg-gray-500 text-white rounded">
          Kép eltávolítása
        </button>
      </div>

      <div class="flex gap-2 mt-4">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Mentés</button>
        <button type="button" @click="cancel" class="px-4 py-2 bg-gray-500 text-white rounded">Mégse</button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const event = props.event  

const form = reactive({
  name: event.name,
  starts_at: event.starts_at.split('T')[0], // yyyy-mm-dd
  description: event.description,
  limit: event.limit,
  image: null
})

const imagePreview = ref(event.image_path ? `/storage/${event.image_path}` : null)

function onFileChange(e) {
  const file = e.target.files[0]
  form.image = file
  if (file && file.type.startsWith('image/')) {
    imagePreview.value = URL.createObjectURL(file)
  }
}



function onSubmit() {
  const payload = new FormData()
  payload.append('name', form.name)
  payload.append('starts_at', form.starts_at)
  payload.append('description', form.description)
  payload.append('limit', form.limit)
  if (form.image) payload.append('image', form.image)

  router.put(`/events/${event.id}`, payload, {
    onSuccess: () => {
      alert('Esemény sikeresen frissítve!')
     
    }
  })
}

function cancel() {
  router.get('/')
}
</script>
