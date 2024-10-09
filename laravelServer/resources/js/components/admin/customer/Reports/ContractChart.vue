<template>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" v-text="`${ancillary.title}`"></div>
            <doughnut-chart :chart-data="chart_data" :chart-options="chart_options"></doughnut-chart>
        </div>
    </div>
</template>

<script>
    import DoughnutChart from './DoughnutChart';

    export default {
        name: 'ContractChart',
        components: {
            DoughnutChart
        },
        props: ['ancillary'],
        data() {
            return {
                chart_options: [],
                chart_data: [],
                labels: [],
                bgColors: [],
                values: [],
                totalComplete: 0
            }
        },
        methods: {
            setChartConfiguration(){
                /*this.chart_options = {
                        animation: {
                            delay: 12000,
                            duration: 2000
                        },

                        interaction: {
                            mode: 'index'
                        },
                        label: '',
                        responsive: true,
                        maintainAspectRatio: false,
                        spanGaps: false,
                        elements: {
                            line: {
                                tension: 0.000001
                            }
                        },
                        fill: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                    display: false
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontFamily: "Tahoma",
                                    autoSkip: false,
                                    maxRotation: 30,
                                    minRotation: 30,
                                }
                            }]
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                            titleFontFamily: "Tahoma",
                            callbacks: {
                                title: function (tooltipItem) {
                                    let itemIndex = tooltipItem[0].index;
                                    let itemLabel = itemIndex;
                                    if (itemIndex == 0) {
                                        itemLabel = 'پیشنهادی';
                                    }
                                    if (itemIndex == 1) {
                                        itemLabel = 'حسابداری';
                                    }
                                    if (itemIndex == 2) {
                                        itemLabel = 'تاییدیه سازمان';
                                    }
                                    if (itemIndex == 3) {
                                        itemLabel = 'مجمع';
                                    }
                                    if (itemIndex == 4) {
                                        itemLabel = 'جلسه'
                                    }
                                    if (itemIndex == 5) {
                                        itemLabel = 'بازگشایی';
                                    }
                                    return itemLabel;
                                },
                                label: function (tooltipItem) {
                                    return tooltipItem.yLabel;
                                }
                                /!*title: function (tooltipItem) {
                                    $titles = [
                                        'ق.پیشنهادی',
                                        'ق.پیشنهادی',
                                        'ق.پیشنهادی',
                                        'ق.پیشنهادی',
                                        'ق.پیشنهادی',
                                        'ق.پیشنهادی'
                                    ];
                                    return $titles[tooltipItem[0].index];

                                }*!/
                            },
                        },
                        titleFontSize: "12"
                    }*/

                 this.chart_options = {
                     responsive: true,
                     legend: {
                         display: false
                     },
                     tooltips: {
                         titleAlign: 'right',
                         rtl: true,
                         titleFontFamily: '"yekan", Tahoma, Geneva, sans-serif',
                     },
                     title: {
                         display: true,
                         text: `درصد پیشرفت % ${this.totalComplete}`,
                         fontFamily: '"yekan", Tahoma, Geneva, sans-serif',
                         position: 'bottom',
                         padding: 0
                     },
                     animation: {
                         animateScale: false,
                         animateRotate: true
                     },
                     cutoutPercentage: 60,
                     rotation: Math.PI,
                     circumference: Math.PI
                 };
            },
            setChartData() {
                this.ancillary.base_progress.forEach((base, index) => {
                        if (typeof base.description != 'undefined') {
                            let titlePivot = base.description;
                            if (base.pivot.status === 'complete') {
                                this.labels.unshift(titlePivot);
                                this.values.unshift([base.percentage]);
                                this.totalComplete += base.percentage;
                                this.bgColors.unshift('rgba(202,19,100,1)');
                            } else {
                                this.labels.push(titlePivot);
                                this.values.push([base.percentage]);
                                this.bgColors.push('rgba(0, 0, 0, 0.06)');
                            }
                        } else {
                            this.labels.unshift('عدم تعریف');
                            this.values.unshift('100');
                            this.bgColors.unshift('rgba(0,0,0,0.06)');
                        }
                    });
                this.chart_data = {
                    labels: this.labels,
                    datasets: [{
                        label: '',
                        data: this.values,
                        backgroundColor: this.bgColors,
                    }],
                }
            }
        },
        /*async mounted() {
            await this.setChartData();
        },*/
        created() {
            this.setChartData();
            this.setChartConfiguration();
        }

    }
</script>

<style>

</style>
