<script lang="ts">
export default { layout: null }
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useTheme } from '../composable/usetheme.ts'
import AppSidebar from '../components/appsidebar.vue'
import AppNavbar from '../components/appnavbar.vue'

const props = defineProps<{
    user?: { name: string; email: string; user_type: string }
}>()

const { theme, styles, surface, isDark, setUserType, initTheme } = useTheme()

const isSidebarOpen = ref(false)
const csrfToken = ref('')

// Form state
const fullName = ref('Dr. Sarah Chen')
const emailAddress = ref('s.chen@csuccis.edu.ph')

onMounted(() => {
    initTheme()
    if (props.user?.user_type) {
        setUserType(props.user.user_type)
    }
    
    // Grab CSRF token for the logout form in the sidebar
    const tokenTag = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
    if (tokenTag) {
        csrfToken.value = tokenTag.content
    }
})
</script>

<template>
    <div
        class="fixed inset-0 w-full h-full overflow-hidden font-['Space_Grotesk'] flex transition-colors duration-300"
        :style="{ ...styles.pageBg, color: surface.textPrimary }"
    >
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="absolute inset-0 z-40 md:hidden backdrop-blur-sm transition-opacity"
            :style="{ backgroundColor: surface.overlayBg }"
        ></div>

        <AppSidebar
            :is-open="isSidebarOpen"
            :csrf-token="csrfToken"
            @close="isSidebarOpen = false"
        />

        <main class="flex-1 flex flex-col h-full overflow-hidden min-w-0">
            <AppNavbar
                :user-name="user?.name"
                @toggle-sidebar="isSidebarOpen = true"
            />

            <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full no-scrollbar">
                <div class="max-w-7xl mx-auto pb-12">
                    
                    <div class="mb-10">
                        <h1 class="text-3xl lg:text-4xl font-black tracking-tight" :style="styles.textPrimary">Edit Profile</h1>
                        <p class="mt-2 text-lg" :style="styles.textSecondary">Update your professional information for the faculty directory.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <div class="lg:col-span-1">
                            <div class="rounded-2xl shadow-sm p-8 flex flex-col items-center text-center transition-colors" :style="styles.cardBg">
                                
                                <div class="relative mb-6">
                              <div class="h-40 w-40 rounded-full ring-4 overflow-hidden flex items-center justify-center" :style="{ backgroundColor: surface.inputBg, '--tw-ring-color': surface.borderSubtle }"
>
                                        <img class="h-full w-full object-contain" alt="Profile Avatar" src="https://ui-avatars.com/api/?name=Sarah+Chen&size=200&background=random"/>
                                    </div>
                                    <button 
                                        class="absolute bottom-2 right-2 p-2.5 rounded-full shadow-lg border-4 hover:scale-110 transition-transform flex items-center justify-center text-white"
                                        :style="{ backgroundColor: theme.accent, borderColor: surface.cardBg }"
                                    >
                                        <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                                    </button>
                                </div>
                                
                                <h2 class="text-2xl font-bold" :style="styles.textPrimary">{{ user?.name || fullName }}</h2>
                                <p class="text-sm uppercase font-bold mt-1" :style="{ color: theme.accent }">{{ theme.abbr }}</p>
                                
                                <div class="w-full border-t my-6" :style="{ borderColor: surface.borderSubtle }"></div>
                                
                                <button 
                                    class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all border"
                                    :style="{ backgroundColor: surface.inputBg, borderColor: surface.borderSubtle, color: surface.textPrimary }"
                                    @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.borderColor = theme.accent"
                                    @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.borderColor = surface.borderSubtle"
                                >
                                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                                    Change Photo
                                </button>
                            </div>
                        </div>

                        <div class="lg:col-span-2 flex flex-col gap-8">
                            
                            <div class="rounded-2xl shadow-sm p-8 transition-colors" :style="styles.cardBg">
                                
                                <h3 class="text-lg font-bold mb-6 flex items-center gap-2" :style="styles.textPrimary">
                                    <span class="material-symbols-outlined" :style="{ color: theme.accent }">person</span>
                                    Personal Information
                                </h3>
                                
                                <form @submit.prevent class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold" :style="styles.textSecondary">Full Name</label>
                                            <input 
                                                v-model="fullName"
                                                class="w-full px-4 py-3.5 rounded-xl border focus:ring-2 focus:outline-none transition-all" 
                                                type="text" 
                                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                                                @focus="(e: FocusEvent) => (e.currentTarget as HTMLElement).style.borderColor = theme.accent"
                                                @blur="(e: FocusEvent) => (e.currentTarget as HTMLElement).style.borderColor = surface.inputBorder"
                                            />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold" :style="styles.textSecondary">Email Address</label>
                                            <input 
                                                v-model="emailAddress"
                                                class="w-full px-4 py-3.5 rounded-xl border focus:ring-2 focus:outline-none transition-all" 
                                                type="email" 
                                                :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder, color: surface.textPrimary }"
                                                @focus="(e: FocusEvent) => (e.currentTarget as HTMLElement).style.borderColor = theme.accent"
                                                @blur="(e: FocusEvent) => (e.currentTarget as HTMLElement).style.borderColor = surface.inputBorder"
                                            />
                                        </div>
                                    </div>

                                    <div class="pt-8 border-t mt-8" :style="{ borderColor: surface.borderSubtle }">
                                        <h3 class="text-lg font-bold mb-6 flex items-center gap-2" :style="styles.textPrimary">
                                            <span class="material-symbols-outlined" :style="{ color: theme.accent }">security</span>
                                            Security & Access
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="flex flex-col gap-2">
                                                <label class="text-sm font-bold" :style="styles.textSecondary">Password</label>
                                                <button 
                                                    class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl border transition-all text-left" 
                                                    type="button"
                                                    :style="{ backgroundColor: surface.inputBg, borderColor: surface.inputBorder }"
                                                    @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.hoverBg"
                                                    @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.inputBg"
                                                >
                                                    <span :style="styles.textMuted">••••••••••••</span>
                                                    <span class="text-xs font-bold" :style="{ color: theme.accent }">Update</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-8 flex justify-end gap-4">
                                        <button 
                                            class="px-8 py-3.5 font-bold rounded-xl transition-all" 
                                            type="button"
                                            :style="styles.textSecondary"
                                            @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = surface.hoverBg"
                                            @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.backgroundColor = 'transparent'"
                                        >
                                            Cancel
                                        </button>
                                        <button 
                                            class="py-3.5 px-10 rounded-xl font-bold shadow-lg transition-all active:scale-[0.98]" 
                                            type="submit"
                                            :style="styles.button"
                                            @mouseenter="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.opacity = '0.9'"
                                            @mouseleave="(e: MouseEvent) => (e.currentTarget as HTMLElement).style.opacity = '1'"
                                        >
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="p-6 rounded-2xl border flex items-start gap-4 transition-colors" :style="{ backgroundColor: theme.accent + '10', borderColor: theme.accent + '20' }">
                                <span class="material-symbols-outlined mt-0.5" :style="{ color: theme.accent }">info</span>
                                <div>
                                    <h4 class="text-sm font-bold" :style="styles.textPrimary">Need to update more info?</h4>
                                    <p class="text-sm mt-1 leading-relaxed" :style="styles.textSecondary">
                                        For changes to Department, Rank, or Employee ID, please contact the administrative office directly.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>