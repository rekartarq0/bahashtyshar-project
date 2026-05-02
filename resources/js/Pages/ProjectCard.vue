<template>
  <div
    @click="$emit('show', item.project_id)"
    :class="[
      'relative flex items-center justify-center rounded-md text-white text-[10px] md:text-[12px] font-semibold text-center leading-tight shadow-md cursor-pointer transition-transform duration-200 hover:scale-105 group',
      item.backed_from_pricing ? 'bg-black' : getDurationColor(item.start_time, item.end_time),
      index === 0 ? 'w-36 h-24 scale-105 shadow-xl ring-2 ring-yellow-400 animate-pulse' : 'w-36 h-20'
    ]"
  >
    {{ item.project.name }}

    <!-- Note Pin -->
    <div v-if="item.note" class="absolute bottom-1 -left-28 group">
      <PinIcon class="w-4 h-4 text-yellow-400 cursor-pointer" />
      <div
        class="absolute z-10 hidden group-hover:block -top-20 left-1/2 -translate-x-1/2 w-48 bg-white text-black text-xs rounded-lg shadow-lg p-2"
      >
        <p class="font-bold">Note:</p>
        <p>{{ item.note }}</p>
      </div>
    </div>
    <div v-if="item.note" class="absolute top-1 left-1">
      <i class="fa-solid fa-pen text-primary"></i>
    </div>

    <!-- Status Icon -->
    <div class="absolute top-1 right-1">
      <Check v-if="getProjectStatus(item) === 'success'" class="w-4 h-4 text-green-300" />
      <Clock v-else-if="getProjectStatus(item) === 'inProcess'" class="w-4 h-4 text-yellow-300" />
      <Minus v-else class="w-4 h-4 text-gray-300" />
    </div>

    <!-- Hover Actions -->
    <div class="absolute inset-0 flex justify-between items-center px-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
      <Edit
        v-if="can('edit projects')"
        @click.stop="$emit('edit', item.project_id)"
        class="w-5 h-5 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black"
      />

      <!-- Back to Design -->
      <div
        v-if="item.stage === 'Pricing' && can('edit projects')"
        class="absolute bottom-1 left-1/2 -translate-x-1/2"
      >
        <ArrowRightBold
          @click.stop="$emit('back-to-design', item)"
          class="w-6 h-6 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black"
        />
      </div>

      <!-- Next Stage -->
      <ArrowLeftBold
        v-if="!isLastStage(item.stage) && !item.end_time && can('edit projects')"
        @click.stop="$emit('add-next-stage', item)"
        class="w-5 h-5 text-white bg-black/40 rounded-full p-1 cursor-pointer hover:bg-black"
      />

      <!-- Quit Stage -->
      <Finished
        v-else-if="isLastStage(item.stage) && !item.end_time"
        @click.stop="$emit('quit-stage', item)"
        class="w-5 h-5 text-white bg-red-500 rounded-full p-1 cursor-pointer hover:bg-red-600"
      />

      <!-- Archive Project -->
      <TakeawayBox
        v-else-if="item.end_time && !item.project.is_archived"
        @click.stop="$emit('archive-project', item)"
        class="w-5 h-5 text-white bg-blue-500 rounded-full p-1 cursor-pointer hover:bg-blue-600"
      />

      <!-- Completed & Archived -->
      <Check v-else class="w-5 h-5 text-green-500" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  item: Object,
  index: Number
});

// Functions you need

function getDurationColor(start, end) {
  if (!start) return "bg-gradient-to-r from-gray-300 to-gray-500";

  const diffDays = getTotalDays(start, end);

  if (diffDays <= 2) {
    return "bg-gradient-to-br from-green-400 to-green-600";
  } else if (diffDays <= 4) {
    return "bg-gradient-to-br from-yellow-400 to-orange-500";
  } else {
    return "bg-gradient-to-br from-red-500 to-red-700";
  }
}
function getProjectStatus(item) {
  if (!item.start_time) return "pending"; 
  return item.end_time ? "success" : "inProcess";
}

// ---- Helpers ----
function getTotalDays(start, end = null) {
  if (!start) return 0;
  const startDate = new Date(start);
  const endDate = end ? new Date(end) : new Date();
  const diffMs = endDate - startDate;
  return diffMs / (1000 * 60 * 60 * 24); 
}

const can = permission => true; // Replace with your permission logic
const isLastStage = stage => stage === 'Completed'; // Replace with your stage logic
</script>
