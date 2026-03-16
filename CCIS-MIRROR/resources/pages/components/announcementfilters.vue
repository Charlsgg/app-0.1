<script setup lang="ts">
import { useTheme } from '../composable/usetheme.ts'

defineProps<{
    stats: { cs: number; it: number; is: number; lsg: number }
}>()

const { styles, surface } = useTheme()

// Define filters for cleaner template loop
const filters = [
    { key: 'cs', label: 'Computer Science', color: 'bg-blue-500' },
    { key: 'it', label: 'Information Technology', color: 'bg-emerald-500' },
    { key: 'is', label: 'Information Systems', color: 'bg-amber-500' },
    { key: 'lsg', label: 'CCIS LSG', color: 'bg-purple-500' },
]
</script>

<template>
    <div 
        class="rounded-xl p-6 shadow-sm flex flex-col transition-colors border"
        :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
    >
        <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-5" :style="styles.textSecondary">
            Filter by Topic
        </p>
        
        <div class="space-y-1">
            <button 
                v-for="filter in filters" 
                :key="filter.key"
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-all group hover:bg-black/5 dark:hover:bg-white/5"
                :style="{ color: surface.textPrimary }"
            >
                <span class="flex items-center gap-3">
                    <span class="size-2 rounded-full" :class="filter.color"></span>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">{{ filter.label }}</span>
                </span>
                
                <span 
                    class="px-2 py-0.5 rounded text-[10px] font-bold border" 
                    :style="{ 
                        backgroundColor: surface.inputBg, 
                        borderColor: surface.borderSubtle,
                        color: surface.textSecondary 
                    }"
                >
                    {{ stats[filter.key as keyof typeof stats] }}
                </span>
            </button>
        </div>
    </div>
</template>