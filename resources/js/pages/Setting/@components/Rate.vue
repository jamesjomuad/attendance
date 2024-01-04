<template>
    <div>
        <div class="text-h6 q-mb-md">Amount Per Cubic</div>
        <div class="row q-col-gutter-md">
            <q-input
                v-model="$form.rate"
                dense
                outlined
                label="Rate"
                name="rate"
                class="col-3"
                @update:model-value="$emit('update:rate', $form.rate)"
            >
                <template v-slot:prepend>
                    <q-icon name="currency_ruble" />
                </template>
            </q-input>
            <q-select
                dense
                outlined
                label="Unit"
                name="unit"
                class="col-3"
                emit-value
                map-options
                v-model="$form.unit"
                :options="ui.volume.units"
                @input-value="$emit('update:rate', $form.unit)"
            >
                <template v-slot:prepend>
                    <q-icon name="water_drop" />
                </template>
            </q-select>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, defineEmits, defineProps, onMounted } from "vue";

const $emit = defineEmits(['input:rate', 'input:unit']);
const props = defineProps({
    rate: String,
    unit: String,
});
const ui = reactive({
    volume: {
        units: [
            { label: "Milliliter (mL or ml)", value:"ml"},
            { label: "Liter (L or l)", value:"l"},
            { label: "Cubic Meter (m³)", value:"m"},
            { label: "Cubic Inch (in³)", value:"in"},
            { label: "Cubic Foot (ft³)", value:"ft"},
            { label: "Gallon (gal)", value:"gal"},
        ]
    }
})
const $form = reactive({
    rate: null,
    unit: null,
})

watch(()=> props.rate, (n)=>{
    console.log( n )
    $form.rate = n
})

watch(()=> props.unit, (n)=>{
    console.log( n )
    $form.unit = n
})

// watch(()=> $form.rate, (n)=>{
//     $emit('update:rate', n)
// })

// watch(()=> $form.unit, (n)=>{
//     $emit('update:unit', n)
// })
</script>
