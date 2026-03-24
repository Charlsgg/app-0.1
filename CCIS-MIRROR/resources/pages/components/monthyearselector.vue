<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    theme: Record<string, any>
    surface: Record<string, any>
    month: number // 0-11 (January is 0)
    year: number
}>()

const emit = defineEmits<{
    (e: 'update:month', value: number): void
    (e: 'update:year', value: number): void
}>()

// Generate all 12 months dynamically
const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
].map((name, index) => ({ label: name, value: index }))

// Generate a dynamic range of years (e.g., 5 years back and 5 years forward)
const years = computed(() => {
    // You can also base this on the actual current year rather than the selected year
    const baseYear = new Date().getFullYear()
    return Array.from({ length: 11 }, (_, i) => baseYear - 5 + i)
})
</script>

<template>
    <div class="flex items-center gap-2 p-1 rounded-xl border"
        :style="{ backgroundColor: surface.hoverBg, borderColor: surface.borderMedium }">
        <select :value="month" @change="emit('update:month', Number(($event.target as HTMLSelectElement).value))"
            class="bg-transparent border-none outline-none focus:ring-0 text-sm font-bold cursor-pointer py-1.5 pl-3 pr-8 appearance-none"
            :style="{ color: theme.accent }">
            <option v-for="m in months" :key="m.value" :value="m.value">
                {{ m.label }}
            </option>
        </select>

        <div class="w-px h-4" :style="{ backgroundColor: surface.borderStrong }"></div>

        <select :value="year" @change="emit('update:year', Number(($event.target as HTMLSelectElement).value))"
            class="bg-transparent border-none outline-none focus:ring-0 text-sm font-bold cursor-pointer py-1.5 pl-3 pr-8 appearance-none"
            :style="{ color: theme.accent }">
            <option v-for="y in years" :key="y" :value="y">
                {{ y }}
            </option>
        </select>
    </div>
</template>