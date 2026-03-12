<script lang="ts">
export default { layout: null }
</script>

<script setup lang="ts">
import { ref, shallowRef, computed, watch, onMounted } from 'vue'
import {
    Terminal, Bell, User, Home, Megaphone, LogOut,
    Send, Menu, X
} from 'lucide-vue-next'

// ─── Props ───────────────────────────────────────────────────────
const props = defineProps<{
    user?: { name: string; email: string; user_type: string }
}>()

// ─── Theme Configuration ────────────────────────────────────────
interface ThemeConfig {
    label: string
    abbr: string
    color: string
    bgPrimary: string
    bgSecondary: string
    dashTitle: string
    announcementHeading: string
    announcementSubheading: string
    dashboardPath: string
}

const themeMap: Record<string, ThemeConfig> = {
    it_instructor: {
        label: 'Information Technology Society',
        abbr: 'ITS',
        color: '#3b82f6',
        bgPrimary: '#0f172a',
        bgSecondary: '#1e293b',
        dashTitle: 'ITS Dashboard',
        announcementHeading: 'Information Technology Announcements',
        announcementSubheading: 'Stay updated with the latest IT academic updates.',
        dashboardPath: '/it/dashboard',
    },
    cs_instructor: {
        label: 'Computer Science Society',
        abbr: 'CSS',
        color: '#22c55e',
        bgPrimary: '#0a1f12',
        bgSecondary: '#112b1a',
        dashTitle: 'CSS Dashboard',
        announcementHeading: 'Computer Science Announcements',
        announcementSubheading: 'Stay updated with the latest CS academic updates.',
        dashboardPath: '/cs/dashboard',
    },
    is_instructor: {
        label: 'Information System Society',
        abbr: 'ISS',
        color: '#ec5b13',
        bgPrimary: '#2a1c15',
        bgSecondary: '#221610',
        dashTitle: 'ISS Dashboard',
        announcementHeading: 'Information System Announcements',
        announcementSubheading: 'Stay updated with the latest IS academic updates.',
        dashboardPath: '/is/dashboard',
    },
    lsg_officer: {
        label: 'Local Student Government',
        abbr: 'LSG',
        color: '#a855f7',
        bgPrimary: '#1a0f2e',
        bgSecondary: '#150c24',
        dashTitle: 'LSG Dashboard',
        announcementHeading: 'LSG Announcements',
        announcementSubheading: 'Stay updated with the latest student government updates.',
        dashboardPath: '/lsg/dashboard',
    },
}

const defaultTheme: ThemeConfig = themeMap['is_instructor']

const theme = computed<ThemeConfig>(() => {
    const userType = props.user?.user_type ?? ''
    return themeMap[userType] ?? defaultTheme
})

// ─── Computed Style Helpers ─────────────────────────────────────
const c = computed(() => theme.value.color)

const styles = computed(() => ({
    bgPrimary: { backgroundColor: theme.value.bgPrimary },
    bgSecondary: { backgroundColor: theme.value.bgSecondary },

    // Badge / Logo
    badge: {
        backgroundColor: c.value + '20',
        borderColor: c.value + '30',
        color: c.value,
    },
    badgeShadow: {
        backgroundColor: c.value + '20',
        borderColor: c.value + '30',
        color: c.value,
        boxShadow: `0 4px 12px ${c.value}15`,
    },

    // Sidebar active item
    sidebarActive: {
        backgroundColor: c.value,
        boxShadow: `0 4px 12px ${c.value}33`,
    },
    sidebarBorderLeft: {
        borderColor: c.value + '20',
    },

    // Borders
    borderSubtle: { borderColor: c.value + '10' },
    borderMedium: { borderColor: c.value + '30' },

    // Icon color
    iconColor: { color: c.value },
    iconBg: {
        backgroundColor: c.value + '10',
        color: c.value,
    },

    // Primary button
    button: {
        backgroundColor: c.value,
        boxShadow: `0 4px 12px ${c.value}33`,
    },

    // Notification dot
    dot: { backgroundColor: c.value },

    // Avatar
    avatar: {
        backgroundColor: c.value,
        boxShadow: `0 4px 8px ${c.value}33`,
    },

    // Card icon container
    cardIcon: {
        backgroundColor: theme.value.bgPrimary,
        borderColor: c.value + '20',
        color: c.value,
    },
}))

// ─── Sidebar State ──────────────────────────────────────────────
const isSidebarOpen = ref(false)

// ─── Composer State ─────────────────────────────────────────────
const newTitle = ref('')
const newContent = ref('')
const announcements = ref<any[]>([])
const csrfToken = ref('')
const isPosting = ref(false)

// ─── Lifecycle ──────────────────────────────────────────────────
onMounted(() => {
    fetchAnnouncements()
    const tokenTag = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
    if (tokenTag) {
        csrfToken.value = tokenTag.content
    }
})

// ─── API Methods ────────────────────────────────────────────────
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

