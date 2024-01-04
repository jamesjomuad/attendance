<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit.prevent.stop="onUpdate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <!-- Title -->
                            <q-input
                                dense
                                outlined
                                v-model="$form.title"
                                label="Title"
                                class="col-6"
                                :rules="[ val => val && val.length > 0 || 'Required!']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="campaign" />
                                </template>
                            </q-input>
                            <!-- Type -->
                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Type"
                                v-model="$form.type"
                                :color="ui.typeBg"
                                :options="['Emergency', 'Alert', 'Update', 'Reminder']"
                                :rules="[ val => val && val.length > 0 || 'Required!']"
                            >
                                <template v-slot:prepend>
                                    <q-icon :color="ui.typeBg" name="error" />
                                </template>
                            </q-select>
                            <!-- Start date -->
                            <q-input dense outlined v-model="$form.date_start" class="col-6" hint="Schedule posting">
                                <template v-slot:prepend>
                                    <q-icon name="event" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-date v-model="$form.date_start" mask="YYYY-MM-DD HH:mm">
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                        </q-date>
                                    </q-popup-proxy>
                                    </q-icon>
                                </template>

                                <template v-slot:append>
                                    <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-time v-model="$form.date_start" mask="YYYY-MM-DD HH:mm" format24h>
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                        </q-time>
                                    </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                            <!-- End Date -->
                            <q-input dense outlined v-model="$form.date_end" class="col-6" hint="Schedule hiding">
                                <template v-slot:prepend>
                                    <q-icon name="event" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-date v-model="$form.date_end" mask="YYYY-MM-DD HH:mm">
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                        </q-date>
                                    </q-popup-proxy>
                                    </q-icon>
                                </template>

                                <template v-slot:append>
                                    <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-time v-model="$form.date_end" mask="YYYY-MM-DD HH:mm" format24h>
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                        </q-time>
                                    </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                            <div class="col-12">
                                <q-editor
                                    v-model = "$editor.content"
                                    :dense = "$q.screen.lt.md"
                                    :toolbar = "$editor.toolbar"
                                    :fonts = "$editor.fonts"
                                    :paragraph-tag="'p'"
                                />
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions align="right">
                        <q-btn
                            label="Update"
                            color="primary"
                            type="submit"
                            :disable="ui.loading"
                            :loading="ui.loading"
                        />
                    </q-card-actions>
                    <q-inner-loading
                        :showing="ui.loading"
                        label="Please wait..."
                        label-style="font-size: 1.1em"
                    />
                </q-card>
            </div>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    typeBg: computed(()=>{
        let colors = {
            'Emergency': 'red',
            'Alert': 'warning',
            'Update': 'green-4',
            'Reminder': 'info'
        }
        return colors[$form.type]
    })
})
const $editor = reactive({
    content: '',
    toolbar: [
        [
            {
                label: $q.lang.editor.align,
                icon: $q.iconSet.editor.align,
                fixedLabel: true,
                list: 'only-icons',
                options: ['left', 'center', 'right', 'justify']
            },
            {
                label: $q.lang.editor.align,
                icon: $q.iconSet.editor.align,
                fixedLabel: true,
                options: ['left', 'center', 'right', 'justify']
            }
        ],
        ['bold', 'italic', 'strike', 'underline', 'subscript', 'superscript'],
        ['token', 'hr', 'link', 'custom_btn'],
        ['print', 'fullscreen'],
        [
            {
                label: $q.lang.editor.formatting,
                icon: $q.iconSet.editor.formatting,
                list: 'no-icons',
                options: [
                'p',
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'code'
                ]
            },
            {
                label: $q.lang.editor.fontSize,
                icon: $q.iconSet.editor.fontSize,
                fixedLabel: true,
                fixedIcon: true,
                list: 'no-icons',
                options: [
                'size-1',
                'size-2',
                'size-3',
                'size-4',
                'size-5',
                'size-6',
                'size-7'
                ]
            },
            {
                label: $q.lang.editor.defaultFont,
                icon: $q.iconSet.editor.font,
                fixedIcon: true,
                list: 'no-icons',
                options: [
                'default_font',
                'arial',
                'arial_black',
                'comic_sans',
                'courier_new',
                'impact',
                'lucida_grande',
                'times_new_roman',
                'verdana'
                ]
            },
            'removeFormat'
        ],
        ['quote', 'unordered', 'ordered', 'outdent', 'indent'],
        ['undo', 'redo'],
        ['viewsource']
    ],
    fonts: {
        arial: 'Arial',
        arial_black: 'Arial Black',
        comic_sans: 'Comic Sans MS',
        courier_new: 'Courier New',
        impact: 'Impact',
        lucida_grande: 'Lucida Grande',
        times_new_roman: 'Times New Roman',
        verdana: 'Verdana'
    }
})
const $form = reactive({
    title: "",
    type: "Update",
    date_start: null,
    date_end: null,
});


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/announcement/${$route.params.id}`)
    $form.title = data.title
    $form.type = data.type
    $form.date_start = data.date_start
    $form.date_end = data.date_end
    $editor.content = data.content
    ui.loading = false
})

async function onUpdate(){
    ui.loading = true
    try{
        const params = $form
        params.content = $editor.content
        const { data } = await axios.put(`/api/announcement/${$route.params.id}`, params)
        if( data.status )
        $q.notify({
            type: 'positive',
            message: `${data.message}`
        })
    }
    catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
}

</script>
