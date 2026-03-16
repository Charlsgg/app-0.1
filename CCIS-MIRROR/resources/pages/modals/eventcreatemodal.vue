<script setup lang="ts">
defineProps<{
    show: boolean
    theme: Record<string, any>
    surface: Record<string, any>
    styles: Record<string, any>
}>()

defineEmits<{
    (e: 'close'): void
}>()
</script>

<template>
    <Teleport to="body">
        <div 
            v-if="show" 
            class="fixed inset-0 backdrop-blur-sm z-100 flex items-center justify-center p-4 transition-opacity font-['Space_Grotesk']"
            :style="{ backgroundColor: surface.overlayBg }"
        >
            <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" :style="styles.cardBg">
                
                <div class="p-6 border-b flex justify-between items-center" :style="{ borderColor: surface.borderSubtle, backgroundColor: surface.hoverBg }">
                    <h3 class="text-xl font-bold" :style="styles.textPrimary">Post New Event</h3>
                    <button 
                        @click="$emit('close')" 
                        class="size-8 rounded-full flex items-center justify-center transition-colors" 
                        :style="styles.textSecondary"
                        @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.borderSubtle"
                        @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = 'transparent'"
                    >
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-6 space-y-4" :style="styles.textPrimary">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Event Title</label>
                        <input 
                            class="w-full rounded-lg font-display outline-none p-2 border" 
                            placeholder="e.g., yess Science Night" type="text"
                            :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                        />
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Month</label>
                            <select 
                                class="w-full rounded-lg font-display outline-none p-2 border appearance-none"
                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                            >
                                <option>January</option>
                                <option selected>October</option>
                            </select>
                        </div>
                        <div class="space-y-1 col-span-2">
                            <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Day Range</label>
                            <input 
                                class="w-full rounded-lg font-display outline-none p-2 border" 
                                placeholder="e.g., 3-10 or 15" type="text"
                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Year</label>
                            <input 
                                class="w-full rounded-lg font-display outline-none p-2 border" 
                                type="number" value="2023"
                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                            />
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Venue</label>
                            <input 
                                class="w-full rounded-lg font-display outline-none p-2 border" 
                                placeholder="Room or Hall" type="text"
                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                            />
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase" :style="{ color: theme.accent }">Brief Description</label>
                        <textarea 
                            class="w-full rounded-lg font-display outline-none p-3 border resize-none" 
                            placeholder="Describe the event..." rows="3"
                            :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                        ></textarea>
                    </div>
                </div>
                
                <div class="p-6 flex gap-3 justify-end border-t" :style="{ borderColor: surface.borderSubtle, backgroundColor: surface.hoverBg }">
                    <button 
                        @click="$emit('close')" 
                        class="px-6 py-2 rounded-lg font-semibold text-sm transition-colors border" 
                        :style="{ borderColor: surface.borderSubtle, color: surface.textPrimary }"
                        @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.borderSubtle"
                        @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = 'transparent'"
                    >
                        Cancel
                    </button>
                    <button 
                        class="px-6 py-2 rounded-lg font-semibold text-sm transition-opacity"
                        :style="styles.button"
                        @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.opacity = '0.9'"
                        @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.opacity = '1'"
                    >
                        Publish Event
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>