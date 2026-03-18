<script setup lang="ts">
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { useTheme } from '../composable/usetheme.ts'
import { computed, watch } from 'vue'

interface Event {
    id: number | string
    month: string
    day: string
    title: string
    time: string
}

const props = defineProps<{
    user?: { name: string; email: string; user_type: string },
    events: Event[]
}>()

// Destructure theme and setUserType to connect the prop data
const { styles, surface, theme, setUserType } = useTheme()

// Sync the global theme state with this component's user prop 
watch(
    () => props.user?.user_type,
    (newType) => {
        if (newType) {
            setUserType(newType)
        }
    },
    { immediate: true } // Run immediately on mount
)

// Simplify the link by utilizing the pre-mapped path from useTheme
const eventsLink = computed(() => theme.value.eventsPath)
</script>

<template>
    <div 
        class="rounded-xl p-6 shadow-sm flex flex-col transition-colors border"
        :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
    >
        <h3 class="font-bold mb-5 flex items-center gap-2 text-sm uppercase tracking-wider" :style="styles.textPrimary">
            <CalendarIcon :size="16" /> Upcoming Events
        </h3>
        
        <div class="space-y-5">
            <div v-if="events.length === 0" class="text-sm italic" :style="styles.textSecondary">
                No upcoming events.
            </div>

            <div v-for="event in events" :key="event.id" class="flex gap-4 items-center">
                <div 
                    class="size-11 rounded-lg flex flex-col items-center justify-center shrink-0 border" 
                    :style="styles.cardIcon"
                >
                    <span class="text-[10px] font-black uppercase leading-none">{{ event.month }}</span>
                    <span class="text-base font-bold">{{ event.day }}</span>
                </div>
                
                <div class="min-w-0">
                    <p class="text-sm font-bold truncate" :style="styles.textPrimary">{{ event.title }}</p>
                    <p class="text-[11px] font-medium" :style="styles.textSecondary">{{ event.time }}</p>
                </div>
            </div>
        </div>
        
        <a :href="eventsLink" 
          class="w-full mt-6 py-2.5 text-xs font-bold transition-all border rounded-lg hover:opacity-80 text-center block"
          :style="{ borderColor: surface.borderSubtle, color: surface.textSecondary, backgroundColor: surface.inputBg }"
        >
            View Full Calendar
        </a>
    </div>
</template>