<script setup lang="ts">
import { ref, shallowRef, onMounted, computed } from 'vue' 
import { Megaphone } from 'lucide-vue-next'
import { useTheme } from '../composable/usetheme.ts'
import AppSidebar from '../components/appsidebar.vue'
import AppNavbar from '../components/appnavbar.vue'
import AnnouncementComposer from '../components/announcementcomposer.vue'
import RecentAnnouncements from '../components/recentannouncements.vue'

const props = defineProps<{
    user?: { name: string; email: string; user_type: string; profile?: { profile_picture: string } }
}>()

const { theme, styles, surface, setUserType, initTheme } = useTheme()

// --- State Variables ---
const isSidebarOpen = ref(false)
const announcements = ref<any[]>([])
const csrfToken = ref('')
const showAll = ref(false) 
const isLoading = ref(false) // Added loading state

// --- Logic for "Recent" vs "All" ---
const displayedAnnouncements = computed(() => {
    return showAll.value ? announcements.value : announcements.value.slice(0, 3)
})

// --- Lifecycle ---
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

// --- API Calls & Handlers ---
const fetchAnnouncements = async () => {
    isLoading.value = true
    try {
        const response = await fetch('/api/my-announcements')
        if (response.ok) {
            const data = await response.json()
            announcements.value = data.announcements.map((post: any) => ({
                ...post,
                icon: shallowRef(Megaphone),
            }))
        }
    } catch (error) {
        console.error('Error fetching announcements:', error)
    } finally {
        isLoading.value = false
    }
}

// Updated: Trigger auto-refresh after posting
const handleNewAnnouncement = async (savedPost: any) => {
    // Re-fetch from server to get accurate IDs, formatted dates, and profile images
    await fetchAnnouncements();
    
    // Automatically show the newest post if it was hidden by the "3 posts" limit
    if (announcements.value.length > 0) {
        showAll.value = false; 
    }
};
</script>

<template>
    <div class="fixed inset-0 w-full h-full overflow-hidden font-['Space_Grotesk'] flex transition-colors duration-300"
        :style="styles.pageBg">
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false"
            class="absolute inset-0 z-40 md:hidden backdrop-blur-sm transition-opacity"
            :style="{ backgroundColor: surface.overlayBg }"></div>

        <AppSidebar :is-open="isSidebarOpen" :csrf-token="csrfToken" @close="isSidebarOpen = false" />

        <main class="flex-1 flex flex-col h-full overflow-hidden min-w-0">
            <AppNavbar :user-name="user?.name" @toggle-sidebar="isSidebarOpen = true" />

            <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full custom-scrollbar">
                <div class="max-w-4xl mx-auto pb-12">

                    <div class="mb-6 md:mb-8 flex flex-col gap-2">
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl md:text-3xl font-bold tracking-tight" :style="styles.textPrimary">
                                Your Announcements
                            </h3>
                            <div v-if="isLoading" class="size-4 border-2 border-t-transparent animate-spin rounded-full" :style="{ borderColor: theme.accent }"></div>
                        </div>
                        <p :style="styles.textSecondary" class="text-sm md:text-base max-w-2xl">
                            Create and manage your posts here.
                        </p>
                    </div>

                    <AnnouncementComposer :csrf-token="csrfToken" @post-created="handleNewAnnouncement" />

                    <div class="mt-12 mb-4 flex items-center justify-between">
                        <h4 class="font-bold text-lg" :style="styles.textPrimary">
                            {{ showAll ? 'All My Announcements' : 'Recent Announcements' }}
                        </h4>

                        <button v-if="announcements.length > 3" @click="showAll = !showAll"
                            class="text-sm font-semibold hover:underline transition-all"
                            :style="{ color: theme.accent }">
                            {{ showAll ? 'Show Less' : `View All (${announcements.length})` }}
                        </button>
                    </div>

                    <RecentAnnouncements 
                        :announcements="displayedAnnouncements" 
                        @refresh="fetchAnnouncements"
                    />

                    <div v-if="announcements.length === 0 && !isLoading"
                        class="text-center py-20 border-2 border-dashed rounded-2xl mt-4"
                        :style="{ borderColor: surface.borderSubtle }">
                        <p :style="styles.textSecondary">You haven't posted anything yet.</p>
                    </div>

                </div>
            </div>
        </main>
    </div>
</template>