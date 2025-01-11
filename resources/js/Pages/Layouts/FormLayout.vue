<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import {usePage,router} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import Loading from "@/Components/Loading.vue";


const page = usePage()

const {url,item,method,destroy} = page.props as {
    url: string,
    method: string
    item?: Record<string, any>,
    destroy?: boolean
}
const formNode = ref(null)

const errorMessage =  ref<string>()
const showError = ref<boolean>(false)
const loading = ref<boolean>(false)
const success = ref<boolean>(false)

const backurl = ref<string>('')

onMounted(() => {
    if(!item) return
    let formnode = formNode.value;
    for(const [key,val] of Object.entries(item)){
        const input = formnode.querySelector(`[name="${key}"]`)
        if(!input) continue
        input.value = val
        if(destroy)
            input.setAttribute('readonly','')
    }


})

async function submit(){
    try {
        loading.value = true
        const form = new FormData(formNode.value)
        form.set('_method',method)
        const {data} = await axios.post(url,form)
        backurl.value = data.url
        success.value = true
    } catch (e){
        if(!e.response) return

        success.value = false
        loading.value = false
        const {
            data:  {message}
        } = e.response

        errorMessage.value = message
        showError.value = true
    }
}

function done(){
    console.log(backurl.value)
    router.visit(backurl.value)
}

</script>

<template>
    <app-layout>
        <loading v-model:show="loading" :success="success" @continue="done" />
        <div class="container mx-auto mt-4">
            <Transition appear>
                <div v-if="showError" class="px-4 py-2 bg-red-500 text-white rounded my-4">
                    <h5 class="text-xl font-semibold">
                        <i class="bi bi-bug"></i>
                        Error
                    </h5>
                    {{ errorMessage }}
                </div>
            </Transition>
            <div v-if="destroy" class="px-4 py-2 rounded font-semibold bg-orange-500 text-xl my-4 text-white">
                <i class="bi bi-exclamation-triangle"></i>
                ¿Seguro que desea eliminar el elemento?
            </div>
            <form ref="formNode" @submit.prevent="submit">
                <div class="p-4 rounded bg-white shadow">
                    <slot></slot>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn">
                        Procesar
                    </button>
                </div>
            </form>

        </div>
    </app-layout>
</template>

<style scoped>

</style>
