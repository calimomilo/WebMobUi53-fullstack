<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    poll: { type: Object, default: null },
});

// additional verification in case user managed to access without being allowed
const canSeeResults = !!props.poll.options[0].votes_count;
const chartHeight = computed(() => props.poll.options.length * 48 + 50)
console.log(chartHeight.value)

const chartData = computed(() => ({
    labels: props.poll.options.map(o => o.label),
    datasets: [{
        data: props.poll.options.map(o => o.votes_count),
        backgroundColor: '#e60076',
        borderRadius: 6,
    }]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
        legend: { display: false },
    },
    scales: {
        x: {
            ticks: { precision: 0 },
        },
    },
}


  

</script>

<template>
    <h2 class="text-lg mb-1 pt-4 mt-8 border-t-2 border-gray-200">{{ props.poll.question }} : résultats</h2>
    <div :class="`w-full p-4 h-[${chartHeight}px]`">
        <Bar :data="chartData" :options="chartOptions" />
    </div>

    
</template>
