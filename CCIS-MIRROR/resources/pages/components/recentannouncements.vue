<script setup lang="ts">
import { useTheme } from '../composable/usetheme.ts'

interface Attachment {
    attachment_id: number | string
    file_type: string
    file_path: string
}

interface Announcement {
    id: number | string
    title: string
    content: string
    topic: string
    date: string
    likes_count: number
    attachments?: Attachment[]
    author_name?: string
    author_avatar?: string | null // Added for profile picture path
}

defineProps<{
    announcements: Announcement[]
}>()

const { theme, styles, surface } = useTheme()

// --- Helper Functions ---
const getFileUrl = (path: string) => `/storage/${path}`
const isImage = (mimeType: string) => mimeType && mimeType.startsWith('image/')
const getFileName = (path: string) => path.split('/').pop() || 'Download File'

// Default fallback avatar if the user hasn't uploaded one
const defaultAvatar = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/></svg>`
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold" :style="styles.textPrimary">Recent Announcements</h2>
            <button :style="{ color: theme.accent }" class="text-sm font-semibold hover:underline">View All</button>
        </div>

        <div
            v-if="announcements.length === 0"
            class="text-center py-12 italic"
            :style="styles.textSecondary"
        >
            No announcements posted yet.
        </div>

        <div
            v-for="post in announcements"
            :key="post.id"
            class="rounded-xl p-5 space-y-4 shadow-sm hover:shadow-md transition-shadow"
            :style="styles.cardBg"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img 
                        :alt="post.author_name || 'Author'" 
                        class="size-10 rounded-full object-cover border"
                        :style="{ borderColor: surface.borderSubtle }"
                        :src="post.author_avatar ? getFileUrl(post.author_avatar) : defaultAvatar"
                    />
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide" :style="styles.textPrimary">
                            {{ post.title }}
                        </p>
                        <p class="text-xs" :style="styles.textSecondary">
                            <span v-if="post.author_name" class="font-medium">{{ post.author_name }} &bull; </span>
                            Posted {{ post.date }}
                        </p>
                    </div>
                </div>
                <button class="transition-colors hover:opacity-80" :style="styles.textMuted">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </button>
            </div>

            <p 
                class="leading-relaxed whitespace-pre-wrap text-sm md:text-base"
                :style="styles.textPrimary"
                v-html="post.content"
            ></p>

            <div v-if="post.attachments && post.attachments.length > 0" class="space-y-3">
                <template v-for="attachment in post.attachments" :key="attachment.attachment_id">
                    
                    <div 
                        v-if="isImage(attachment.file_type)" 
                        class="h-48 w-full rounded-lg bg-cover bg-center overflow-hidden border"
                        :style="{ backgroundImage: `url('${getFileUrl(attachment.file_path)}')`, borderColor: surface.borderSubtle }"
                    ></div>

                    <div 
                        v-else 
                        class="rounded-lg p-3 flex items-center justify-between group hover:opacity-80 transition-opacity border"
                        :style="{ backgroundColor: surface.inputBg, borderColor: surface.borderSubtle }"
                    >
                        <div class="flex items-center gap-3 overflow-hidden">
                            <span :style="{ color: theme.accent }" class="material-symbols-outlined">picture_as_pdf</span>
                            <span class="text-sm font-medium truncate" :style="styles.textPrimary">
                                {{ getFileName(attachment.file_path) }}
                            </span>
                        </div>
                        <a 
                            :href="getFileUrl(attachment.file_path)" 
                            target="_blank" 
                            :style="{ color: theme.accent }"
                            class="opacity-80 hover:opacity-100 p-1 flex items-center justify-center"
                        >
                            <span class="material-symbols-outlined">download</span>
                        </a>
                    </div>
                </template>
            </div>

            <div class="pt-2 flex gap-4" :style="{ borderTop: `1px solid ${surface.borderSubtle}` }">
                <button class="flex items-center gap-1.5 text-xs hover:text-red-500 transition-colors" :style="styles.textSecondary">
                    <span class="material-symbols-outlined text-sm">favorite</span>
                    {{ post.likes_count }}
                </button>
            </div>
        </div>
    </div>
</template>