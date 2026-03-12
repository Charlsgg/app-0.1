<script setup lang="ts">
import { X, User, Home, Megaphone, LogOut, Calendar } from 'lucide-vue-next'

defineProps<{
    isOpen: boolean
    csrfToken: string
}>()

defineEmits(['close'])
</script>

<template>
    <div v-if="isOpen" @click="$emit('close')" class="absolute inset-0 bg-black/80 z-40 md:hidden backdrop-blur-sm transition-opacity"></div>

    <aside :class="['absolute inset-y-0 left-0 z-50 w-64 bg-[#221610] border-r border-[#ec5b13]/10 flex flex-col transition-transform duration-300 ease-in-out md:relative md:translate-x-0', isOpen ? 'translate-x-0 shadow-2xl shadow-black/50' : '-translate-x-full']">
        <div class="p-6 flex flex-col h-full">
            <button @click="$emit('close')" class="md:hidden absolute top-6 right-6 text-slate-500 hover:text-white transition-colors">
                <X :size="24" />
            </button>

            <div class="flex items-center gap-3 mb-8 mt-2 md:mt-0">
                <div class="h-10 w-10 shrink-0 rounded-full bg-[#ec5b13]/20 flex items-center justify-center border border-[#ec5b13]/30 shadow-lg shadow-[#ec5b13]/10">
                    <span class="text-[#ec5b13] font-black text-sm tracking-wider">ISS</span>
                </div>
                <h1 class="text-sm font-bold text-white uppercase tracking-wide leading-tight">Information System Society</h1>
            </div>

            <nav class="flex-1 flex flex-col gap-1 overflow-y-auto pr-2 -mr-2">
                <div class="mt-2">
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#ec5b13] text-white shadow-lg shadow-[#ec5b13]/20 mb-2">
                        <User :size="20" />
                        <span class="text-sm font-bold tracking-wide">Dashboard</span>
                    </div>
                    <div class="ml-5 mt-2 flex flex-col gap-1.5 border-l border-[#ec5b13]/20 pl-4 py-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-300 hover:text-white hover:bg-[#ec5b13]/10 rounded-lg text-sm font-medium transition-colors">
                            <Megaphone :size="18" /> Home
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-slate-300 hover:text-white hover:bg-[#ec5b13]/10 rounded-lg text-sm font-medium transition-colors">
                            <Calendar :size="18" /> Events
                        </a>
                    </div>
                </div>
            </nav>

            <div class="mt-auto pt-6 border-t border-[#ec5b13]/10 shrink-0">
                <form action="/logout" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 font-medium transition-all border border-transparent hover:border-red-500/20">
                        <LogOut :size="20" /> Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>
</template>