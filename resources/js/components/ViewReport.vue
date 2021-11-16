<template>
    <!-- Modal -->
    <div class="modal fade" id="view-report-modal" tabindex="-1" aria-labelledby="view-report-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-survivor-modal-label">Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-5">
                        <p>% of Clean Survivors</p>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" v-bind:style="'width:'+survivors.cleanPercentage+'%'" v-bind:aria-valuenow="survivors.cleanPercentage" aria-valuemin="0" aria-valuemax="100">{{ survivors.cleanPercentage }}%</div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="50%">Item</th>
                                    <th>Average Count per Survivor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, name) in items.averagesOfItems" v-bind:key="name">
                                    <td>{{ name }}</td>
                                    <td>{{ value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <p>Total points lost due to infected survivors: {{ items.totalInfectedPoints }} points</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            survivors: {
                infectedPercentage: 0,
                cleanPercentage: 0
            },
            items: {
                averagesOfItems: {},
                totalInfectedPoints: 0
            }
        };
    },

    created () {
        this.getReport();
    },

    methods: {
        getReport() {
            const url = 'api/v1/report';
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.survivors = res.data.survivors;
                    this.items = res.data.items;
                })
                .catch(err => console.log(err));
        }
    }
}
</script>