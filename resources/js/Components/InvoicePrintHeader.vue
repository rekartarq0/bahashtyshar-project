<template>
    <div class="hidden print:block w-full font-droidkufi">
        <div class="mx-4 border-b-4 border-gray-300 pb-2 mb-2">
            <!-- Top section: Logo & Business Info -->
            <div class="flex mx-10 justify-between items-start">
                <!-- Business Info -->
                <div class="space-y-1">
                    <h1 class="text-3xl font-extrabold text-gray-800">{{ settings.name }}</h1>
                    <p class="text-base text-gray-700 font-medium">{{ settings.title1 }}</p>
                    <p v-if="settings.title2" class="text-base text-gray-600">{{ settings.title2 }}</p>
                </div>

                <!-- Logo -->
                <div class="text-center">
                    <img :src="`/storage/${settings.image}`" alt="Logo" class="w-28 h-28 object-contain rounded" />
                </div>
            </div>
            <!-- Contact Info: Phones -->
            <div class="mt-0 mx-10 flex justify-evenly space-x-8 text-sm text-gray-700 rtl:space-x-reverse">
                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                    <span class="font-semibold">📞 تەلەفۆن ١:</span>
                    <span>{{ settings.phone1 }}</span>
                </div>
                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                    <span class="font-semibold">📞 تەلەفۆن ٢:</span>
                    <span>{{ settings.phone2 }}</span>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    settings: Object
});
</script>
  
