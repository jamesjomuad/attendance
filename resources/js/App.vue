<template>
    <router-view></router-view>
</template>

<script setup>
import { useMeta } from 'quasar'


useMeta({
    title: 'Dashboard',
    titleTemplate: title => `${title} - Attendance`,
})
</script>


<style type="text/scss" lang="scss">
    // Material Icons
    @import url('https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons');
    // Animate css
    @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
</style>
