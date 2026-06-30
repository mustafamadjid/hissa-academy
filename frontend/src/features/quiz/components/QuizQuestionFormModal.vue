<script setup lang="ts">
import { reactive, ref, watch } from 'vue'
import { Plus, Trash2 } from '@lucide/vue'

import type {
  AdminQuizQuestionDto,
  QuestionFormErrors,
  QuestionFormValues,
} from '../types/admin-quiz.types'
import { validateQuestionForm } from '../validation/question-form.validation'

const props = defineProps<{
  open: boolean
  question: AdminQuizQuestionDto | null
  isSubmitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  submit: [values: QuestionFormValues]
}>()

const values = reactive<QuestionFormValues>({
  question: '',
  answers: [],
})
const correctAnswerIndex = ref(0)
const errors = ref<QuestionFormErrors>({})

function resetForm(): void {
  values.question = props.question?.question ?? ''
  values.answers = props.question
    ? props.question.answers.map((item) => ({
        answer: item.answer,
        isCorrect: item.is_correct,
      }))
    : [
        { answer: '', isCorrect: true },
        { answer: '', isCorrect: false },
      ]
  const currentCorrectIndex = values.answers.findIndex((item) => item.isCorrect)
  correctAnswerIndex.value = currentCorrectIndex >= 0 ? currentCorrectIndex : 0
  applyCorrectAnswer()
  errors.value = {}
}

function applyCorrectAnswer(): void {
  values.answers.forEach((item, index) => {
    item.isCorrect = index === correctAnswerIndex.value
  })
}

function addAnswer(): void {
  values.answers.push({ answer: '', isCorrect: false })
}

function removeAnswer(index: number): void {
  if (values.answers.length === 1) return

  values.answers.splice(index, 1)
  if (correctAnswerIndex.value === index) correctAnswerIndex.value = 0
  if (correctAnswerIndex.value > index) correctAnswerIndex.value -= 1
  applyCorrectAnswer()
}

function submitForm(): void {
  applyCorrectAnswer()
  errors.value = validateQuestionForm(values)
  if (Object.keys(errors.value).length > 0) return

  emit('submit', {
    question: values.question,
    answers: values.answers.map((item) => ({ ...item })),
  })
}

watch(correctAnswerIndex, applyCorrectAnswer)
watch(
  () => [props.open, props.question] as const,
  ([open]) => {
    if (open) resetForm()
  },
)
</script>

<template>
  <UModal
    :open="open"
    :title="question ? 'Edit pertanyaan' : 'Tambah pertanyaan'"
    description="Isi pertanyaan, opsi jawaban, dan pilih satu kunci jawaban."
    :dismissible="!isSubmitting"
    :ui="{ content: 'sm:max-w-2xl', footer: 'justify-end' }"
    @update:open="emit('update:open', $event)"
  >
    <template #body>
      <form id="question-form" class="space-y-5" @submit.prevent="submitForm">
        <div
          v-if="errorMessage"
          role="alert"
          class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ errorMessage }}
        </div>

        <UFormField
          label="Pertanyaan"
          name="question"
          required
          :error="errors.question"
        >
          <UTextarea
            v-model="values.question"
            maxlength="255"
            :rows="3"
            placeholder="Tuliskan pertanyaan quiz"
            class="w-full"
            autofocus
          />
        </UFormField>

        <fieldset class="space-y-3">
          <div class="flex items-start justify-between gap-3">
            <div>
              <legend class="text-sm font-semibold text-slate-700">
                Opsi jawaban
              </legend>
              <p class="mt-1 text-xs text-slate-500">
                Pilih tombol radio untuk menetapkan kunci jawaban.
              </p>
            </div>
            <UButton
              type="button"
              color="neutral"
              variant="outline"
              size="sm"
              @click="addAnswer"
            >
              <Plus :size="15" aria-hidden="true" />
              Tambah opsi
            </UButton>
          </div>

          <p v-if="errors.answers" role="alert" class="text-xs text-red-600">
            {{ errors.answers }}
          </p>

          <div
            v-for="(item, index) in values.answers"
            :key="index"
            class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-start gap-3"
          >
            <input
              v-model="correctAnswerIndex"
              type="radio"
              name="correct_answer"
              :value="index"
              :aria-label="`Jadikan opsi ${index + 1} sebagai kunci jawaban`"
              class="mt-2 size-4 accent-emerald-700"
            />
            <UFormField
              :name="`answer_${index}`"
              :error="errors.answerItems?.[index]"
              class="min-w-0"
            >
              <UInput
                v-model="item.answer"
                maxlength="255"
                :placeholder="`Opsi jawaban ${index + 1}`"
                :aria-label="`Opsi jawaban ${index + 1}`"
                class="w-full"
              />
            </UFormField>
            <UButton
              type="button"
              color="error"
              variant="ghost"
              :disabled="values.answers.length === 1"
              :aria-label="`Hapus opsi ${index + 1}`"
              @click="removeAnswer(index)"
            >
              <Trash2 :size="16" aria-hidden="true" />
            </UButton>
          </div>
        </fieldset>
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
        form="question-form"
        type="submit"
        :label="question ? 'Simpan perubahan' : 'Tambah pertanyaan'"
        :loading="isSubmitting"
      />
    </template>
  </UModal>
</template>
