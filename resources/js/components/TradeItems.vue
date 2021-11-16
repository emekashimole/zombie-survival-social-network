<template>
    <!-- Modal -->
    <div class="modal fade" id="trade-items-modal" tabindex="-1" aria-labelledby="trade-items-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form @submit.prevent="tradeItems">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add-survivor-modal-label">Trade Items</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" v-if="message" role="alert"><small>{{ message }}</small></div>
                        <div class="alert alert-danger" v-for="(error, index) in errors" v-bind:key="index" role="alert"><small>{{ error }}</small></div>
                        <div class="form-group">
                            <label for="">Select Survivor A</label>
                            <select v-model.number="tradeSurvivorA.id" class="custom-select">
                                <option v-for="survivor in survivors" v-bind:key="survivor.id" v-bind:value="survivor.id">{{ survivor.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Items</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select v-model.number="tradeSurvivorAItem.id" class="custom-select">
                                        <option v-for="item in items" v-bind:key="item.id" v-bind:value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" v-model.number="tradeSurvivorAItem.quantity" placeholder="Q" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Survivor B</label>
                            <select v-model.number="tradeSurvivorB.id" class="custom-select">
                                <option v-for="survivor in survivors" v-bind:key="survivor.id" v-bind:value="survivor.id">{{ survivor.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Items</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select v-model.number="tradeSurvivorBItem.id" class="custom-select">
                                        <option v-for="item in items" v-bind:key="item.id" v-bind:value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" v-model.number="tradeSurvivorBItem.quantity" placeholder="Q" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Trade</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            survivors: [],
            items: [],
            tradeSurvivorA: {
                id: 0,
                items: []
            },
            tradeSurvivorB: {
                id: 0,
                items: []
            },
            tradeSurvivorAItem: {
                id: 0,
                quantity: 0
            },
            tradeSurvivorBItem: {
                id: 0,
                quantity: 0
            },
            tradeData: {
                survivors: []
            },
            message: '',
            errors: []
        };
    },

    created () {
        this.getSurvivors();
        this.getItems();
    },

    methods: {
        getSurvivors() {
            const url = 'api/v1/survivors';
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.survivors = res.data;
                })
                .catch(err => console.log(err));
        },
        getItems() {
            const url = 'api/v1/items';
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.items = res.data;
                })
                .catch(err => console.log(err));
        },
        tradeItems() {
            this.reset();
            this.setTradeData();
            const url = 'api/v1/items/trade';
            fetch(url, {
                method: 'post',
                body: JSON.stringify(this.tradeData),
                headers: {
                    'content-type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success === false)
                        if (res.errors)
                            this.errors = res.errors;
                        else
                            this.errors.push(res.message)
                    else
                        this.message = res.message
                })
                .catch(err => console.log(err));
        },
        setTradeData() {
            this.tradeSurvivorA.items.push(this.tradeSurvivorAItem);
            this.tradeSurvivorB.items.push(this.tradeSurvivorBItem);
            this.tradeData.survivors.push(this.tradeSurvivorA, this.tradeSurvivorB);
        },
        reset() {
            this.tradeSurvivorA.items = [];
            this.tradeSurvivorB.items = [];
            this.tradeData.survivors = [];
            this.message = '';
            this.errors = [];
        }
    }
}
</script>