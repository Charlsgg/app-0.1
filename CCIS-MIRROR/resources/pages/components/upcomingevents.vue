<script setup lang="ts">
defineProps<{
    theme: Record<string, any>
    surface: Record<string, any>
    styles: Record<string, any>
}>()

defineEmits<{
    (e: 'show-detail'): void
}>()

const events = [
    { dates: 'Oct 10-12', posted: 'Oct 1', title: 'Tech Summit 2023: Innovations in AI', loc: 'Grand Auditorium', desc: 'Three-day event featuring keynote speakers from industry leaders.' },
    { dates: 'Oct 15', posted: 'Sep 28', title: 'Career Orientation Seminar', loc: 'Seminar Hall B', desc: 'A guide for graduating students on how to navigate the job market.' },
    { dates: 'Oct 20', posted: 'Oct 2', title: 'Hackathon 2023 Kick-off', loc: 'IT Lab 4 & 5', desc: 'Join us for the opening ceremony of the annual 48-hour coding marathon.' }
]
</script>

<template>
    <aside 
        class="w-full xl:w-80 shrink-0 rounded-2xl overflow-hidden shadow-lg shadow-black/5 flex flex-col" 
        :style="styles.cardBg"
    >
        <div class="p-6 border-b" :style="{ borderColor: surface.borderSubtle, backgroundColor: surface.hoverBg }">
            <h3 class="text-lg font-bold" :style="styles.textPrimary">Upcoming Events</h3>
            <p class="text-xs" :style="styles.textMuted">Chronological feed of announcements</p>
        </div>
        
        <div class="p-4 space-y-4">
            <div 
                v-for="event in events"
                :key="event.title"
                class="p-4 rounded-xl transition-all cursor-pointer border"
                :style="{ backgroundColor: surface.inputBg, borderColor: surface.borderSubtle }"
                @click="$emit('show-detail')"
                @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.borderColor = surface.borderStrong"
                @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.borderColor = surface.borderSubtle"
            >
                <div class="flex justify-between items-start mb-2">
                    <span 
                        class="px-2 py-1 text-[10px] font-bold rounded uppercase"
                        :style="{ backgroundColor: theme.accent + '20', color: theme.accent }"
                    >
                        {{ event.dates }}
                    </span>
                    <span class="text-[10px]" :style="styles.textMuted">Posted: {{ event.posted }}</span>
                </div>
                <h4 class="font-bold text-sm mb-1 leading-snug" :style="styles.textPrimary">{{ event.title }}</h4>
                <div class="flex items-center gap-1 text-[11px] mb-2" :style="styles.textMuted">
                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                    {{ event.loc }}
                </div>
                <p class="text-xs line-clamp-2" :style="styles.textSecondary">{{ event.desc }}</p>
            </div>
        </div>
    </aside>
</template>