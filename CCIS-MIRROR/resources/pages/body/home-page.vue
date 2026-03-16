<script setup lang="ts">
import { ref, shallowRef, onMounted } from 'vue'
import { Megaphone } from 'lucide-vue-next'
import { useTheme } from '../composable/usetheme.ts'
import AppSidebar from '../components/appsidebar.vue'
import AppNavbar from '../components/appnavbar.vue'
import AnnouncementComposer from '../components/announcementcomposer.vue'
import RecentAnnouncements from '../components/recentannouncements.vue'

const props = defineProps<{
    user?: { name: string; email: string; user_type: string }
}>()

const { styles, surface, setUserType, initTheme } = useTheme()

// --- State Variables ---
const isSidebarOpen = ref(false)
const announcements = ref<any[]>([])
const csrfToken = ref('')

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
    try {
        // 1. CHANGE THIS TO OUR NEW ENDPOINT
        const response = await fetch('/api/my-announcements') 
        
        if (response.ok) {
            const data = await response.json()
            
            // 2. The controller sends { announcements: [...] }, so we map over data.announcements
            announcements.value = data.announcements.map((post: any) => ({
                id: post.id,
                title: post.title,
                content: post.content,
                topic: post.topic,
                likes_count: post.likes_count,
                attachments: post.attachments,
                
                // The controller already formats these, so we just assign them!
                author_name: post.author_name,
                author_avatar: post.author_avatar,
                date: post.date, 
                
                icon: shallowRef(Megaphone),
            }))
        }
    } catch (error) {
        console.error('Error fetching announcements:', error)
    }
}

// Handles the event emitted from AnnouncementComposer.vue
const handleNewAnnouncement = (savedPost: any) => {
    const index = announcements.value.findIndex(a => a.id === savedPost.announcement_id);

    const formattedPost = {
        id: savedPost.announcement_id,
        title: savedPost.title,
        content: savedPost.content,
        topic: savedPost.topic,
        likes_count: savedPost.likes_count || 0,
        author_name: props.user?.name || 'Unknown', // Fallback to current user
        author_avatar: null, // Update if you handle avatars locally
        attachments: savedPost.attachments || [],
        date: index !== -1 ? announcements.value[index].date : 'Just now',
        icon: shallowRef(Megaphone),
    };

    if (index !== -1) {
        // Update existing announcement in the list
        announcements.value[index] = formattedPost;
    } else {
        // Add new announcement to the top
        announcements.value.unshift(formattedPost);
    }
};
</script>
<template>
    <div
        class="fixed inset-0 w-full h-full overflow-hidden font-['Space_Grotesk'] flex transition-colors duration-300"
        :style="styles.pageBg"
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
                <div class="max-w-4xl mx-auto pb-12">
                    
                    <div class="mb-6 md:mb-8 flex flex-col gap-2">
                        <h3
                            class="text-2xl md:text-3xl font-bold tracking-tight"
                            :style="styles.textPrimary"
                        >
                            Your Announcements 
                        </h3>
                        <p :style="styles.textSecondary" class="text-sm md:text-base max-w-2xl">
                            Create and see your announcements here.
                        </p>
                    </div>

                    <AnnouncementComposer 
                        :csrf-token="csrfToken" 
                        @post-created="handleNewAnnouncement" 
                    />

                    <RecentAnnouncements :announcements="announcements" />

                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>