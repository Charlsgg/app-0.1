<script setup lang="ts">
import { useTheme } from '../composable/usetheme.ts'

interface Attachment {
    id: number | string
    path: string   // Changed from file_path to match PHP
    type: string   // Changed from file_type to match PHP
}

interface Announcement {
    id: number | string
    title: string
    content: string
    topic: string
    date: string
    author_name: string
    author_avatar: string | null
    likes_count: number
    attachments: Attachment[]
}

defineProps<{
    announcements: Announcement[]
    isLoading: boolean
}>()

const { theme, styles, surface } = useTheme()

// --- Helpers ---
const getFileUrl = (path: string) => `/storage/${path}`

// Robust check for image type
const isImage = (type: string | null) => {
    if (!type) return false
    return type.startsWith('image/') || /\.(jpg|jpeg|png|webp|gif)$/i.test(type)
}

const getFileName = (path: string) => path.split('/').pop() || 'Download File'

const defaultAvatar = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/></svg>`
</script>

<template>
    <section class="flex-1 min-w-0 flex flex-col gap-6">
        <article
            v-for="post in announcements"
            :key="post.id"
            class="rounded-xl p-6 space-y-4 shadow-sm border transition-all"
            :style="{ backgroundColor: surface.cardBg, borderColor: surface.borderSubtle }"
        >
            <div class="flex items-center gap-3">
                <img 
                    class="size-10 rounded-full object-cover border"
                    :style="{ borderColor: surface.borderSubtle }"
                    :src="post.author_avatar ? getFileUrl(post.author_avatar) : defaultAvatar"
                />
                <div>
                    <h3 class="text-base font-bold leading-tight" :style="styles.textPrimary">{{ post.title }}</h3>
                    <p class="text-xs" :style="styles.textSecondary">{{ post.author_name }} • {{ post.date }}</p>
                </div>
            </div>

            <div class="text-sm leading-relaxed" :style="styles.textPrimary" v-html="post.content"></div>

            <div v-if="post.attachments.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                <template v-for="file in post.attachments" :key="file.id">
                    
                    <div 
                        v-if="isImage(file.type)" 
                        class="h-44 w-full rounded-lg bg-cover bg-center border"
                        :style="{ 
                            backgroundImage: `url('${getFileUrl(file.path)}')`, 
                            borderColor: surface.borderSubtle 
                        }"
                    ></div>

                    <div 
                        v-else 
                        class="rounded-lg p-3 flex items-center justify-between border"
                        :style="{ backgroundColor: surface.inputBg, borderColor: surface.borderSubtle }"
                    >
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span :style="{ color: theme.accent }" class="material-symbols-outlined text-sm">description</span>
                            <span class="text-xs font-medium truncate" :style="styles.textPrimary">
                                {{ getFileName(file.path) }}
                            </span>
                        </div>
                        <a :href="getFileUrl(file.path)" target="_blank" :style="{ color: theme.accent }">
                            <span class="material-symbols-outlined text-sm">download</span>
                        </a>
                    </div>
                </template>
            </div>
        </article>
    </section>
</template>