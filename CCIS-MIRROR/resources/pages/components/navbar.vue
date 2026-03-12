<script setup lang="ts">
import { Terminal, Bell, Menu } from 'lucide-vue-next'

defineProps<{
    user?: { name: string; email: string; user_type: string }
}>()

defineEmits(['toggle-sidebar'])
</script>

<template>
    <header class="shrink-0 flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-[#ec5b13]/10 bg-[#221610] z-30">
        <div class="flex items-center gap-3">
            <button @click="$emit('toggle-sidebar')" class="md:hidden text-slate-300 hover:text-white p-2 -ml-2 hover:bg-[#ec5b13]/20 rounded-lg transition-colors">
                <Menu :size="24" />
            </button>
            <div class="p-2 rounded-lg bg-[#ec5b13]/10 text-[#ec5b13] hidden sm:flex shadow-inner">
                <Terminal :size="20" />
            </div>
            <h2 class="text-lg md:text-xl font-bold tracking-tight text-white truncate">ISS Dashboard</h2>
        </div>
        
        <div class="flex items-center gap-3 md:gap-5">
            <button class="p-2 rounded-lg hover:bg-[#ec5b13]/10 transition-colors text-slate-400 hover:text-[#ec5b13] relative">
                <Bell :size="20" />
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#ec5b13]"></span>
            </button>
            <div class="h-8 w-8 md:h-9 md:w-9 shrink-0 rounded-full bg-[#ec5b13] flex items-center justify-center text-xs font-bold text-white shadow-md shadow-[#ec5b13]/20 border border-white/10">
                {{ user?.name?.charAt(0) || 'U' }}
            </div>
        </div>
    </header>
</template>