<template>
    <div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Hello Survivors!</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Welcome to the Zombie Survival Social Network</h6>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#add-survivor-modal">Add Survivor</button>
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#add-item-modal">Add Item</button>
                        <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#trade-items-modal">Trade Items</button>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#view-report-modal">View Report</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th width="10%">Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Flag Count</th>
                        <th>Items Count</th>
                        <th>Date Joined</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="survivor in survivors" v-bind:key="survivor.id">
                        <td>{{ survivor.id }}</td>
                        <td>{{ survivor.name }}</td>
                        <td>{{ survivor.age }}</td>
                        <td>{{ survivor.gender }}</td>
                        <td>{{ survivor.status }}</td>
                        <td>{{ survivor.flagCount }}</td>
                        <td>{{ survivor.itemsCount }}</td>
                        <td>{{ survivor.createdAt }}</td>
                        <td class="d-flex flex-row justify-content-around">
                            <button @click="getSurvivor(survivor.id)" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#view-survivor-modal">View</button>
                            <button @click="getSurvivor(survivor.id)" type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#update-survivor-modal">Update</button>
                            <button @click="getSurvivor(survivor.id)" type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#flag-survivor-modal">Flag</button>
                            <button @click="getSurvivor(survivor.id)" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-survivor-modal">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <nav>
                <ul class="pagination justify-content-center">
                    <li v-bind:class="[{disabled: !pagination.previousPageUrl}]" class="page-item">
                        <a @click="getSurvivors(pagination.previousPageUrl)" href="#" class="page-link">Previous</a>
                    </li>
                    <li class="page-item disabled"><a class="page-link text-dark" href="#">Page {{ pagination.currentPage }} of {{ pagination.lastPage }}</a></li>
                    <li v-bind:class="[{disabled: !pagination.nextPageUrl}]" class="page-item">
                        <a @click="getSurvivors(pagination.nextPageUrl)" href="#" class="page-link">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="view-survivor-modal" tabindex="-1" aria-labelledby="view-survivor-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="view-survivor-modal-label">Survivor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" v-model="survivor.name" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Age</label>
                            <input type="number" v-model="survivor.age" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Gender</label>
                            <input type="text" v-model="survivor.gender" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Location</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" v-model="survivor.lastLocation.lat" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" v-model="survivor.lastLocation.long" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Items</label>
                            <div class="row" v-for="survivorItem in survivorItems" v-bind:key="survivorItem.id">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" v-bind:value="survivorItem.itemName" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" v-bind:value="survivorItem.quantity" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="flag-survivor-modal" tabindex="-1" aria-labelledby="flag-survivor-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form @submit.prevent="flagSurvivor">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="flag-survivor-modal-label">Flag Survivor</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" v-if="message" role="alert"><small>{{ message }}</small></div>
                            <div class="alert alert-danger" v-for="(error, index) in errors" v-bind:key="index" role="alert"><small>{{ error }}</small></div>
                            <div class="form-group">
                                <label for="">Select Flag Origin</label>
                                <select v-model.number="flagSurvivorData.flagOriginId" class="custom-select">
                                    <option v-for="survivor in survivors" v-bind:key="survivor.id" v-bind:value="survivor.id">{{ survivor.name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Last Location</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" v-model="flagSurvivorData.lastLocation.lat" class="form-control" placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" v-model="flagSurvivorData.lastLocation.long" class="form-control" placeholder="Longitude">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Flag</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="update-survivor-modal" tabindex="-1" aria-labelledby="update-survivor-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form @submit.prevent="updateSurvivor">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="update-survivor-modal-label">Update Survivor</h5>
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
                                <input type="number" v-model.number="survivor.age" class="form-control">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="delete-survivor-modal" tabindex="-1" aria-labelledby="delete-survivor-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form @submit.prevent="deleteSurvivor">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="update-survivor-modal-label">Delete Survivor</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" v-if="message" role="alert"><small>{{ message }}</small></div>
                            <div class="alert alert-danger" v-for="(error, index) in errors" v-bind:key="index" role="alert"><small>{{ error }}</small></div>
                            <p>Are you sure you want to delete this survivor?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            survivors: [],
            survivor: {
                id: 0,
                name: '',
                age: 0,
                gender: '',
                lastLocation: {
                    lat: 0,
                    long: 0
                },
                status: '',
                flagCount: 0,
                itemsCount: 0,
                createdAt: '',
                updatedAt: ''
            },
            survivorItems: [],
            flagSurvivorData: {
                flagOriginId: 0,
                lastLocation: {
                    lat: 0,
                    long: 0
                }
            },
            pagination: {},
            message: '',
            errors: []
        };
    },

    created () {
        this.getSurvivors();
    },

    methods: {
        getSurvivors(url) {
            this.reset();
            let vm = this;
            url = url || 'api/v1/survivors';
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.survivors = res.data;
                    vm.setPagination(res.meta);
                })
                .catch(err => console.log(err));
        },
        getSurvivor(id) {
            this.reset();
            let url = `api/v1/survivors/${id}`;
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.survivor = res.data;
                })
                .catch(err => console.log(err));
            url = `api/v1/survivors/${id}/items`;
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.survivorItems = res.data
                })
                .catch(err => console.log(err));
        },
        flagSurvivor() {
            this.reset();
            let url = `api/v1/survivors/${this.survivor.id}/flag`;
            fetch(url, {
                method: 'put',
                body: JSON.stringify(this.flagSurvivorData),
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
        updateSurvivor() {
            this.reset();
            let url = `api/v1/survivors/${this.survivor.id}`;
            fetch(url, {
                method: 'put',
                body: JSON.stringify(this.survivor),
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
                        this.message = 'Survivor has been successfully updated'
                })
                .catch(err => console.log(err));
        },
        deleteSurvivor() {
            this.reset();
            let url = `api/v1/survivors/${this.survivor.id}`;
            fetch(url, {
                method: 'delete',
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
        setPagination(meta) {
            this.pagination = {
                currentPage: meta.currentPage,
                lastPage: meta.lastPage,
                previousPageUrl: meta.previousPageUrl,
                nextPageUrl: meta.nextPageUrl
            };
        },
        reset() {
            this.message = '';
            this.errors = [];
            this.allSurvivors = [];
        }
    },
}
</script>