<template>
    <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
        <div className="flex flex-col justify-between items-center w-full mb-4">
            <div className="w-full overflow-x-auto">

                <table className="w-full table-auto">
                    <thead className="font-droidkufi">
                        <tr className="bg-gray-100 text-right dark:bg-meta-4">
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[20px]">
                                #</th>
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[250px]">
                                ناو</th>
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black min-w-[250px]">
                                دروستکراوە لە</th>
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black min-w-[120px]">
                                دەسکاری </th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in data" :key="i">
                            <td
                                className="border bg-gray-100 border-[#eee] py-2 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ i + 1 }}
                            </td>
                            <td
                                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ item.name }}
                            </td>
                            <td
                                className="border border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ new Date(item.created_at).toLocaleString() }}
                                <span className="mx-1">by</span>
                                {{ item?.user?.name }}
                            </td>

                            <td
                                className="border border-[#eee] py-1 px-4  dark:border-strokedark text-sm sm:text-xs text-black">
                                <el-button v-if="can('delete category')" type="danger" plain :icon="Delete"
                                    @click="destroy(item.id)" circle class="mr-2" />
                                <el-button v-if="can('edit category')" type="primary" plain :icon="Edit"
                                    @click="edit(item.id)" circle class="mr-2" />
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<script setup>
import { defineProps } from 'vue';

import {
    Delete,
    Edit,

} from '@element-plus/icons-vue'
import Can from '@/Components/Can.vue';
defineProps({
    data: Array,
    destroy: Function,
    edit: Function
});
</script>