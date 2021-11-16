<template>
    <!-- Modal -->
    <div class="modal fade" id="add-item-modal" tabindex="-1" aria-labelledby="add-item-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form @submit.prevent="addItem">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add-item-modal-label">Add New Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" v-if="message" role="alert">{{ message }}</div>
                        <div class="alert alert-danger" v-for="(error, index) in errors" v-bind:key="index" role="alert"><small>{{ error }}</small></div>
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" v-model="item.name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Points</label>
                            <input type="number" v-model="item.points" class="form-control">
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
            item: {
                name: '',
                points: 0,
                gender: '',
                lastLocation: {
                    lat: 0,
                    long: 0
                },
                items: []
            },
            message: '',
            errors: []
        };
    },

    methods: {
        addItem() {
            const url = 'api/v1/items';
            fetch(url, {
                method: 'post',
                body: JSON.stringify(this.item),
                headers: {
                    'content-type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success === false)
                        this.errors = res.errors
                    else
                        this.message = 'Item has been successfully added'
                })
                .catch(err => console.log(err));
        }
    }
}
</script>