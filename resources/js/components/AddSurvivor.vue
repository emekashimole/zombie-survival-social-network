<template>
    <!-- Modal -->
    <div class="modal fade" id="add-survivor-modal" tabindex="-1" aria-labelledby="add-survivor-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form @submit.prevent="addSurvivor">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add-survivor-modal-label">Add New Survivor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" v-if="message" role="alert"><small>{{ message }}</small></div>
                        <div class="alert alert-danger" v-for="(error, index) in errors" v-bind:key="index" role="alert"><small>{{ error }}</small></div>
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" v-model="survivor.name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Age</label>
                            <input type="number" v-model="survivor.age" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Gender</label>
                            <select class="custom-select" v-model="survivor.gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Location</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" v-model="survivor.lastLocation.lat" class="form-control" placeholder="Latitude">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" v-model="survivor.lastLocation.long" class="form-control" placeholder="Longitude">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Items</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select v-model.number="survivorItem.id" class="custom-select">
                                        <option v-for="item in items" v-bind:key="item.id" v-bind:value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" v-model.number="survivorItem.quantity" placeholder="Q" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
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
            items: [],
            survivor: {
                name: '',
                age: 0,
                gender: '',
                lastLocation: {
                    lat: 0,
                    long: 0
                },
                items: []
            },
            survivorItem: {
                id: 0,
                quantity: 0
            },
            message: '',
            errors: []
        };
    },

    created () {
        this.getItems();
    },

    methods: {
        getItems() {
            const url = 'api/v1/items';
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.items = res.data;
                })
                .catch(err => console.log(err));
        },
        addSurvivor() {
            const url = 'api/v1/survivors';
            this.addSurvivorItem();
            fetch(url, {
                method: 'post',
                body: JSON.stringify(this.survivor),
                headers: {
                    'content-type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success === false)
                        this.errors = res.errors
                    else
                        this.message = 'Survivor has been successfully added'
                })
                .catch(err => console.log(err));
        },
        addSurvivorItem() {
            this.survivor.items.push(this.survivorItem);
        }
    }
}
</script>