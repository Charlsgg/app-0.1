<script setup lang="ts">
import { computed, ref, watch } from 'vue'

interface CalendarEvent {
    title: string
    venue: string
    description: string
    start_time: string
    end_time?: string // Added end_time
}

const props = defineProps<{
    show: boolean
    theme: Record<string, any>
    surface: Record<string, any>
    styles: Record<string, any>
    events: CalendarEvent[] 
}>()

const emit = defineEmits<{
    (e: 'close'): void
}>()

const selectedIndex = ref(0)

watch(() => props.show, (newVal) => {
    if (newVal) {
        selectedIndex.value = 0
    }
})

const activeEvent = computed(() => {
    if (!props.events || props.events.length === 0) return null
    return props.events[selectedIndex.value]
})

const formattedDate = computed(() => {
    if (!activeEvent.value?.start_time) return 'Event Details'
    const date = new Date(activeEvent.value.start_time)
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }).toUpperCase()
})

// Helper to format start and end times cleanly
const formatTimeRange = (start?: string, end?: string) => {
    if (!start) return 'TBA'
    
    const startDate = new Date(start)
    const startTimeStr = startDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })

    if (!end) return startTimeStr

    const endDate = new Date(end)
    const endTimeStr = endDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })

    // If the event ends on a different day, append the end date so it's clear
    if (startDate.toDateString() !== endDate.toDateString()) {
        const endDateStr = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        return `${startTimeStr} - ${endDateStr}, ${endTimeStr}`
    }

    return `${startTimeStr} - ${endTimeStr}`
}
</script>

<template>
    <Teleport to="body">
        <div 
            v-if="show" 
            class="fixed inset-0 backdrop-blur-sm z-[101] flex items-center justify-center p-4 transition-opacity"
            :style="{ backgroundColor: surface.overlayBg }"
            @click.self="$emit('close')"
        >
            <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-2xl shadow-2xl overflow-hidden min-h-[500px] max-h-[90vh]" :style="styles.cardBg">
                
                <div 
                    v-if="events && events.length > 1"
                    class="w-full md:w-[320px] shrink-0 border-b md:border-b-0 md:border-r flex flex-col"
                    :style="{ borderColor: surface.borderSubtle, backgroundColor: surface.cardBg }"
                >
                    <div class="p-6 border-b" :style="{ borderColor: surface.borderSubtle }">
                        <h4 class="font-bold text-[11px] uppercase tracking-wider opacity-60" :style="styles.textPrimary">
                            {{ events.length }} Events Scheduled
                        </h4>
                    </div>
                    
                    <div class="p-4 overflow-y-auto space-y-3 scrollbar-hide flex-1">
                        <button
                            v-for="(event, index) in events"
                            :key="index"
                            @click="selectedIndex = index"
                            class="w-full text-left p-4 rounded-xl transition-all border"
                            :style="{ 
                                backgroundColor: selectedIndex === index ? surface.cardBg : surface.hoverBg,
                                borderColor: selectedIndex === index ? theme.accent : 'transparent',
                            }"
                        >
                            <div 
                                class="font-bold text-[15px] leading-tight line-clamp-2 mb-1.5 transition-colors" 
                                :style="{ color: selectedIndex === index ? styles.textPrimary.color : styles.textSecondary.color }"
                            >
                                {{ event.title }}
                            </div>
                            <div 
                                class="text-[13px] flex items-center gap-1.5 transition-colors" 
                                :style="{ color: selectedIndex === index ? theme.accent : styles.textMuted.color }"
                            >
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                {{ formatTimeRange(event.start_time, event.end_time) }}
                            </div>
                        </button>
                    </div>
                </div>

                <div class="w-full md:flex-1 relative flex flex-col overflow-hidden" :style="{ backgroundColor: surface.cardBg }">
                    
                    <button 
                        @click="$emit('close')" 
                        class="absolute top-5 right-5 p-1 transition-opacity z-[110] hover:opacity-60"
                        :style="styles.textPrimary"
                    >
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>

                    <div class="w-full h-full overflow-y-auto scrollbar-hide flex flex-col">
                        
                        <div class="relative h-16 flex items-center justify-center shrink-0" :style="{ backgroundColor: theme.accent + '08' }">
                            <span class="material-symbols-outlined text-4xl opacity-[0.05]" :style="{ color: theme.accent }">calendar_today</span>
                        </div>
                        
                        <div class="p-8 md:px-10 flex-1 bg-transparent">
                            
                            <div class="-mt-10 mb-4 relative z-10">
                                <span 
                                    class="inline-flex items-center px-3 py-1 text-[11px] font-bold rounded-md tracking-wider backdrop-blur-md" 
                                    :style="{ backgroundColor: theme.accent + '15', color: theme.accent }"
                                >
                                    {{ formattedDate }}
                                </span>
                            </div>

                            <h3 class="text-[32px] font-extrabold mb-10 leading-tight" :style="styles.textPrimary">
                                {{ activeEvent?.title || 'No Title Selected' }}
                            </h3>
                            
                            <div class="space-y-8">
                                <div class="flex items-start gap-5">
                                    <div class="size-11 rounded-full flex items-center justify-center shrink-0" :style="{ backgroundColor: theme.accent + '10' }">
                                        <span class="material-symbols-outlined text-[22px]" :style="{ color: theme.accent }">schedule</span>
                                    </div>
                                    <div class="pt-0.5">
                                        <p class="text-[11px] font-bold uppercase tracking-widest mb-1 opacity-50" :style="styles.textPrimary">Time</p>
                                        <p class="text-[15px] font-medium" :style="styles.textPrimary">
                                            {{ formatTimeRange(activeEvent?.start_time, activeEvent?.end_time) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-5">
                                    <div class="size-11 rounded-full flex items-center justify-center shrink-0" :style="{ backgroundColor: theme.accent + '10' }">
                                        <span class="material-symbols-outlined text-[22px]" :style="{ color: theme.accent }">location_on</span>
                                    </div>
                                    <div class="pt-0.5">
                                        <p class="text-[11px] font-bold uppercase tracking-widest mb-1 opacity-50" :style="styles.textPrimary">Venue</p>
                                        <p class="text-[15px] font-medium" :style="styles.textPrimary">{{ activeEvent?.venue || 'TBA' }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start gap-5">
                                    <div class="size-11 rounded-full flex items-center justify-center shrink-0" :style="{ backgroundColor: theme.accent + '10' }">
                                        <span class="material-symbols-outlined text-[22px]" :style="{ color: theme.accent }">description</span>
                                    </div>
                                    <div class="pt-0.5 w-full">
                                        <p class="text-[11px] font-bold uppercase tracking-widest mb-1 opacity-50" :style="styles.textPrimary">Details</p>
                                        <p class="text-[15px] leading-relaxed opacity-90" :style="styles.textPrimary">
                                            {{ activeEvent?.description || 'No additional details provided.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none; 
    scrollbar-width: none; 
}
</style>