const postAnnouncement = async () => {
    if (!newTitle.value.trim() || !newContent.value.trim()) {
        alert('Please enter both a title and a message.')
        return
    }

    isPosting.value = true

    try {
        const response = await fetch('/api/announcements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
            },
            body: JSON.stringify({
                title: newTitle.value,
                content: newContent.value,
                board_id: 1,
            }),
        })

        if (response.ok) {
            const savedPost = await response.json()
            announcements.value.unshift({
                id: savedPost.announcement_id,
                title: savedPost.title,
                content: savedPost.content,
                date: 'Just now',
                icon: shallowRef(Megaphone),
            })
            newTitle.value = ''
            newContent.value = ''
        } else {
            const errorData = await response.json()
            alert(errorData.message || 'Failed to save announcement.')
        }
    } catch (error) {
        console.error('Error posting announcement:', error)
        alert('A network error occurred.')
    } finally {
        isPosting.value = false
    }
}
</script>

<template>
    <div
        class="fixed inset-0 w-full h-full overflow-hidden font-['Space_Grotesk'] text-slate-100 flex"
        :style="styles.bgPrimary"
    >
        <!-- ── Mobile Overlay ─────────────────────────────────── -->
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="absolute inset-0 bg-black/80 z-40 md:hidden backdrop-blur-sm transition-opacity"
        ></div>

        <!-- ── Sidebar ────────────────────────────────────────── -->
        <aside
            :class="[
                'absolute inset-y-0 left-0 z-50 w-64 flex flex-col transition-transform duration-300 ease-in-out md:relative md:translate-x-0',
                isSidebarOpen ? 'translate-x-0 shadow-2xl shadow-black/50' : '-translate-x-full',
            ]"
            :style="{
                backgroundColor: theme.bgSecondary,
                borderRight: `1px solid ${c}10`,
            }"
        >
            <div class="p-6 flex flex-col h-full">
                <!-- Close button (mobile) -->
                <button
                    @click="isSidebarOpen = false"
                    class="md:hidden absolute top-6 right-6 text-slate-500 hover:text-white transition-colors"
                >
                    <X :size="24" />
                </button>

                <!-- Logo / Brand -->
                <div class="flex items-center gap-3 mb-8 mt-2 md:mt-0">
                    <div
                        class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center border shadow-lg"
                        :style="styles.badgeShadow"
                    >
                        <span class="font-black text-sm tracking-wider">{{ theme.abbr }}</span>
                    </div>
                    <h1 class="text-sm font-bold text-white uppercase tracking-wide leading-tight">
                        {{ theme.label }}
                    </h1>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 flex flex-col gap-1 overflow-y-auto pr-2 -mr-2">
                    <div class="mt-2">
                        <div
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white shadow-lg mb-2"
                            :style="styles.sidebarActive"
                        >
                            <User :size="20" />
                            <span class="text-sm font-bold tracking-wide">PROFILE</span>
                        </div>

                        <div
                            class="ml-5 mt-2 flex flex-col gap-1.5 border-l pl-4 py-1"
                            :style="styles.sidebarBorderLeft"
                        >
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2 text-slate-300 hover:text-white rounded-lg text-sm font-medium transition-colors"
                                :style="{ '--hover-bg': c + '10' }"
                                @mouseenter="($event.target as HTMLElement).style.backgroundColor = c + '10'"
                                @mouseleave="($event.target as HTMLElement).style.backgroundColor = 'transparent'"
                            >
                                <Home :size="18" /> Home
                            </a>
                            <a
                                href="#"
                                class="flex items-center gap-3 px-3 py-2 text-slate-300 hover:text-white rounded-lg text-sm font-medium transition-colors"
                                @mouseenter="($event.target as HTMLElement).style.backgroundColor = c + '10'"
                                @mouseleave="($event.target as HTMLElement).style.backgroundColor = 'transparent'"
                            >
                                <Megaphone :size="18" /> Announcements
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Logout -->
                <div class="mt-auto pt-6 shrink-0" :style="{ borderTop: `1px solid ${c}10` }">
                    <form action="/logout" method="POST">
                        <input type="hidden" name="_token" :value="csrfToken" />
                        <button
                            type="submit"
                            class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 font-medium transition-all border border-transparent hover:border-red-500/20"
                        >
                            <LogOut :size="20" /> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ── Main Content ───────────────────────────────────── -->
        <main class="flex-1 flex flex-col h-full overflow-hidden min-w-0">
            <!-- Header -->
            <header
                class="shrink-0 flex items-center justify-between px-4 md:px-8 py-3 md:py-4 z-30"
                :style="{
                    backgroundColor: theme.bgSecondary,
                    borderBottom: `1px solid ${c}10`,
                }"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="isSidebarOpen = true"
                        class="md:hidden text-slate-300 hover:text-white p-2 -ml-2 rounded-lg transition-colors"
                        @mouseenter="($event.target as HTMLElement).style.backgroundColor = c + '20'"
                        @mouseleave="($event.target as HTMLElement).style.backgroundColor = 'transparent'"
                    >
                        <Menu :size="24" />
                    </button>
                    <div
                        class="p-2 rounded-lg hidden sm:flex shadow-inner"
                        :style="styles.iconBg"
                    >
                        <Terminal :size="20" />
                    </div>
                    <h2 class="text-lg md:text-xl font-bold tracking-tight text-white truncate">
                        {{ theme.dashTitle }}
                    </h2>
                </div>

                <div class="flex items-center gap-3 md:gap-5">
                    <button
                        class="p-2 rounded-lg transition-colors text-slate-400 relative"
                        @mouseenter="($event.target as HTMLElement).style.color = c"
                        @mouseleave="($event.target as HTMLElement).style.color = ''"
                    >
                        <Bell :size="20" />
                        <span
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full"
                            :style="styles.dot"
                        ></span>
                    </button>
                    <div
                        class="h-8 w-8 md:h-9 md:w-9 shrink-0 rounded-full flex items-center justify-center text-xs font-bold text-white border border-white/10"
                        :style="styles.avatar"
                    >
                        {{ user?.name?.charAt(0) || 'U' }}
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
                <div class="max-w-250 mx-auto pb-12">
                    <!-- Page Heading -->
                    <div class="mb-6 md:mb-8 flex flex-col gap-2">
                        <h3 class="text-2xl md:text-3xl font-bold tracking-tight text-white">
                            {{ theme.announcementHeading }}
                        </h3>
                        <p class="text-slate-400 text-sm md:text-base max-w-2xl">
                            {{ theme.announcementSubheading }}
                        </p>
                    </div>

                    <!-- Composer Card -->
                    <div
                        class="mb-10 rounded-2xl shadow-lg shadow-black/20 overflow-hidden flex flex-col transition-colors p-4 md:p-6"
                        :style="{
                            backgroundColor: theme.bgSecondary,
                            border: `1px solid ${c}30`,
                        }"
                    >
                        <input
                            v-model="newTitle"
                            type="text"
                            placeholder="Announcement Title"
                            class="w-full bg-transparent border-none focus:ring-0 text-white text-xl md:text-2xl font-bold placeholder-slate-600 px-0 pb-4 outline-none mb-4"
                            :style="{ borderBottom: `1px solid ${c}10` }"
                        />

                        <textarea
                            v-model="newContent"
                            placeholder="What's the latest update?"
                            class="w-full min-h-32 bg-transparent border-none focus:ring-0 text-white text-base placeholder-slate-600 outline-none resize-none"
                        ></textarea>

                        <div
                            class="mt-6 flex flex-col sm:flex-row items-end justify-end gap-4 pt-4"
                            :style="{ borderTop: `1px solid ${c}10` }"
                        >
                            <button
                                @click="postAnnouncement"
                                :disabled="isPosting"
                                class="w-full sm:w-auto px-8 py-2.5 text-white text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="styles.button"
                            >
                                <span v-if="isPosting">Posting...</span>
                                <span v-else>Post Announcement</span>
                                <Send :size="16" />
                            </button>
                        </div>
                    </div>

                    <!-- Announcements List -->
                    <div class="flex flex-col gap-6">
                        <div
                            v-if="announcements.length === 0"
                            class="text-center py-12 text-slate-500 italic"
                        >
                            No announcements posted yet.
                        </div>

                        <div
                            v-for="post in announcements"
                            :key="post.id"
                            class="p-5 md:p-6 rounded-2xl transition-all flex flex-col sm:flex-row gap-5 md:gap-6 group"
                            :style="{
                                backgroundColor: theme.bgSecondary,
                                border: `1px solid ${c}10`,
                            }"
                            @mouseenter="($event.currentTarget as HTMLElement).style.borderColor = c + '30'"
                            @mouseleave="($event.currentTarget as HTMLElement).style.borderColor = c + '10'"
                        >
                            <!-- Card Icon -->
                            <div
                                class="h-16 w-16 md:h-20 md:w-20 rounded-xl border shrink-0 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner"
                                :style="styles.cardIcon"
                            >
                                <component :is="post.icon" :size="32" />
                            </div>

                            <!-- Card Content -->
                            <div class="flex-1 min-w-0">
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-1 pb-2"
                                    :style="{ borderBottom: `1px solid ${c}10` }"
                                >
                                    <h5 class="text-base md:text-xl font-bold text-white truncate">
                                        {{ post.title }}
                                    </h5>
                                    <span class="text-xs font-medium text-slate-400 shrink-0">
                                        {{ post.date }}
                                    </span>
                                </div>

                                <div
                                    class="text-slate-300 text-sm mb-4 leading-relaxed whitespace-pre-wrap"
                                    v-html="post.content"
                                ></div>
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

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.whitespace-pre-wrap {
    white-space: pre-wrap;
}
</style>