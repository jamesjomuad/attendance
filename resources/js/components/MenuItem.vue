<template>
    <q-item clickable v-ripple :to="to">
        <q-item-section avatar>
            <q-icon :name="icon" />
        </q-item-section>
        <q-item-section>{{label}}</q-item-section>
    </q-item>
</template>

<script>
export default {
    name: "MenuItem",
    props: ['label','icon','to']
}
</script>
