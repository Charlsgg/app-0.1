<script lang="ts">
export default { layout: null }
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Sidebar from './sidebar.vue'
import Navbar from './navbar.vue'

const props = defineProps<{
    user: { name: string; email: string; user_type: string }
}>()

const isSidebarOpen = ref(false)
const csrfToken = ref('')

onMounted(() => {
    const tokenTag = window.document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
    if (tokenTag) {
        csrfToken.value = tokenTag.content
    }
})
</script>

<template>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#2a1c15] font-['Space_Grotesk'] text-slate-100 flex">
        
        <Sidebar 
            :is-open="isSidebarOpen" 
            :csrf-token="csrfToken"
            @close="isSidebarOpen = false" 
        />

        <main class="flex-1 flex flex-col h-full overflow-hidden min-w-0">
            <Navbar 
                :user="user" 
                @toggle-sidebar="isSidebarOpen = true" 
            />
            
            <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
                <slot />
            </div>
        </main>

    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.whitespace-pre-wrap { white-space: pre-wrap; }
</style>