<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

import type {
  CreateLessonFormErrors,
  CreateLessonFormValues,
} from '../types/course.types'
import { validateCreateLessonForm } from '../validation/create-lesson-form.validation'

const props = defineProps<{
  open: boolean
  isSubmitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  submit: [values: CreateLessonFormValues]
}>()

const values = reactive<CreateLessonFormValues>({
  title: '',
  videoUrl: '',
  isRequired: true,
})
const errors = ref<CreateLessonFormErrors>({})

function resetForm(): void {
  values.title = ''
  values.videoUrl = ''
  values.isRequired = true
  errors.value = {}
}

function submitForm(): void {
  errors.value = validateCreateLessonForm(values)

  if (Object.keys(errors.value).length > 0) return

  emit('submit', { ...values })
}

watch(
  () => props.open,
  (open) => {
    if (open) resetForm()
  },
)
</script>

<template>
  <UModal
    :open="open"
    title="Tambah lesson"
    description="Tambahkan materi baru pada urutan paling akhir course."
    :dismissible="!isSubmitting"
    :ui="{ content: 'sm:max-w-xl', footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <form id="lesson-create-form" class="space-y-5" @submit.prevent="submitForm">
        <div
          v-if="errorMessage"
          role="alert"
          class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>

        <UFormField
          label="Judul lesson"
          name="title"
          required
          :error="errors.title"
        >
          <UInput
            v-model="values.title"
            maxlength="255"
            placeholder="Contoh: Pengenalan Akad Syariah"
            class="w-full"
            autofocus
          />
        </UFormField>

        <UFormField
          label="Link video YouTube"
          name="video_url"
          required
          :error="errors.videoUrl"
          help="URL akan dikonversi menjadi YouTube video ID sebelum dikirim."
        >
          <UInput
            v-model="values.videoUrl"
            type="url"
            inputmode="url"
            placeholder="https://www.youtube.com/watch?v=..."
            class="w-full"
          />
        </UFormField>

        <UCheckbox
          v-model="values.isRequired"
          label="Lesson wajib diselesaikan"
          description="Student harus menyelesaikan lesson ini untuk melanjutkan pembelajaran."
        />
      </form>
    </template>

    <template #footer>
      <UButton
        label="Batal"
        color="neutral"
        variant="outline"
        :disabled="isSubmitting"
        @click="emit('update:open', false)"
      />
      <UButton
        form="lesson-create-form"
        type="submit"
        label="Tambah lesson"
        :loading="isSubmitting"
      />
    </template>
  </UModal>
</template>
