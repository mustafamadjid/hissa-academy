<script setup lang="ts">
import { computed } from "vue";
import { Pencil, Trash2, BookOpen, GraduationCap, Search } from "@lucide/vue";
import type { TableColumn } from "@nuxt/ui";
import type { CourseDto } from "../types/course.types";

const props = defineProps<{
  courses: readonly CourseDto[];
  isLoading: boolean;
  page: number;
  pageSize: number;
  total: number;
}>();

const tableData = computed(() => [...props.courses]);

const emit = defineEmits<{
  "update:page": [value: number];
  edit: [course: CourseDto];
  delete: [course: CourseDto];
}>();

const columns: TableColumn<CourseDto>[] = [
  { accessorKey: "name", header: "Informasi Course" },
  { accessorKey: "minimum_score", header: "Passing Grade" },
  { accessorKey: "status", header: "Status" },
  { id: "actions", header: "Aksi" },
];

// Helper untuk styling status
function getStatusConfig(status: string) {
  const configs: Record<
    string,
    {
      label: string;
      color: "success" | "warning" | "neutral";
      variant: "subtle" | "outline";
    }
  > = {
    active: { label: "Aktif", color: "success", variant: "subtle" },
    draft: { label: "Draft", color: "warning", variant: "subtle" },
    inactive: { label: "Nonaktif", color: "neutral", variant: "outline" },
  };
  return (
    configs[status] ?? { label: status, color: "neutral", variant: "subtle" }
  );
}

function visibleRange(page: number, pageSize: number, total: number): string {
  if (total === 0) return "0 course";
  const from = (page - 1) * pageSize + 1;
  const to = Math.min(page * pageSize, total);
  return `Menampilkan ${from}-${to} dari ${total} data`;
}
</script>

<template>
  <div class="w-full space-y-4">
    <!-- Main Table Container -->
    <div
      class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
      <UTable
        :data="tableData"
        :columns="columns"
        :loading="isLoading"
        :ui="{
          root: 'relative overflow-x-auto',
          base: 'min-w-full table-fixed',
          th: 'bg-slate-50/50 px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200',
          td: 'px-4 py-4 text-sm text-slate-600 border-b border-slate-100 align-middle',
          tr: 'hover:bg-slate-50/30 transition-colors group',
        }"
      >
        <!-- Custom Empty State -->
        <template #empty>
          <div
            class="flex flex-col items-center justify-center py-12 text-center"
          >
            <div class="mb-3 rounded-full bg-slate-50 p-3">
              <Search class="h-6 w-6 text-slate-400" />
            </div>
            <p class="text-sm font-medium text-slate-900">
              Tidak ada course ditemukan
            </p>
            <p class="text-xs text-slate-500">
              Coba sesuaikan pencarian atau tambah data baru.
            </p>
          </div>
        </template>

        <!-- Column -->
        <template #name-cell="{ row }">
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100"
            >
              <BookOpen class="h-5 w-5" />
            </div>
            <div class="flex flex-col min-w-0">
              <span
                class="font-semibold text-slate-900 truncate group-hover:text-emerald-700 transition-colors"
              >
                {{ row.original.name }}
              </span>
              <span class="text-xs text-slate-400 line-clamp-1">
                {{ row.original.description }}
              </span>
            </div>
          </div>
        </template>

        <!-- Column: Minimum Score -->
        <template #minimum_score-cell="{ row }">
          <div
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700"
          >
            <GraduationCap class="h-3.5 w-3.5 text-slate-500" />
            {{ row.original.minimum_score }}
          </div>
        </template>

        <!-- Column: Status dengan Dot -->
        <template #status-cell="{ row }">
          <UBadge
            :color="getStatusConfig(row.original.status).color"
            :variant="getStatusConfig(row.original.status).variant"
            size="sm"
            class="rounded-full px-2.5 py-0.5 font-medium capitalize"
          >
            <span
              class="mr-1.5 h-1.5 w-1.5 rounded-full"
              :class="{
                'bg-emerald-500': row.original.status === 'active',
                'bg-amber-500': row.original.status === 'draft',
                'bg-slate-400': row.original.status === 'inactive',
              }"
            />
            {{ getStatusConfig(row.original.status).label }}
          </UBadge>
        </template>

        <!-- Column: Aksi -->
        <template #actions-cell="{ row }">
          <div
            class="flex justify-end gap-1 opacity-100 group-hover:opacity-100 transition-opacity"
          >
            <UTooltip text="Edit Course">
              <UButton
                :icon="Pencil"
                color="neutral"
                variant="ghost"
                size="sm"
                class="hover:bg-emerald-50 hover:text-emerald-600 cursor-pointer"
                @click="emit('edit', row.original)"
              />
            </UTooltip>
            <UTooltip text="Hapus Course">
              <UButton
                :icon="Trash2"
                color="error"
                variant="ghost"
                size="sm"
                class="hover:bg-red-50 cursor-pointer"
                @click="emit('delete', row.original)"
              />
            </UTooltip>
          </div>
        </template>
      </UTable>

      <!-- Pagination Footer -->
      <div
        class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row"
      >
        <p class="text-xs font-medium text-slate-500">
          {{ visibleRange(page, pageSize, total) }}
        </p>

        <UPagination
          :page="page"
          :total="total"
          :items-per-page="pageSize"
          :disabled="isLoading"
          show-edges
          :ui="{
            root: 'flex items-center',
            list: 'flex items-center gap-1',
            label: 'text-xs font-semibold',
          }"
          @update:page="emit('update:page', $event)"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Transisi halus untuk loading state */
.u-table--loading {
  @apply opacity-50 pointer-events-none transition-opacity duration-300;
}
</style>
