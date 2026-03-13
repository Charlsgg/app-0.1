<script lang="ts">
export default { layout: null }
</script>

<script setup lang="ts">
import { ref, shallowRef, onMounted } from 'vue'
import { Megaphone } from 'lucide-vue-next'
import { useTheme } from '../composable/usetheme.ts'
import AppSidebar from '../components/appsidebar.vue'
import AppNavbar from '../components/appnavbar.vue'

const props = defineProps<{
    user?: { name: string; email: string; user_type: string }
}>()

const { theme, styles, surface, isDark, setUserType, initTheme } = useTheme()

const isSidebarOpen = ref(false)
const announcements = ref<any[]>([])
const csrfToken = ref('')

onMounted(() => {
    initTheme()
    if (props.user?.user_type) {
        setUserType(props.user.user_type)
    }
    fetchAnnouncements()
    const tokenTag = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
    if (tokenTag) {
        csrfToken.value = tokenTag.content
    }
})

// Kept your fetch function in case you want to render dynamic posts above the static feed
const fetchAnnouncements = async () => {
    try {
        const response = await fetch('/api/announcements')
        if (response.ok) {
            const data = await response.json()
            announcements.value = data.map((post: any) => ({
                id: post.announcement_id,
                title: post.title,
                content: post.content,
                date: new Date(post.created_at).toLocaleDateString(undefined, {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                }),
                icon: shallowRef(Megaphone),
            }))
        }
    } catch (error) {
        console.error('Error fetching announcements:', error)
    }
}
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

            <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
                <div class="max-w-7xl mx-auto pb-12 flex gap-8">
                    
                    <section class="flex-1 min-w-0 space-y-6">
                        <div class="mb-6 md:mb-8 flex flex-col gap-2">
                            <h3
                                class="text-2xl md:text-3xl font-bold tracking-tight"
                                :style="styles.textPrimary"
                            >
                                General Announcements
                            </h3>
                            <p :style="styles.textSecondary" class="text-sm md:text-base max-w-2xl">
                                Stay up to date with the latest news, events, and important notices.
                            </p>
                        </div>

                        <div v-if="announcements.length > 0" class="flex flex-col gap-6 mb-6">
                            <article
                                v-for="post in announcements"
                                :key="post.id"
                                class="rounded-2xl shadow-sm border overflow-hidden p-6 transition-colors"
                                :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
                            >
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="size-12 rounded-full flex items-center justify-center bg-black/5 dark:bg-white/5" :style="{ color: surface.textPrimary }">
                                        <component :is="post.icon" :size="24" />
                                    </div>
                                    <div>
                                        <h3 class="font-bold" :style="styles.textPrimary">{{ post.title }}</h3>
                                        <p class="text-xs" :style="styles.textSecondary">{{ post.date }}</p>
                                    </div>
                                </div>
                                <div class="text-sm leading-relaxed whitespace-pre-wrap" :style="styles.textSecondary" v-html="post.content"></div>
                            </article>
                        </div>

                        <div class="space-y-6">
        
                        
                        </div>
                    </section>

                    <aside class="w-80 shrink-0 hidden xl:flex flex-col h-fit sticky top-4 space-y-6">
                        
                        <div 
                            class="rounded-2xl p-6 shadow-sm flex flex-col transition-colors border"
                            :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
                        >
                            <h3 class="font-bold mb-4" :style="styles.textPrimary">Upcoming Deadlines</h3>
                            <div class="space-y-4">
                                <div class="flex gap-3">
                                    <div class="size-10 rounded-lg flex flex-col items-center justify-center shrink-0" :style="{ backgroundColor: isDark ? 'rgba(239, 68, 68, 0.2)' : '#fef2f2', color: '#ef4444' }">
                                        <span class="text-xs font-bold uppercase leading-tight">Oct</span>
                                        <span class="text-sm font-bold leading-none">24</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold" :style="styles.textPrimary">CS101 Midterms</p>
                                        <p class="text-xs mt-0.5" :style="styles.textSecondary">9:00 AM • Room 402</p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="size-10 rounded-lg flex flex-col items-center justify-center shrink-0" :style="{ backgroundColor: isDark ? 'rgba(59, 130, 246, 0.2)' : '#eff6ff', color: '#3b82f6' }">
                                        <span class="text-xs font-bold uppercase leading-tight">Oct</span>
                                        <span class="text-sm font-bold leading-none">25</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold" :style="styles.textPrimary">System Analysis Proposal</p>
                                        <p class="text-xs mt-0.5" :style="styles.textSecondary">11:59 PM • Online</p>
                                    </div>
                                </div>
                            </div>
                            <button 
                                class="w-full mt-6 py-2 text-sm font-bold transition-colors border rounded-lg hover:opacity-80"
                                :style="{ borderColor: surface.borderSubtle, color: surface.textSecondary }"
                            >
                                View Full Calendar
                            </button>
                        </div>
                        
                        <div 
                            class="rounded-2xl p-6 shadow-sm flex flex-col transition-colors border"
                            :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
                        >
                            <p class="text-xs font-bold uppercase tracking-widest mb-4" :style="styles.textSecondary">
                                Announcement Filters
                            </p>
                            <div class="space-y-1">
                                <button class="w-full flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-colors hover:bg-black/5 dark:hover:bg-white/5" :style="{ color: surface.textPrimary }">
                                    <span class="flex items-center gap-2">
                                        <span class="size-2 rounded-full bg-blue-500"></span> Computer Science
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold" :style="{ backgroundColor: surface.inputBg, color: surface.textSecondary }">12</span>
                                </button>
                                <button class="w-full flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-colors hover:bg-black/5 dark:hover:bg-white/5" :style="{ color: surface.textPrimary }">
                                    <span class="flex items-center gap-2">
                                        <span class="size-2 rounded-full bg-emerald-500"></span> Information Technology
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold" :style="{ backgroundColor: surface.inputBg, color: surface.textSecondary }">8</span>
                                </button>
                                <button class="w-full flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-colors hover:bg-black/5 dark:hover:bg-white/5" :style="{ color: surface.textPrimary }">
                                    <span class="flex items-center gap-2">
                                        <span class="size-2 rounded-full bg-amber-500"></span> Information Systems
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold" :style="{ backgroundColor: surface.inputBg, color: surface.textSecondary }">5</span>
                                </button>
                                <button class="w-full flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-colors hover:bg-black/5 dark:hover:bg-white/5" :style="{ color: surface.textPrimary }">
                                    <span class="flex items-center gap-2">
                                        <span class="size-2 rounded-full bg-purple-500"></span> CCIS LSG
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold" :style="{ backgroundColor: surface.inputBg, color: surface.textSecondary }">20</span>
                                </button>
                            </div>
                        </div>
                    </aside>

                </div>
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