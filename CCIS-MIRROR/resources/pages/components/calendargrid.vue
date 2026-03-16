<script setup lang="ts">
export interface CalendarEvent {
    id: string | number
    title: string
    color?: string // Optional: overrides theme.accent (e.g., for specific blue/red events)
}

export interface CalendarDay {
    date: number
    isCurrentMonth: boolean
    events: CalendarEvent[]
    isHighlight?: boolean 
}

defineProps<{
    theme: Record<string, any>
    surface: Record<string, any>
    styles: Record<string, any>
    days: CalendarDay[] 
}>()

defineEmits<{
    (e: 'show-detail', day: CalendarDay): void
}>()

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
</script>

<template>
    <section 
        class="flex-1 w-full rounded-2xl overflow-hidden shadow-lg shadow-black/5" 
        :style="styles.cardBg"
    >
        <div class="grid grid-cols-7 gap-px" :style="{ backgroundColor: surface.borderSubtle }">
            
            <div 
                v-for="day in daysOfWeek" 
                :key="day" 
                class="p-4 text-center font-bold text-xs uppercase tracking-wider"
                :style="{ backgroundColor: surface.hoverBg, color: theme.accent }"
            >
                {{ day }}
            </div>

            <div 
                v-for="(day, index) in days" 
                :key="'day-' + index" 
                @click="$emit('show-detail', day)" 
                class="min-h-30 p-2 cursor-pointer transition-colors"
                :class="[
                    !day.isCurrentMonth ? 'opacity-40' : '',
                    day.isHighlight || day.events.length ? 'border-t-2' : ''
                ]"
                :style="{ 
                    backgroundColor: surface.cardBg, 
                    color: !day.isCurrentMonth ? surface.textMuted : 'inherit',
                    borderTopColor: (day.isHighlight || day.events.length) ? theme.accent : 'transparent'
                }"
                @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.hoverBg"
                @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.cardBg"
            >
                <span :class="{ 'font-bold': day.isHighlight || day.events.length }">
                    {{ day.date }}
                </span>
                
                <div 
                    v-for="event in day.events"
                    :key="event.id"
                    class="mt-2 p-1.5 text-[10px] rounded leading-tight font-semibold border-l-2 mb-1"
                    :style="event.color ? {
                        backgroundColor: event.color + '20', 
                        color: event.color, 
                        borderLeftColor: event.color 
                    } : { 
                        backgroundColor: theme.accent + '20', 
                        color: theme.accent, 
                        borderLeftColor: theme.accent 
                    }"
                >
                    {{ event.title }}
                </div>

            </div>
        </div>
    </section>
</template>