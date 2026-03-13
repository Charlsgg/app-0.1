import { createApp } from 'vue'
import Login from './components/Login.vue'
import Home from '../pages/body/announcement-page.vue'
import Events from '../pages/body/events-page.vue'
import Announcements from '../pages/body/announcement-page.vue'
import Profile from '../pages/body/profile-page.vue'
import PublicBoard from './components/PublicBoard.vue' 

const el = document.getElementById('app')

if (el) {
    const page = el.dataset.page
    const user = JSON.parse(el.dataset.user || '{}')

    if (page === 'announcement-board') {
        createApp(PublicBoard).mount('#app')
    } else if (page === 'home-page') {
        createApp(Home, { user }).mount('#app')
    } else if (page === 'events-page') {
        createApp(Events, { user }).mount('#app')
    } else if (page === 'announcement-page') {
        createApp(Announcements, { user }).mount('#app')
    } else if (page === 'profile-page') {
        createApp(Profile, { user }).mount('#app')
    } else {
        createApp(Login).mount('#app')
    }
}