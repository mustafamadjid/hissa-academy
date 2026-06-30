<script setup lang="ts">
import { ref, watch } from 'vue'
import type { DeepReadonly } from 'vue'
import {
  ArrowDown,
  ArrowUp,
  CheckCircle2,
  CircleHelp,
  GripVertical,
  Pencil,
  Trash2,
} from '@lucide/vue'
import Draggable from 'vuedraggable'

import type { AdminQuizQuestionDto } from '../types/admin-quiz.types'

const props = defineProps<{
  questions: DeepReadonly<AdminQuizQuestionDto[]>
  disabled?: boolean
}>()

const emit = defineEmits<{
  edit: [question: AdminQuizQuestionDto]
  delete: [question: AdminQuizQuestionDto]
  reorder: [questions: AdminQuizQuestionDto[]]
}>()

const localQuestions = ref<AdminQuizQuestionDto[]>([])

interface DraggableEndEvent {
  oldIndex?: number
  newIndex?: number
}

function syncQuestions(): void {
  localQuestions.value = props.questions.map((question) => ({
    ...question,
    answers: question.answers.map((answer) => ({ ...answer })),
  }))
}

function emitReorder(): void {
  emit('reorder', localQuestions.value.map((question) => ({ ...question })))
}

function handleDragEnd(event: DraggableEndEvent): void {
  if (event.oldIndex !== event.newIndex) emitReorder()
}

function moveQuestion(index: number, offset: -1 | 1): void {
  const targetIndex = index + offset
  if (
    props.disabled ||
    targetIndex < 0 ||
    targetIndex >= localQuestions.value.length
  ) {
    return
  }

  const [question] = localQuestions.value.splice(index, 1)
  if (!question) return

  localQuestions.value.splice(targetIndex, 0, question)
  emitReorder()
}

watch(() => props.questions, syncQuestions, { immediate: true, deep: true })
</script>

<template>
  <section aria-labelledby="question-list-title">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h2 id="question-list-title" class="text-xl font-bold text-emerald-950">
          Daftar Pertanyaan
        </h2>
        <p class="mt-1 text-xs leading-5 text-slate-500">
          Tarik pegangan untuk mengubah urutan. Perubahan disimpan otomatis.
        </p>
      </div>
      <span class="text-xs font-semibold text-slate-500">
        {{ questions.length }} pertanyaan
      </span>
    </div>

    <div
      v-if="disabled && questions.length > 0"
      role="status"
      class="mb-3 text-xs font-medium text-emerald-700"
    >
      Menyimpan perubahan pertanyaan...
    </div>

    <Draggable
      v-if="localQuestions.length > 0"
      v-model="localQuestions"
      item-key="id"
      tag="ol"
      handle=".question-drag-handle"
      :disabled="disabled"
      :animation="180"
      ghost-class="opacity-40"
      class="space-y-3"
      @end="handleDragEnd"
    >
      <template #item="{ element: question, index }">
        <li class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
          <div class="flex items-start gap-3">
            <button
              type="button"
              class="question-drag-handle flex size-9 shrink-0 cursor-grab items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 active:cursor-grabbing disabled:cursor-not-allowed"
              :disabled="disabled"
              :aria-label="`Tarik untuk mengurutkan pertanyaan ${index + 1}`"
            >
              <GripVertical :size="18" aria-hidden="true" />
            </button>

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold leading-6 text-slate-800">
                    <span class="mr-1 text-slate-400">{{ index + 1 }}.</span>
                    {{ question.question }}
                  </p>
                  <p class="mt-1 text-xs text-slate-500">
                    {{ question.points }} poin · {{ question.answers.length }} opsi
                  </p>
                </div>

                <div class="flex gap-1">
                  <UButton
                    color="neutral"
                    variant="ghost"
                    size="sm"
                    :disabled="disabled || index === 0"
                    :aria-label="`Naikkan pertanyaan ${index + 1}`"
                    @click="moveQuestion(index, -1)"
                  >
                    <ArrowUp :size="15" aria-hidden="true" />
                  </UButton>
                  <UButton
                    color="neutral"
                    variant="ghost"
                    size="sm"
                    :disabled="disabled || index === localQuestions.length - 1"
                    :aria-label="`Turunkan pertanyaan ${index + 1}`"
                    @click="moveQuestion(index, 1)"
                  >
                    <ArrowDown :size="15" aria-hidden="true" />
                  </UButton>
                  <UButton
                    color="neutral"
                    variant="ghost"
                    size="sm"
                    :disabled="disabled"
                    @click="emit('edit', question)"
                  >
                    <Pencil :size="15" aria-hidden="true" />
                    Edit
                  </UButton>
                  <UButton
                    color="error"
                    variant="ghost"
                    size="sm"
                    :disabled="disabled"
                    @click="emit('delete', question)"
                  >
                    <Trash2 :size="15" aria-hidden="true" />
                    Hapus
                  </UButton>
                </div>
              </div>

              <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                <li
                  v-for="answer in question.answers"
                  :key="answer.id"
                  class="flex items-start gap-2 rounded-lg border px-3 py-2 text-xs leading-5"
                  :class="
                    answer.is_correct
                      ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                      : 'border-slate-200 bg-slate-50 text-slate-600'
                  "
                >
                  <CheckCircle2
                    v-if="answer.is_correct"
                    :size="15"
                    class="mt-0.5 shrink-0"
                    aria-hidden="true"
                  />
                  <span>{{ answer.answer }}</span>
                  <span v-if="answer.is_correct" class="sr-only">(Kunci jawaban)</span>
                </li>
              </ul>
            </div>
          </div>
        </li>
      </template>
    </Draggable>

    <div
      v-else
      class="flex flex-col items-center rounded-xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center"
    >
      <span class="mb-3 flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        <CircleHelp :size="21" aria-hidden="true" />
      </span>
      <p class="text-sm font-semibold text-slate-800">Belum ada pertanyaan</p>
      <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">
        Tambahkan pertanyaan beserta opsi dan kunci jawabannya.
      </p>
    </div>
  </section>
</template>
