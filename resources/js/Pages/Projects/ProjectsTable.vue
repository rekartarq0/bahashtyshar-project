<template>
    <div className="rounded-sm border border-stroke bg-white px-5 pt-6 m-5 pb-2.5 
    shadow-default dark:border-strokedark dark:bg-boxdark-2 sm:px-7.5 xl:pb-1">
        <div className="flex flex-col justify-between items-center w-full mb-4">
            <div className="w-full overflow-x-auto">

                <table className="w-full table-auto">
                    <thead className="font-droidkufi">
                        <tr className="bg-gray-100 text-right dark:bg-meta-4">
                            <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                                #</th>
                            <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black min-w-[400px]">
                                ناوی پڕۆژە</th>
                            <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                                 </th>
                            <th className="border border-[#eee] dark:border-strokedark py-2 px-4 text-xs text-black">
                                حاڵەکاتی پڕۆژە</th>
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-black">
                                دروستکراوە لە</th>
                            <th
                                className="border border-[#eee] dark:border-strokedark py-2 px-4 text-sm sm:text-xs text-blacky">
                                دەسکاری </th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in data.data" :key="i">
                            <td
                                className="border bg-gray-100 border-[#eee] py-2 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ i + 1 + (data.current_page - 1) * data.per_page }}
                            </td>
                            <td
                                className="border font-droidkufi border-[#eee] py-1 px-4 dark:border-strokedark sm:text-xs text-primary font-bold text-base">
                                {{ item.name }}
                            </td>
                                <td
                                className="border font-droidkufi border-[#eee] py-1 mx-auto  dark:border-strokedark text-sm sm:text-xs text-black">
                               <el-button plain  @click="openForm(item.id)">
                                کردنەوەی فۆڕمی حاڵەکاتی پڕۆژە
                               </el-button>
                               
                            </td>
                            <td
                                class="border border-[#eee] py-1 flex px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                <div v-for="(stage, index) in item?.stages" :key="stage.id" class="flex items-center">
                                    <el-button class="no-click" plain :type="stageType(stage.stage)">
                                        {{ stage.stage }}
                                    </el-button>
                                    <span v-if="index !=3&& item.stages.length > 1" class="mx-1">&#8630;</span>
                                </div>
                            </td>

                            <td
                                className="border border-[#eee] py-1 px-4 dark:border-strokedark text-sm sm:text-xs text-black">
                                {{ new Date(item.created_at).toLocaleString() }}
                                <span className="mx-1">by</span>
                                {{ item?.user?.name }}
                            </td>

                            <td
                                className="border border-[#eee] py-1 px-4  dark:border-strokedark text-sm sm:text-xs text-black">
                                <el-button v-if="can('delete projects')" type="danger" plain :icon="Delete"
                                    @click="destroy(item.id)" circle class="mr-2" />
                                <el-button v-if="can('edit projects')" type="primary" plain :icon="Edit"
                                    @click="edit(item.id)" circle class="mr-2" />
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <div class="mt-4 flex justify-start items-end">
                <el-pagination background layout="prev, pager, next" :total="data.total"
                    :current-page="data.current_page" :page-size="data.per_page" @current-change="handlePageChange" />
                <el-select v-model="perPage" placeholder="perpage" style="width: 100px" @change="fetchPage()">
                    <el-option v-for="size in [10, 25, 50, 100]" :key="size" :label="size" :value="size" />
                </el-select>
            </div>

        </div>
    </div>
</template>




<script setup>
import { defineProps, ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import {
    Delete,
    Edit,
} from '@element-plus/icons-vue';

const props = defineProps({
    data: Object,
    destroy: Function,
    edit: Function,
    searchQuery: String,
    search: Function,
        openForm: Function, 

});


const perPage = ref(25); // default value


const fetchPage = debounce((page) => {
    router.get('/projects', {
        q: props.searchQuery,
        page,
        perPage: perPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);


const handlePageChange = (page) => {
    fetchPage(page); // Fetch data for the new page
};
// Watch for changes in searchQuery
watch(
    () => props.searchQuery,
    () => {
        fetchPage(props.data.current_page); // Keep the current page while searching
    },
    { immediate: false } // Run the watch immediately to handle initial state
);

const stageType = (stageName) => {
    const colors = {
        Planning: 'danger', // blue
        Creative: 'warning', // green
        Design: 'primary',   // orange
        Sale: 'success',      // red
    };
    return colors[stageName] || 'info'; // fallback
};
</script>

<style>

.no-click {
    cursor: default !important;
}
</style>